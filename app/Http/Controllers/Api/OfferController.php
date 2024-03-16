<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UnitResource;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $units = Offer::all();
        return UnitResource::collection($units);
    }
    public function offerForPurchase()
    {
        $units = Offer::where('status',1)->latest()->get();
        return UnitResource::collection($units);
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
            'name' => 'required|unique:units'
        ]);

        $sliderImage = '';
        if ($request->hasFile('logo')) {
            $sliderImage = time() . '.' . $request->logo->extension();
            
            $request->logo->move(public_path('uploads'), $sliderImage);
            
            $request = new Request($request->all());
            $request->merge(['logo' => $sliderImage]);
        }
       
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $brands = Offer::create($request->all());
        
        return new UnitResource($brands);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Offer  $unit
     * @return \Illuminate\Http\Response
     */
    public function show(Offer $unit)
    {
        $unit = Offer::find($unit->id);
      
        if (is_null($unit)) {
            return $this->sendError('Brand not found.');
        }
        $response = ['status'=>true, 'data'=>$unit];
        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Offer  $unit
     * @return \Illuminate\Http\Response
     */
    public function edit(Offer $unit)
    {
        //
    }

    public function getOfferSerial()
    {
        $serial = Offer::select('serial')->latest()->first();
        
        return $serial?$serial->serial:0;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Offer  $unit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        $sliderImage = $request->logo;
        if ($request->hasFile('logo')) {
            $sliderImage = time() . '.' . $request->logo->extension();
            
            $request->logo->move(public_path('uploads'), $sliderImage);
            
            $request = new Request($request->all());
            $request->merge(['logo' => $sliderImage]);
        }
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $unit = Offer::find($id);
        $unit->update($request->all());
        
        return new UnitResource($unit);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Offer  $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $unit = Offer::find($id);
        $unit->delete();
        $response = ['status'=>true,'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
