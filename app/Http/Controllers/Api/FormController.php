<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AreaResource;
use App\Models\Appointment;
use App\Models\Area;
use App\Models\CorporateForm;
use App\Models\Country;
use App\Models\OrderByPicture;
use App\Models\SupplyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function corporate()
    {
        $areas = CorporateForm::all();
        return AreaResource::collection($areas);
    }
    public function order_by_picture()
    {
        $areas = OrderByPicture::all();
        return AreaResource::collection($areas);
    }
    public function appointment()
    {
        $areas = Appointment::all();
        return AreaResource::collection($areas);
    }
    public function supplyRequest()
    {
        $areas = SupplyRequest::all();
        return AreaResource::collection($areas);
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
            'name' => 'required'
        ]);
       
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $brands = Area::create($request->all());
        
        return new AreaResource($brands);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function show(Area $area)
    {
        $area = Area::find($area->id);
      
        if (is_null($area)) {
            return $this->sendError('Brand not found.');
        }
        $response = ['status'=>true, 'data'=>$area];
        return response($response, 200);
    }

    public function getArea($id){
        $area = Area::where('district_id',$id)->get();
      
        if (is_null($area)) {
            return $this->sendError('District not found.');
        }
        $response = ['status'=>true, 'data'=>$area];
        return response($response, 200);
    }
    public function getCountry(){
        $country = Country::all();
      
        if (is_null($country)) {
            return $this->sendError('District not found.');
        }
        $response = ['status'=>true, 'data'=>$country];
        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function edit(Area $area)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Area $area)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
       
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $area->update($request->all());
        
        return new AreaResource($area);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function supplyRequestDelete($id)
    {
        $area = SupplyRequest::find($id);
        $area->delete();
        $response = ['status'=>true,'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
