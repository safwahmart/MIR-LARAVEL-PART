<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UnitResource;
use App\Models\Discount;
use App\Models\Purchase;
use App\Models\PurchaseProduct;
use App\Models\StockIn;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $discounts = Purchase::select('purchases.*','supplier_name','supplier_name_bn','name','name_bn')->selectRaw('(SELECT sum(qty) FROM purchase_products WHERE purchase_products.purchase_id =purchases.id group by purchase_id) AS total_qty')->selectRaw('(SELECT sum(unit_cost) FROM purchase_products WHERE purchase_products.purchase_id =purchases.id group by purchase_id) AS total_qty')->leftJoin('suppliers','suppliers.id','=','purchases.supplier_id')->leftJoin('ware_houses','ware_houses.id','=','purchases.warehouse_id')->latest()->get();
        return UnitResource::collection($discounts);
    }
    public function searchPurchase(Request $request)
    {
        $discount = Purchase::query();
        $discount->addSelect(['qty' => PurchaseProduct::select('qty')->limit(1)])->select('purchases.*','supplier_name','supplier_name_bn','name','name_bn')->leftJoin('suppliers','suppliers.id','=','purchases.supplier_id')->leftJoin('ware_houses','ware_houses.id','=','purchases.warehouse_id');
        if ($request->invoice_no) {
            $discount->where('invoice_no', $request->invoice_no);
        }
        if ($request->supplier_name) {
            $discount->where('supplier_id', $request->supplier_name);
        }
        if ($request->warehouse) {
            $discount->where('warehouse_id', $request->warehouse);
        }
        if ($request->start_date) {
            $discount->whereBetween('date', [$request->start_date, $request->end_date]);
        }
        $discounts = $discount->get();
        return UnitResource::collection($discounts);
    }
    public function getPurchase($id)
    {
        $purchases = Purchase::where('id', $id)->first();
        return $purchases;
        // return UnitResource::collection($purchases);
    }
    public function getPurchaseProduct($id)
    {
        $purchases = PurchaseProduct::select('purchase_products.*', 'product_sku', 'categories.name as category_name', 'categories.name as category_name_bn', 'units.name as unit_name', 'units.name_bn as unit_name_bn')->leftJoin('products', 'products.id', '=', 'purchase_products.product_id')->leftJoin('categories', 'categories.id', '=', 'products.category_id')->leftJoin('units', 'units.id', '=', 'products.unit_id')->where('purchase_id', $id)->get();
        return UnitResource::collection($purchases);
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
            'supplier_id' => 'required',
            'warehouse_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $purchase = Purchase::latest()->first();
        $product_id = json_decode($request->product_id);
        $lot = json_decode($request->lot);
        $unit_cost = json_decode($request->unit_cost);
        $variation_id = json_decode($request->variation_id);
        $qty = json_decode($request->qty);
        $expire_date = json_decode($request->expire_date);
        $comment = json_decode($request->comment);
        $purchase = Purchase::create([
            'invoice_no' => isset($purchase)?date('ymd') . '0000' . $purchase->serial + 1:date('ymd') . '00001',
            "supplier_id" => $request->supplier_id,
            "warehouse_id" => $request->warehouse_id,
            "date" => $request->date,
            'serial' => isset($purchase)?$purchase->serial + 1:1
        ]);
        for ($i = 0; $i < count($product_id); $i++) {
            PurchaseProduct::create([
                "purchase_id" => $purchase->id,
                "product_id" => count($product_id)>0 ?$product_id[$i]:'',
                "lot" => count($lot)>0 ?$lot[$i]:'',
                "unit_cost" => count($unit_cost)>0 ?$unit_cost[$i]:'',
                "variation_id" => count($variation_id)>0 ?$variation_id[$i]:null,
                "qty" => count($qty)>0 ?$qty[$i]:'',
                "expire_date" => count($expire_date)>0 ?$expire_date[$i]:'',
                "comment" => count($comment)>0 ?$comment[$i]:'',
            ]);
            
            StockIn::create([
                "purchase_id" => $purchase->id,
                "product_id" => count($product_id)>0 ?$product_id[$i]:'',
                "variation_id" => count($variation_id)>0 ?$variation_id[$i]:null,
                "qty" => count($qty)>0 ?$qty[$i]:'',
                "unit_price" => count($unit_cost)>0 ?$unit_cost[$i]:'',
            ]);
        }
        $offers = Purchase::select('purchase_products.*', 'purchases.supplier_id', 'purchases.warehouse_id', 'purchases.date')->leftJoin('purchase_products', 'purchases.id', '=', 'purchase_products.product_id')->latest()->get();
        return new UnitResource($offers);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Discount  $unit
     * @return \Illuminate\Http\Response
     */
    public function show(Discount $unit)
    {
        $unit = Discount::find($unit->id);

        if (is_null($unit)) {
            return $this->sendError('Brand not found.');
        }
        $response = ['status' => true, 'data' => $unit];
        return response($response, 200);
    }

    public function purchaseStatus(Request $request,$id){
        $purchase = Purchase::find($id);
        $purchase->update([
            "status"=> $request->status
        ]);
        return new UnitResource($purchase);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Discount  $unit
     * @return \Illuminate\Http\Response
     */
    public function edit(Discount $unit)
    {
        //
    }

    public function getDiscountSerial()
    {
        $serial = Discount::select('serial')->latest()->first();

        return $serial ? $serial->serial : 0;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Discount  $unit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Purchase $purchase)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required',
            'warehouse_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $product_id = json_decode($request->product_id);
        $lot = json_decode($request->lot);
        $unit_cost = json_decode($request->unit_cost);
        $variation_id = json_decode($request->variation_id);
        $qty = json_decode($request->qty);
        $expire_date = json_decode($request->expire_date);
        $comment = json_decode($request->comment);
        $purchase->update([
            "supplier_id" => $request->supplier_id,
            "warehouse_id" => $request->warehouse_id,
            "date" => $request->date,
        ]);
        PurchaseProduct::where('purchase_id', $purchase->id)->delete();
        StockIn::where('purchase_id', $purchase->id)->delete();
        for ($i = 0; $i < count($product_id); $i++) {
            PurchaseProduct::create([
                "purchase_id" => $purchase->id,
                "product_id" => $product_id[$i],
                "lot" => $lot[$i],
                "unit_cost" => $unit_cost[$i],
                "variation_id" => $variation_id[$i],
                "qty" => $qty[$i],
                "expire_date" => $expire_date[$i],
                "comment" => $comment[$i],
            ]);
            StockIn::create([
                "purchase_id" => $purchase->id,
                "product_id" =>$product_id[$i],
                "variation_id" => $variation_id[$i],
                "qty" => $qty[$i],
                "unit_price" => $unit_cost[$i],
            ]);
        }

        return new UnitResource($purchase);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Discount  $purchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purchase $purchase)
    {
        $purchase->delete();
        PurchaseProduct::where('purchase_id', $purchase->id)->delete();
        StockIn::where('purchase_id', $purchase->id)->delete();
        $response = ['status' => true, 'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
