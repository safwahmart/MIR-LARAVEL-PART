<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UnitResource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Codeboxr\RedxCourier\Facade\RedxCourier;
use Xenon\MultiCourier\Provider\ECourier;
use Xenon\MultiCourier\Courier;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::select('orders.*', 'customers.name as customer_name', 'name_bn as customer_name_bn','created.name as created_user','updated.name as updated_user', 'customers.phone', 'customers.address')->leftJoin('customers', 'customers.id', '=', 'orders.customer_id','created.name as created_user','updated.name as updated_user')->leftJoin('users as created', 'created.id', '=', 'orders.created_by')->leftJoin('users as updated', 'updated.id', '=', 'orders.updated_by')->addSelect([
            // Key is the alias, value is the sub-select
            'total_price' => OrderProduct::query()
                // You can use eloquent methods here
                ->select(DB::raw('sum(total_price) as value'))
                ->whereColumn('order_id', 'orders.id')
                ->groupBy('order_id')
                ->take(1)
        ])->latest()->get();
        return UnitResource::collection($orders);
    }

    public function searchOrder(Request $request)
    {
        $order = Order::query();
        $order->select('orders.*', 'customers.name as customer_name', 'name_bn as customer_name_bn','created.name as created_user','updated.name as updated_user', 'customers.phone', 'customers.address')->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')->leftJoin('users as created', 'created.id', '=', 'orders.created_by')->leftJoin('users as updated', 'updated.id', '=', 'orders.updated_by')->addSelect([
            // Key is the alias, value is the sub-select
            'total_price' => OrderProduct::query()
                // You can use eloquent methods here
                ->select(DB::raw('sum(total_price) as value'))
                ->whereColumn('order_id', 'orders.id')
                ->groupBy('order_id')
                // ->latest()
                ->take(1)
        ]);
        if ($request->district_id) {
            $order->where('district', $request->district_id);
        }
        if ($request->area_id) {
            $order->where('area', $request->area_id);
        }
        if ($request->name) {
            $order->where('name', $request->name)->orWhere('invoice_no', $request->name);
        }
        if ($request->status) {
            $order->where('orders.status', $request->status);
        }
        if ($request->start_date) {
            $order->whereBetween('order_date', [$request->start_date, $request->end_date]);
        }
        $orders = $order->get();
        return UnitResource::collection($orders);
    }
    public function searchTrack(Request $request)
    {
        $order = Order::query();
        $order->select('orders.*', 'customers.name as customer_name', 'name_bn as customer_name_bn', 'customers.phone', 'customers.address')->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')->leftJoin('users', 'users.customer_id', '=', 'orders.customer_id')->addSelect([
            // Key is the alias, value is the sub-select
            'total_price' => OrderProduct::query()
                // You can use eloquent methods here
                ->select(DB::raw('sum(total_price) as value'))
                ->whereColumn('order_id', 'orders.id')
                ->groupBy('order_id')
                // ->latest()
                ->take(1)
        ]);
        if ($request->email) {
            $order->where('users.email', $request->email)->orWhere('customers.phone', $request->email);
        }
        // if ($request->user_id) {
        //     $order->where('users.id', $request->user_id);
        // }
        if ($request->order_no) {
            $order->where('invoice_no', $request->order_no);
        }
        $orders = $order->get();
        return UnitResource::collection($orders);
    }
    public function getOrder($id)
    {
        $orders = Order::select('orders.*', 'name as customer_name', 'name_bn as customer_name_bn', 'phone', 'address', 'district', 'area', 'zip_code')->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')->where('orders.id', $id)->first();
        return $orders;
        // return UnitResource::collection($orders);
    }
    public function getOrderProduct($id)
    {
        $orders = OrderProduct::select('order_products.*', 'product_sku', 'product_name', 'units.name as unit_name', 'units.name_bn as unit_name_bn')->leftJoin('products', 'products.id', '=', 'order_products.product_id')->addSelect([
            // Key is the alias, value is the sub-select
            'stock_in' => StockIn::query()
                // You can use eloquent methods here
                ->select(DB::raw('sum(qty) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->addSelect([
            // Key is the alias, value is the sub-select
            'stock_out' => StockOut::query()
                // You can use eloquent methods here
                ->select(DB::raw('sum(qty) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->leftJoin('units', 'units.id', '=', 'products.unit_id')->where('order_id', $id)->get();
        return UnitResource::collection($orders);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'customer_id' => 'required',
            'warehouse_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $order = Order::latest()->first();
        $product_id = json_decode($request->product_id);
        $order_price = json_decode($request->unit_price);
        $variation_id = json_decode($request->variation_id);
        // return empty($variation_id)?'sonjoy':'biswas';
        $qty = json_decode($request->qty);
        $total_price = json_decode($request->total_price);
        $customer_id = $request->customer_id;
        if (!isset($customer_id)) {
            $customer = Customer::create([
                'customer_type_id' => $request->customer_type_id,
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'district' => $request->district,
                'area' => $request->area,
                'zip_code' => $request->zip_code,
            ]);
            $customer_id = $customer->id;
            User::create([
                "name" => $request->name,
                "email" => $request->email,
                "phone" => $request->phone,
                "password" => Hash::make($request->password),
                "customer_id" => $customer_id,
            ]);
        }
        $order = Order::create([
            'invoice_no' => isset($order) ? date('ymd') . '0000' . $order->serial + 1 : date('ymd') . '00001',
            "customer_id" => $customer_id,
            "warehouse_id" => $request->warehouse_id,
            "date" => $request->date,
            "sale_by" => $request->sale_by,
            "order_date" => $request->order_date,
            "delivery_date" => $request->delivery_date,
            "time_slot_id" => $request->time_slot_id,
            "discount_amount" => $request->discount_amount,
            "discount_per" => $request->discount_per,
            "special_discount_amount" => $request->special_discount,
            "special_dis_per" => $request->special_dis_per,
            "order_note" => $request->order_note,
            "sub_total" => $request->sub_total,
            "vat" => $request->vat,
            "shipping_cost" => $request->shipping_cost,
            "cod_charge" => $request->cod_charge,
            "rounding" => $request->rounding,
            "payable" => $request->payable,
            'created_by'=>Auth::user()->id,
            'updated_by'=>Auth::user()->id,
            'serial' => isset($order) ? $order->serial + 1 : 1
        ]);
        for ($i = 0; $i < count($product_id); $i++) {
            OrderProduct::create([
                "order_id" => $order->id,
                "product_id" => $product_id[$i],
                "unit_price" => $order_price[$i],
                "variation_id" => !empty($variation_id) ?$variation_id[$i]:null,
                "qty" => $qty[$i],
                "total_price" => $total_price[$i],
            ]);
            StockOut::create([
                "stock_reason_id" => $order->id,
                "product_id" => $product_id[$i],
                "variation_id" => !empty($variation_id) ?$variation_id[$i]:null,
                "qty" => $qty[$i],
                "unit_price" => $order_price[$i],
                "stock_reason" => 'purchase',
            ]);
        }
        $offers = Order::latest()->get();
        return new UnitResource($offers);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        $order = Order::find($order->id);

        if (is_null($order)) {
            return $this->sendError('Brand not found.');
        }
        $response = ['status' => true, 'data' => $order];
        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    public function getOrderSerial()
    {
        $serial = Order::select('serial')->latest()->first();

        return $serial ? $serial->serial : 0;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required',
            'warehouse_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $product_id = json_decode($request->product_id);
        $order_price = json_decode($request->unit_price);
        $variation_id = json_decode($request->variation_id);
        $qty = json_decode($request->qty);
        $total_price = json_decode($request->total_price);
        $order->update([
            "customer_id" => $request->customer_id,
            "warehouse_id" => $request->warehouse_id,
            "date" => $request->date,
            "sale_by" => $request->sale_by,
            "order_date" => $request->order_date,
            "delivery_date" => $request->delivery_date,
            "time_slot_id" => $request->time_slot_id,
            "discount_amount" => $request->discount_amount,
            "discount_per" => $request->discount_per,
            "special_discount_amount" => $request->special_discount,
            "special_dis_per" => $request->special_dis_per,
            "order_note" => $request->order_note,
            "sub_total" => $request->sub_total,
            "vat" => $request->vat,
            "shipping_cost" => $request->shipping_cost,
            "cod_charge" => $request->cod_charge,
            "rounding" => $request->rounding,
            "payable" => $request->payable,
            'updated_by'=>Auth::user()->id,
        ]);
        OrderProduct::where('order_id', $order->id)->delete();
        StockOut::where('stock_reason_id', $order->id)->delete();
        for ($i = 0; $i < count($product_id); $i++) {
            OrderProduct::create([
                "order_id" => $order->id,
                "product_id" => $product_id[$i],
                "unit_price" => $order_price[$i],
                "variation_id" => $variation_id[$i],
                "qty" => $qty[$i],
                "total_price" => $total_price[$i],
            ]);
            StockOut::create([
                "stock_reason_id" => $order->id,
                "product_id" => $product_id[$i],
                "variation_id" => $variation_id[$i],
                "qty" => $qty[$i],
                "unit_price" => $order_price[$i],
                "stock_reason" => 'purchase',
            ]);
        }
        $offers = Order::latest()->get();
        return new UnitResource($order);
    }

    public function statusUpdate(Request $request)
    {
        $order = Order::find($request->id);
        $order->update([
            'status' => $request->status,
            'updated_by'=>Auth::user()->id,
        ]);
        $offers = Order::latest()->get();
        return new UnitResource($order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::find($id);
        $order->delete();
        OrderProduct::where('order_id', $order->id)->delete();
        StockOut::where('order_id', $order->id)->delete();
        $response = ['status' => true, 'message' => 'Deleted successfully.'];
        return response($response, 200);
    }

    public function redxCreateOrder(Request $request)
    {
        $products = OrderProduct::select('categories.name as category_name', 'product_name', 'sale_price')->leftJoin('products', 'products.id', '=', 'order_products.product_id')->leftJoin('categories', 'categories.id', '=', 'products.category_id')->where('order_id', $request->id)->get();
        $list = [];
        foreach ($products as $index => $product) {
            $list[$index]['name'] = $product->product_name ?? '';
            $list[$index]['category'] = $product->category_name ?? '';
            $list[$index]['value'] = $product->sale_price ?? 0;
        }
        $pages_array[] = (object) array('name' => $products[0]->product_name, 'category' => 'asdf', 'value' => $products[0]->sale_price);
        // try {
        $redx = RedxCourier::order()
            ->create([
                "customer_name"          => $request->customer_name ?? '',
                "customer_phone"         => $request->phone ?? '',
                "delivery_area"          => "Mirpur DOHS", // delivery area name
                "delivery_area_id"       => 1, // area id
                "customer_address"       => $request->address ?? '',
                "merchant_invoice_id"    => $request->invoice_no ?? '',
                "cash_collection_amount" => $request->payable ?? '',
                "parcel_weight"          => 500, //parcel weight in gram
                "instruction"            => "",
                "value"                  => 100, //compensation amount
                "pickup_store_id"        => 1, // store id
                "parcel_details_json"    => json_encode($list)
            ]);
        $order = Order::where('id', $request->id)->first();
        $order->update([
            'tracking_id' => $redx->tracking_id,            
            'updated_by'=>Auth::user()->id,
        ]);

        return response(['status' => true], 200);
        // } catch (Exception $e) {
        //     return response(['status' => false,'message'=>$e], 400);
        // }
    }
    public function redxTrackingById($id)
    {
        try {
            $order = Order::where('id', $id)->first();
            $tracking = RedxCourier::order()->orderDetails($order->tracking_id);
            return response(['status' => true, 'tracking' => $tracking], 200);
        } catch (Exception $e) {
            return response(['status' => false], 400);
        }
    }

    public function pathao()
    {
        $courier = Courier::getInstance();
        $courier->setProvider(ECourier::class, 'local'); /* local/production */
        $courier->setConfig([
            'API-KEY' => 'xxx',
            'API-SECRET' => 'xxxx',
            'USER-ID' => 'xxxx',
        ]);
        $orderData = array(
            'recipient_name' => 'XXXXX',
            'recipient_mobile' => '017XXXXX',
            'recipient_city' => 'Dhaka',
            'recipient_area' => 'Badda',
            'recipient_thana' => 'Badda',
            'recipient_address' => 'Full Address',
            'package_code' => '#XXXX',
            'product_price' => '1500',
            'payment_method' => 'COD',
            'recipient_landmark' => 'DBBL ATM',
            'parcel_type' => 'BOX',
            'requested_delivery_time' => '2019-07-05',
            'delivery_hour' => 'any',
            'recipient_zip' => '1212',
            'pick_hub' => '18490',
            'product_id' => 'DAFS',
            'pick_address' => 'Gudaraghat new mobile',
            'comments' => 'Please handle carefully',
            'number_of_item' => '3',
            'actual_product_price' => '1200',
            'pgwid' => 'XXX',
            'pgwtxn_id' => 'XXXXXX'
        );

        $courier->setParams($orderData);
        $response = $courier->placeOrder();
    }

    public function couriour()
    {
        // $curl = curl_init();

        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => "https://sandbox.redx.com.bd/v1.0.0-beta/parcel", // your preferred link
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => "",
        //     CURLOPT_TIMEOUT => 30000,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => "GET",
        //     CURLOPT_HTTPHEADER => array(
        //         // Set Here Your Requesred Headers
        //         'Content-Type: application/json',
        //         'API-ACCESS-TOKEN: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI4MDA5NTUiLCJpYXQiOjE2ODk4NDYwMzksImlzcyI6Imo1Q0tuTThtYTBQNExUajdBZGRsZVNrZTg4cnlmYUFyIiwic2hvcF9pZCI6ODAwOTU1LCJ1c2VyX2lkIjoxODU5NDQ3fQ.4mZfLtb2KELRe2dcyrtC6JO_QFiCOCRznJTkgmTWxBs',
        //     ),
        // ));
        // $response = curl_exec($curl);
        // $err = curl_error($curl);
        // curl_close($curl);

        // if ($err) {
        //     echo "cURL Error #:" . $err;
        // } else {
        //     print_r(json_decode($response));
        // }

        // $endpoint = "https://sandbox.redx.com.bd/v1.0.0-beta/parcel";
        // $client = new \GuzzleHttp\Client(['headers' => ['API-ACCESS-TOKEN' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI4MDA5NTUiLCJpYXQiOjE2ODk4NDYwMzksImlzcyI6Imo1Q0tuTThtYTBQNExUajdBZGRsZVNrZTg4cnlmYUFyIiwic2hvcF9pZCI6ODAwOTU1LCJ1c2VyX2lkIjoxODU5NDQ3fQ.4mZfLtb2KELRe2dcyrtC6JO_QFiCOCRznJTkgmTWxBs','X-Requested-With'=>'XMLHttpRequest']]);
        // $response = $client->post(
        //     $endpoint,
        // array(
        //     'body' => array(
        //         "customer_name" => "John Doe",
        //         "customer_phone" => "01345678999",
        //         "delivery_area" => "Mirpur DOHS",
        //         "delivery_area_id" => 1,
        //         "customer_address" => "House 1, Road 1, Mirpur DOHS, Dhaka",
        //         "merchant_invoice_id" => "",
        //         "cash_collection_amount" => "13293",
        //         "parcel_weight" => 500,
        //         "instruction" => "",
        //         "value" => 100,
        //         "is_closed_box" => false,
        //     )
        // )
        // ['form_params' => [
        //     "customer_name" => "John Doe",
        //     "customer_phone" => "01345678999",
        //     "customer_address"=> "House 1, Road 1, Mirpur DOHS, Dhaka",
        //     "cash_collection_amount"=> "13293",
        //     "parcel_weight"=> 500,
        //     "delivery_area"=> "Mirpur DOHS",
        //     "delivery_area_id" => 2,
        // "customer_address" => "House 1, Road 1, Mirpur DOHS, Dhaka",
        // "merchant_invoice_id" => "",
        // "cash_collection_amount" => "13293",
        // "parcel_weight" => 500,
        // "instruction" => "",
        // "value" => 100,
        // "is_closed_box" => false,
        // "parcel_details_json"=> [
        //     {
        //         "name"=> "item1",
        //         "category"=> "category1",
        //         "value"=> 120.05
        //     },
        //     {
        //         "name"=> "item2",
        //         "category"=> "category2",
        //         "value"=> 130.05
        //     }
        // ]
        // ]]
        // );

        // $response = $client->request('POST', $endpoint, ['query' => [
        //     "customer_name" => "John Doe",
        //     "customer_phone" => "01345678999",
        //     "delivery_area" => "Mirpur DOHS",
        //     "delivery_area_id" => 1,
        //     "customer_address" => "House 1, Road 1, Mirpur DOHS, Dhaka",
        //     "merchant_invoice_id" => "",
        //     "cash_collection_amount" => "13293",
        //     "parcel_weight" => 500,
        //     "instruction" => "",
        //     "value" => 100,
        //     "is_closed_box" => false,
        //     // "parcel_details_json"=> [
        //     //     {
        //     //         "name"=> "item1",
        //     //         "category"=> "category1",
        //     //         "value"=> 120.05
        //     //     },
        //     //     {
        //     //         "name"=> "item2",
        //     //         "category"=> "category2",
        //     //         "value"=> 130.05
        //     //     }
        //     // ]
        // ]]);

        // $statusCode = $response->getStatusCode();
        // $content = $response->getBody();
        // return $content;
        // return RedxCourier::store()
        //                 ->create([
        //                    "name"    => "Safwah Mart", //store name
        //                    "phone"   => "123456789", //store contact person
        //                    "area_id" => 1,
        //                    "address" => "House 1, Road 1, Mirpur DOHS, Dhaka",
        //                 ]);
        return RedxCourier::area()->list();
        return RedxCourier::order()->orderDetails('20A316MOG0DI');
        $pages_array[] = (object) array('name' => 'item1', 'category' => 'category1', 'value' => 120.05);
        return RedxCourier::order()
            ->create([
                "customer_name"          => "John Doe",
                "customer_phone"         => "01345678999",
                "delivery_area"          => "Mirpur DOHS", // delivery area name
                "delivery_area_id"       => 1, // area id
                "customer_address"       => "House 1, Road 1, Mirpur DOHS, Dhaka",
                "merchant_invoice_id"    => "",
                "cash_collection_amount" => "13293",
                "parcel_weight"          => 500, //parcel weight in gram
                "instruction"            => "",
                "value"                  => 100, //compensation amount
                "pickup_store_id"        => 1, // store id
                "parcel_details_json"    => json_encode($pages_array)
            ]);
    }
}
