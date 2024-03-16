<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use Illuminate\Http\Request;
use App\Models\Tag;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $units = Tag::all();
        return TagResource::collection($units);
    }
    public function getTagForProduct()
    {
        $units = Tag::where('status',1)->latest()->get();
        return TagResource::collection($units);
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
       
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $brands = Tag::create($request->all());
        
        return new TagResource($brands);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tag  $unit
     * @return \Illuminate\Http\Response
     */
    public function show(Tag $unit)
    {
        $unit = Tag::find($unit->id);
      
        if (is_null($unit)) {
            return $this->sendError('Brand not found.');
        }
        $response = ['status'=>true, 'data'=>$unit];
        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tag  $unit
     * @return \Illuminate\Http\Response
     */
    public function edit(Tag $unit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tag  $unit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
       
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $unit = Tag::find($id);
        $unit->update($request->all());
        
        return new TagResource($unit);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tag  $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id)
    {        
        $unit = Tag::find($id);
        $unit->delete();
        $response = ['status'=>true,'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
