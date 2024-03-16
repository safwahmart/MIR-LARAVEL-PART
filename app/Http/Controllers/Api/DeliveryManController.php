<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HighlightTypeResource;
use App\Models\DeliveryMan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeliveryManController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $highlightTypes = DeliveryMan::select('delivery_men.*','districts.name as district_name','districts.name_bn as district_name_bn','areas.name as area_name','areas.name_bn as area_name_bn')->leftJoin('districts','districts.id','=','delivery_men.district_id')->leftJoin('areas','areas.id','=','delivery_men.area_id')->latest()->get();
        return HighlightTypeResource::collection($highlightTypes);
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
            'delivery_man_name' => 'required|unique:delivery_men'
        ]);
       
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $brands = DeliveryMan::create($request->all());
        
        return new HighlightTypeResource($brands);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DeliveryMan  $highlightType
     * @return \Illuminate\Http\Response
     */
    public function show(DeliveryMan $highlightType)
    {
        $highlightType = DeliveryMan::find($highlightType->id);
      
        if (is_null($highlightType)) {
            return $this->sendError('Brand not found.');
        }
        $response = ['status'=>true, 'data'=>$highlightType];
        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DeliveryMan  $highlightType
     * @return \Illuminate\Http\Response
     */
    public function edit(DeliveryMan $highlightType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DeliveryMan  $highlightType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $highlightType = DeliveryMan::find($id);
        $validator = Validator::make($request->all(), [
            'delivery_man_name' => 'required'
        ]);
       
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $highlightType->update($request->all());
        
        return new HighlightTypeResource($highlightType);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DeliveryMan  $highlightType
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $highlightType = DeliveryMan::find($id);
        $highlightType->delete();
        $response = ['status'=>true,'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
