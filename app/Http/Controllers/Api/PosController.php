<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UnitResource;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\SaleProduct;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Sale::select('sales.*','name as customer_name','name_bn as customer_name_bn')->leftJoin('customers','customers.id','=','sales.customer_id')->addSelect([
            // Key is the alias, value is the sub-select
            'total_qty' => SaleProduct::query()
                // You can use eloquent methods here
                ->select(DB::raw('sum(qty) as value'))
                ->whereColumn('sale_id', 'sales.id')
                ->groupBy('sale_id')
                ->take(1)
        ])->latest()->get();
        return UnitResource::collection($orders);
    }
    public function getSale($id)
    {
        $orders = Sale::select('sales.*','name as customer_name','name_bn as customer_name_bn','phone','address','district','area','zip_code')->leftJoin('customers','customers.id','=','sales.customer_id')->where('sales.id', $id)->first();
        return $orders;
        // return UnitResource::collection($orders);
    }
    public function getSaleProduct($id)
    {
        $orders = SaleProduct::select('sale_products.*', 'product_sku','product_name', 'units.name as unit_name', 'units.name_bn as unit_name_bn','sale_price','discount')->leftJoin('products', 'products.id', '=', 'sale_products.product_id')->addSelect([
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
        ])->leftJoin('units', 'units.id', '=', 'products.unit_id')->where('sale_id', $id)->get();
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
            'customer_id' => 'required',
            'warehouse_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $order = Sale::latest()->first();
        $product_id = json_decode($request->product_id);
        $qty = json_decode($request->qty);
        $total_price = json_decode($request->total_price);
        $payment_method = json_decode($request->payment_method);
        $amount = json_decode($request->amount);
        $order = Sale::create([
            'invoice_no' => isset($order)?date('ymd') . '0000' . $order->serial + 1:date('ymd') . '00001',
            "customer_id" => $request->customer_id,
            "warehouse_id" => $request->warehouse_id,
            "sale_date" => $request->sale_date,
            "category_id" => $request->category_id,
            "discount_amount" => $request->discount_amount,
            "sub_total" => $request->sub_total,
            "vat" => $request->vat,
            "rounding" => $request->rounding,
            "payable" => $request->payable,
            "paid_amount" => $request->paid_amount,
            "due" => $request->due,
            "change" => $request->change,
            'serial' => isset($order)?$order->serial + 1:1
        ]);
        for ($i = 0; $i < count($product_id); $i++) {
            SaleProduct::create([
                "sale_id" => $order->id,
                "product_id" => $product_id[$i],
                "qty" => $qty[$i],
                "total_price" => $total_price[$i],
            ]);
            StockOut::create([
                "stock_reason_id" => $order->id,
                "product_id" =>$product_id[$i],
                "qty" => $qty[$i],
                "stock_reason" => 'purchase',
            ]);
        }
        for($j = 0; $j< count($payment_method);$j++){
            SalePayment::create([
                "sale_id" => $order->id,
                "payment_method" =>$payment_method[$j],
                "amount" =>$amount[$j],
            ]);
        }
        return new UnitResource($order);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sale  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Sale $order)
    {
        $order = Sale::find($order->id);

        if (is_null($order)) {
            return $this->sendError('Brand not found.');
        }
        $response = ['status' => true, 'data' => $order];
        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sale  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Sale $order)
    {
        //
    }

    public function getSaleSerial()
    {
        $serial = Sale::select('serial')->latest()->first();

        return $serial ? $serial->serial : 0;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sale  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sale $order)
    {
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
            "special_discount_amount" => $request->special_discount_amount,
            "order_note" => $request->order_note,
            "sub_total" => $request->sub_total,
            "vat" => $request->vat,
            "shipping_cost" => $request->shipping_cost,
            "cod_charge" => $request->cod_charge,
            "rounding" => $request->rounding,
            "payable" => $request->payable,
        ]);
        SaleProduct::where('sale_id', $order->id)->delete();
        StockOut::where('stock_reason_id', $order->id)->delete();
        for ($i = 0; $i < count($product_id); $i++) {
            SaleProduct::create([
                "sale_id" => $order->id,
                "product_id" => $product_id[$i],
                "unit_price" => $order_price[$i],
                "variation_id" => $variation_id[$i],
                "qty" => $qty[$i],
                "total_price" => $total_price[$i],
            ]);
            StockOut::create([
                "stock_reason_id" => $order->id,
                "product_id" =>$product_id[$i],
                "variation_id" => $variation_id[$i],
                "qty" => $qty[$i],
                "unit_price" => $order_price[$i],
                "stock_reason" => 'purchase',
            ]);
        }
        $offers = Sale::latest()->get();
        return new UnitResource($order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sale  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sale $order)
    {
        $order->delete();
        SaleProduct::where('sale_id', $order->id)->delete();
        StockOut::where('sale_id', $order->id)->delete();
        $response = ['status' => true, 'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
