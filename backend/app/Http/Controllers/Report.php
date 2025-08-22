<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Product as ControllersProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

Carbon::setLocale('vi');
date_default_timezone_set('Asia/Ho_Chi_Minh');
class Report extends Controller
{
    function revenue(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        if (!$from || !$to) {
            $revenue = DB::table('orders')
                ->select(DB::raw('YEAR(order_date) as year'), DB::raw('SUM(total_amount) as revenue'))
                ->where('status', 'Hoàn thành')
                ->groupBy('year')
                ->get()
                ->map(function ($item) {
                    return ['label' => $item->year, 'revenue' => $item->revenue];
                });
            return response()->json(['revenue' => $revenue], 200);
        }

        $start = Carbon::parse($from);
        $end = Carbon::parse($to);
        $diffDays = $start->diffInDays($end);

        if ($diffDays == 0) {
            $revenue = DB::table('orders')
                ->select(DB::raw('DAY(order_date) as day'), DB::raw('SUM(total_amount) as revenue'))
                ->where('status', 'Hoàn thành')
                ->groupBy('day')
                ->get()
                ->map(function ($item) {
                    return ['label' => $item->day, 'revenue' => $item->revenue];
                });
            return response()->json(['revenue' => $revenue], 200);
        }

        $revenue = DB::table('orders')
            ->select(
                DB::raw('YEAR(order_date) as year'),
                DB::raw('MONTH(order_date) as month'),
                DB::raw('DAY(order_date) as day'),
                DB::raw('WEEK(order_date,1) as week'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->where('status', 'Hoàn thành')
            ->whereBetween('order_date', [$from, $to])
            ->groupBy('year', 'month', 'day', 'week')
            ->get()
            ->map(function ($item) use ($diffDays) {
                $label = '';
                if ($diffDays <= 1 || $diffDays <= 7) {
                    $label = $item->day . '/' . $item->month;
                } elseif ($diffDays <= 31) {
                    $label = 'Tuần ' . $item->week;
                } elseif ($diffDays <= 365) {
                    $label = $item->month . '/' . $item->year;
                } else {
                    $label = $item->year;
                }
                return ['label' => $label, 'revenue' => $item->revenue];
            });
        return response()->json(['revenue' => $revenue], 200);
    }


    function statusOrder(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $order = DB::table('orders')
            ->select('status', DB::raw('COUNT(*) as total'))
            ->when($from && $to, fn($q) => $q->whereBetween('order_date', [$from, $to]))
            ->groupBy('status')
            ->get();

        return response()->json(['order' => $order], 200);
    }

    function sellingProduct()
    {
        $product = Product::withSum('orderItems', 'quantity')
            ->orderByDesc('order_items_sum_quantity')

            ->get();

        return response()->json([
            'transformedBestSellers' => $product
        ], 200);
    }


    function soldOut()
    {
        $products = Product::with(['variants' => function ($query) {
            $query->where('stock_quantity', '<=', 30);
        }])
            ->whereHas('variants', function ($query) {
                $query->where('stock_quantity', '<=', 30);
            })
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'variants' => $product->variants->map(function ($variant) {
                        return [
                            'sku' => $variant->sku,
                            'stock_quantity' => $variant->stock_quantity
                        ];
                    })
                ];
            });

        return response()->json([
            'transformedSoldOuts' => $products
        ], 200);
    }

    function topCustomers()
    {
        $customers = User::withSum('orders', 'total_amount')
            ->orderByDesc('orders_sum_total_amount')
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'username' => $customer->username,
                    'total_amount' => $customer->orders_sum_total_amount,
                ];
            });

        return response()->json([
            'customers' => $customers
        ], 200);
    }

    function orderReview()
    {
        $totalOrderItems = OrderItem::count();
        $reviewedOrderItems = OrderItem::where('reviewed', true)->count();

        return response()->json([
            'total_orders' => $totalOrderItems,
            'reviewed_orders' => $reviewedOrderItems,
            'review_percentage' => $totalOrderItems > 0 ? round(($reviewedOrderItems / $totalOrderItems) * 100, 2) : 0
        ], 200);
    }
}
