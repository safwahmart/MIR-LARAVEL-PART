<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AreaResource;
use App\Models\AccountChart;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;
use App\Models\WishList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function totalCustomer()
    {
        $areas = Customer::count();
        return $areas;
    }
    public function totalProduct()
    {
        $areas = Product::count();
        return $areas;
    }
    public function totalSale()
    {
        $areas = Sale::count();
        return $areas;
    }
    public function todaySale()
    {
        $areas = Sale::where('sale_date', Carbon::now()->toDateString())->count();
        return $areas;
    }

    public function totalWishList($id)
    {
        $products = WishList::where('status', 1)->where('user_id', $id)->count();
        return $products;
    }
    public function totalOrder(Request $request)
    {
        $filter =  $request->get('filter');
        if ($filter == 'today') {
            $areas = Order::where('order_date', Carbon::now()->toDateString())->count();
            return $areas;
        }
        if ($filter == 'yesterday') {
            $areas = Order::where('order_date', Carbon::yesterday())->count();
            return $areas;
        }
        if ($filter == 'weekly') {
            $areas = Order::whereBetween('order_date', [Carbon::now()->subDays(7), Carbon::now()])->count();
            return $areas;
        }
        if ($filter == 'month') {
            $areas = Order::whereBetween('order_date', [Carbon::now()->subDays(30), Carbon::now()])->count();
            return $areas;
        }
        if ($filter == 'year') {
            $currentDate = \Carbon\Carbon::now();
            $agoDate = $currentDate->subDays($currentDate->dayOfYear())->subYear();
            $areas = Order::whereBetween('order_date', [$agoDate, Carbon::now()])->count();
            return $areas;
        }
    }
    public function saleBarcode(Request $request)
    {
        $filter =  $request->get('filter');
        if ($filter == 'today') {
            $users = Sale::select(Sale::raw("COUNT(*) as count"), Sale::raw("Hour(created_at) as date"))
                ->whereYear('created_at', date('Y'))
                ->whereMonth('created_at', date('m'))
                ->whereDay('created_at', date('d'))
                ->groupBy(Sale::raw("Hour(created_at)"))
                ->get();
            return $users;
        }
        if ($filter == 'weekly') {
            $users = Sale::select(Sale::raw("COUNT(*) as count"), Sale::raw("Week(created_at) as date"))
                ->whereYear('created_at', date('Y'))
                ->whereMonth('created_at', date('m'))
                ->groupBy(Sale::raw("Week(created_at)"))
                ->get();
            return $users;
        }
        if ($filter == 'monthly') {
            $users = Sale::select(Sale::raw("COUNT(*) as count"), Sale::raw("Day(created_at) as date"))
                ->whereYear('created_at', date('Y'))
                ->groupBy(Sale::raw("Day(created_at)"))
                // ->pluck('count','date');
                ->get();
            return $users;
        }
        if ($filter == 'yearly') {
            $users = Sale::select(Sale::raw("COUNT(*) as count"), Sale::raw("Month(created_at) as date"))
                ->whereYear('created_at', date('Y'))
                ->groupBy(Sale::raw("Month(created_at)"))
                ->get();
                // ->pluck('count');
            // $months = Sale::select(Sale::raw("COUNT(*) as month"))
            //     ->whereYear('created_at', date('Y'))
            //     ->groupBy(Sale::raw("Month(created_at)"))
            //     ->pluck('month');
            // $datas = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            // foreach ($months as $index => $month) {
            //     $datas[$month] = $users[$index];
            // }
            return $users;
        }
    }
    public function SaleBar(Request $request)
    {
        $filter =  $request->get('filter');
        if ($filter == 'today') {
            $areas = Sale::where('sale_date', Carbon::now())->count();
            return $areas;
        }
        if ($filter == 'yesterday') {
            $areas = Sale::where('sale_date', Carbon::yesterday())->count();
            return $areas;
        }
        if ($filter == 'weekly') {
            $areas = Sale::whereBetween('sale_date', [Carbon::now()->subDays(7), Carbon::now()])->count();
            return $areas;
        }
        if ($filter == 'month') {
            $areas = Sale::whereBetween('sale_date', [Carbon::now()->subDays(30), Carbon::now()])->count();
            return $areas;
        }
        if ($filter == 'year') {
            $currentDate = \Carbon\Carbon::now();
            $agoDate = $currentDate->subDays($currentDate->dayOfYear())->subYear();
            $areas = Sale::whereBetween('sale_date', [$agoDate, Carbon::now()])->count();
            return $areas;
        }
    }
}
