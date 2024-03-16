<?php

namespace App\Http\Controllers\Api;

use App\Models\SocialLink;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AreaResource;
use Illuminate\Support\Facades\Validator;

class SocialLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $areas = SocialLink::all();
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
        $brands = SocialLink::create($request->all());

        return new AreaResource($brands);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SocialLink  $area
     * @return \Illuminate\Http\Response
     */
    public function show(SocialLink $area)
    {
        $area = SocialLink::find($area->id);

        if (is_null($area)) {
            return $this->sendError('Brand not found.');
        }
        $response = ['status'=>true, 'data'=>$area];
        return response($response, 200);
    }

    public function getArea($id){
        $area = SocialLink::where('district_id',$id)->get();

        if (is_null($area)) {
            return $this->sendError('District not found.');
        }
        $response = ['status'=>true, 'data'=>$area];
        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SocialLink  $area
     * @return \Illuminate\Http\Response
     */
    public function edit(SocialLink $area)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SocialLink  $area
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $area = SocialLink::find($id);
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
     * @param  \App\Models\SocialLink  $area
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $area = SocialLink::find($id);
        $area->delete();
        $response = ['status'=>true,'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
