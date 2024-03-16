<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WareHouseResource;
use App\Models\WareHouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WareHouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $wareHouses = WareHouse::all();
        return WareHouseResource::collection($wareHouses);
    }
    public function warehouseForPurchase()
    {
        $wareHouses = WareHouse::where('status',1)->latest()->get();
        return WareHouseResource::collection($wareHouses);
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
        $brands = WareHouse::create($request->all());
        
        return new WareHouseResource($brands);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WareHouse  $wareHouse
     * @return \Illuminate\Http\Response
     */
    public function show(WareHouse $wareHouse)
    {
        $wareHouse = WareHouse::find($wareHouse->id);
      
        if (is_null($wareHouse)) {
            return $this->sendError('Brand not found.');
        }
        $response = ['status'=>true, 'data'=>$wareHouse];
        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WareHouse  $wareHouse
     * @return \Illuminate\Http\Response
     */
    public function edit(WareHouse $wareHouse)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WareHouse  $wareHouse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WareHouse $wareHouse)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
       
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $wareHouse->update($request->all());
        
        return new WareHouseResource($wareHouse);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WareHouse  $wareHouse
     * @return \Illuminate\Http\Response
     */
    public function destroy(WareHouse $wareHouse)
    {
        $wareHouse->delete();
        $response = ['status'=>true,'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
