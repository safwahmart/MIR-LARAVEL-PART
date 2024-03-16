<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AreaResource;
use App\Http\Resources\HighlightTypeResource;
use App\Models\AccountChart;
use App\Models\ExtraShippCost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExtraShipCostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $extraShippCosts = ExtraShippCost::all();
        return HighlightTypeResource::collection($extraShippCosts);
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
            'from_weight' => 'required'
        ]);
       
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $brands = ExtraShippCost::create($request->all());
        
        return new HighlightTypeResource($brands);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ExtraShippCost  $extraShippCost
     * @return \Illuminate\Http\Response
     */
    public function show(ExtraShippCost $extraShippCost)
    {
        $extraShippCost = ExtraShippCost::find($extraShippCost->id);
      
        if (is_null($extraShippCost)) {
            return $this->sendError('Brand not found.');
        }
        $response = ['status'=>true, 'data'=>$extraShippCost];
        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ExtraShippCost  $extraShippCost
     * @return \Illuminate\Http\Response
     */
    public function edit(ExtraShippCost $extraShippCost)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ExtraShippCost  $extraShippCost
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $extraShippCost = ExtraShippCost::find($id);
        $validator = Validator::make($request->all(), [
            'from_weight' => 'required'
        ]);
       
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $extraShippCost->update($request->all());
        
        return new HighlightTypeResource($extraShippCost);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ExtraShippCost  $extraShippCost
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $extraShippCost = ExtraShippCost::find($id);
        $extraShippCost->delete();
        $response = ['status'=>true,'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
