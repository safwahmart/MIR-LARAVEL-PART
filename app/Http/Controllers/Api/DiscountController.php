<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UnitResource;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $discounts = Discount::select('product_name','product_name_bn','discounts.*','categories.name as category_name','categories.name_bn as category_name_bn','sale_price')->leftJoin('products','products.id','=','discounts.product_id')->leftJoin('categories','categories.id','=','products.category_id')->leftJoin('units','units.id','=','products.unit_id')->latest()->get();
        return UnitResource::collection($discounts);
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
            'offer_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $product_id = json_decode($request->product_id);
        $discount_percent = json_decode($request->discount_percent);
        $discount_flat = json_decode($request->discount_flat);
        $start_date = json_decode($request->start_date);
        $end_date = json_decode($request->end_date);
        $offer_status = json_decode($request->offer_status);
        for($i=0;$i<count($product_id);$i++){
            Discount::create([
                "offer_id" =>$request->offer_id,
                "product_id" =>$product_id[$i],
                "discount_percent" =>$discount_percent[$i],
                "discount_flat" =>$discount_flat[$i],
                "start_date" =>$start_date[$i],
                "end_date" =>$end_date[$i],
                "offer_status" =>$offer_status[$i],
            ]);
        }
        $offers = Discount::all();
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
        $response = ['status'=>true, 'data'=>$unit];
        return response($response, 200);
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
        
        return $serial?$serial->serial:0;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Discount  $unit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'offer_id' => 'required'
        ]);
       
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $unit = Discount::find($id);
        $unit->update($request->all());
        
        return new UnitResource($unit);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Discount  $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $unit = Discount::find($id);
        $unit->delete();
        $response = ['status'=>true,'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
