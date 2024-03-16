<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HighlightTypeResource;
use App\Models\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PointController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $highlightTypes = Point::all();
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
            'title' => 'required'
        ]);
       
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $brands = Point::create($request->all());
        
        return new HighlightTypeResource($brands);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Point  $highlightType
     * @return \Illuminate\Http\Response
     */
    public function show(Point $highlightType)
    {
        $highlightType = Point::find($highlightType->id);
      
        if (is_null($highlightType)) {
            return $this->sendError('Brand not found.');
        }
        $response = ['status'=>true, 'data'=>$highlightType];
        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Point  $highlightType
     * @return \Illuminate\Http\Response
     */
    public function edit(Point $highlightType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Point  $highlightType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required'
        ]);
       
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $highlightType = Point::find($id);
        $highlightType->update($request->all());
        
        return new HighlightTypeResource($highlightType);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Point  $highlightType
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $highlightType = Point::find($id);
        $highlightType->delete();
        $response = ['status'=>true,'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
