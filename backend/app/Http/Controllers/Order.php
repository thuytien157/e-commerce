<?php

namespace App\Http\Controllers;

use App\Events\OrderUpdated;
use App\Models\Order as ModelsOrder;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

SupportCarbon::setLocale('vi');
date_default_timezone_set('Asia/Ho_Chi_Minh');
class Order extends Controller
{
    function getGHNServices(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Token' => env('GHN_TOKEN'),
            ])->get('https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/available-services', [
                'shop_id' => (int) env('GHN_SHOP_ID'),
                'from_district' => (int) env('GHN_FROM_DISTRICT'),
                'to_district' => (int) $request->to_district_id,
            ]);

            if ($response->successful()) {
                return response()->json($response->json()['data']);
            } else {
                Log::error('Lỗi khi lấy dịch vụ GHN: ' . $response->body());
                return response()->json(['error' => 'Không thể lấy dịch vụ vận chuyển.'], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Lỗi ngoại lệ khi gọi API GHN: ' . $e->getMessage());
            return response()->json(['error' => 'Đã xảy ra lỗi server.'], 500);
        }
    }

    function createOrder(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'guest_name'  => 'required|string',
                'payment_method'  => 'required|string',
                'guest_phone' => ['required', 'regex:/^0[0-9]{9}$/'],
                'provinces' => 'required',
                'districts' => 'required',
                'wards' => 'required',
                'address' => 'required',
                'guest_address' => 'required',
                'total_amount' => 'required|numeric|min:0',
                'shipping_money' => 'required|numeric|min:0',
                'cartItems' => 'required|array',
                'cartItems.*.variantId' => 'required|integer|exists:variants,id',
                'cartItems.*.quantity' => 'required|integer|min:1',
                'cartItems.*.price' => 'required|numeric|min:0',
            ],
            [
                'guest_name.required'  => 'Vui lòng nhập tên.',
                'payment_method.required'  => 'Vui lòng chọn phương thức thanh toán.',
                'guest_phone.required' => 'Số điện thoại là bắt buộc.',
                'guest_phone.regex'    => 'Số điện thoại phải có 10 chữ số và bắt đầu bằng số 0.',
                'ward.required' => 'Vui lòng nhập phường xã',
                'provinces.required' => 'Vui lòng nhập tỉnh thành phố',
                'districts.required' => 'Vui lòng nhập quận huyện',
                'address.required' => 'Vui lòng nhập địa chỉ',
                'guest_address.required' => 'thiếu địa chỉ',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $order = new ModelsOrder();
            $order->customer_id = $request->customer_id ?? null;
            $order->shipping_address_id = $request->shipping_address ?? null;
            $order->guest_name = $request->guest_name;
            $order->guest_phone = $request->guest_phone;
            $order->guest_address = $request->guest_address;
            $order->total_amount = $request->total_amount;
            $order->shipping_money = $request->shipping_money;
            $order->order_date = SupportCarbon::now();
            if ($request->payment_method == 'VNPAY') {
                $order->payment_method = 'VNPAY';
                $order->status_payments = 'Chưa thanh toán';
            } else {
                $order->payment_method = $request->payment_method;
                $order->status_payments = 'Chưa thanh toán';
            }
            $order->save();

            $orderItemsData = [];
            foreach ($request->input('cartItems') as $item) {
                $unitPrice = $item['price'];
                $quantity = $item['quantity'];

                $orderItemsData[] = [
                    'order_id' => $order->id,
                    'variant_id' => $item['variantId'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $unitPrice * $quantity
                ];
            }

            OrderItem::insert($orderItemsData);


            DB::commit();
            if ($request->payment_method == 'VNPAY') {
                return $this->generatePaymentUrl($request, $order);
            }
            return response()->json(['message' => 'Thêm đơn thành công', 'order_id' => $order->id], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function generatePaymentUrl(Request $request, $order)
    {
        $vnp_TxnRef = $order->id;
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');
        $vnp_TmnCode = env('VNPAY_TMN_CODE');
        $vnp_Url = env('VNPAY_URL');

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => ($order->total_amount) * 100,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => SupportCarbon::now()->format('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $request->ip(),
            "vnp_Locale" => 'vn',
            "vnp_OrderInfo" => 'Thanh toán lại cho đơn hàng #' . $order->id,
            "vnp_OrderType" => 'billpayment',
            "vnp_ReturnUrl" => env('VNPAY_RETURN_URL'),
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_BankCode" => 'ncb',
        ];

        ksort($inputData);
        $query = http_build_query($inputData);
        $secureHash = hash_hmac('sha512', $query, $vnp_HashSecret);
        $vnp_Url = $vnp_Url . '?' . $query . "&vnp_SecureHash=" . $secureHash;

        return response()->json(['return_url' => $vnp_Url]);
    }


    function paymentOrderReturn(Request $request)
    {
        $orderId = $request->vnp_TxnRef;
        return redirect(env('FRONTEND_URL') . '/payment-result/' . $orderId);
    }

    function handleVnpayIpn(Request $request)
    {
        $inputData = $request->all();
        Log::info('VNPAY IPN:', $request->all());
        // 1. Kiểm tra chữ ký an toàn (đảm bảo request từ VNPAY)
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $hashData = http_build_query($inputData);
        $secureHash = hash_hmac('sha512', $hashData, env('VNPAY_HASH_SECRET'));

        if ($secureHash !== $vnp_SecureHash) {
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
        }

        $order = ModelsOrder::find($request->vnp_TxnRef);

        if (!$order) {
            return response()->json(['RspCode' => '01', 'Message' => 'Order not found']);
        }

        // 2. Kiểm tra và cập nhật trạng thái đơn hàng
        if ($order->status_payments == 'Đã thanh toán') {
            return response()->json(['RspCode' => '02', 'Message' => 'Order already confirmed']);
        }

        if ($request->vnp_ResponseCode == '00' && $request->vnp_TransactionStatus == '00') {
            $order->status_payments = 'Đã thanh toán';
        } else {
            $order->status_payments = 'Chưa thanh toán';
        }

        $order->save();
        return response()->json(['RspCode' => '00', 'Message' => 'Success']);
    }

    function repayOrder(Request $request, $orderId)
    {
        $order = ModelsOrder::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Đơn hàng không tồn tại.'], 404);
        }

        if ($order->status_payments === 'Đã thanh toán') {
            return response()->json(['message' => 'Đơn hàng này đã được thanh toán thành công.'], 400);
        }
        return $this->generatePaymentUrl($request, $order);
    }


    function getOrderById(String $id)
    {
        $order = ModelsOrder::with('orderItems.variant.attributes', 'orderItems.variant.product')->find($id);

        if (!$order) {
            return response()->json([
                'mess' => 'Không tìm thấy đơn hàng1'
            ], 404);
        }

        return response()->json([
            'order' => $order
        ]);
    }

    function cancelOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cancellation_reason' => 'required'
        ], [
            'cancellation_reason.required' => 'Vui lòng nhập lý do hủy đơn'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        $order = ModelsOrder::find($request->id);
        if (!$order) {
            return response()->json([
                'mess' => 'Không tìm thấy đơn hàng'
            ], 404);
        }

        $order->status = 'Thất bại';
        $order->cancellation_reason = $request->cancellation_reason;
        if ($order->save()) {
            event(new OrderUpdated($order->id));
            return response()->json([
                'mess' => 'Huỷ đơn thành công'
            ], 200);
        }
    }

    function updateAddresslOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required',
            'provinces' => 'required',
            'districts' => 'required',
            'wards' => 'required',
            'address' => 'required',
        ], [
            'address.required' => 'Vui lòng địa chỉ',
            'provinces.required' => 'Vui lòng tỉnh thành phố',
            'districts.required' => 'Vui lòng quận huyện',
            'wards.required' => 'Vui lòng phường xã'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        $order = ModelsOrder::find($request->id);
        if (!$order) {
            return response()->json([
                'mess' => 'Không tìm thấy đơn hàng'
            ], 404);
        }

        $order->guest_address = $request->address;
        if ($order->save()) {
            event(new OrderUpdated($order->id));
            return response()->json([
                'mess' => 'Cập nhật thành công'
            ], 200);
        }
    }
}
