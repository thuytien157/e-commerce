<?php

namespace App\Http\Controllers;

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

    public function createOrder(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'guest_name'  => 'required|string',
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

            return response()->json(['message' => 'Order created successfully!', 'order_id' => $order->id], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create order.', 'error' => $e->getMessage()], 500);
        }
    }
}
