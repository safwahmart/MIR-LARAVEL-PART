<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AreaResource;
use App\Models\AccountChart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountChartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $areas = AccountChart::all();
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
            'name' => 'required|unique:attribute_types'
        ]);
       
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $brands = AccountChart::create($request->all());
        
        return new AreaResource($brands);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AccountChart  $area
     * @return \Illuminate\Http\Response
     */
    public function show(AccountChart $area)
    {
        $area = AccountChart::find($area->id);
      
        if (is_null($area)) {
            return $this->sendError('Brand not found.');
        }
        $response = ['status'=>true, 'data'=>$area];
        return response($response, 200);
    }

    public function getArea($id){
        $area = AccountChart::where('district_id',$id)->get();
      
        if (is_null($area)) {
            return $this->sendError('District not found.');
        }
        $response = ['status'=>true, 'data'=>$area];
        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AccountChart  $area
     * @return \Illuminate\Http\Response
     */
    public function edit(AccountChart $area)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AccountChart  $area
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AccountChart $area)
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
     * @param  \App\Models\AccountChart  $area
     * @return \Illuminate\Http\Response
     */
    public function destroy(AccountChart $area)
    {
        $area->delete();
        $response = ['status'=>true,'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
