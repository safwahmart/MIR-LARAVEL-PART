<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttributeResource;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $attributes = Attribute::all();
        // $attributeTypes = DB::table('attributes')
        // ->leftJoin('attribute_types','attributes.attribute_type_id','=','attribute_types.id')
        // ->get();
        // dd($attributeTypes);

        return AttributeResource::collection($attributes);
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
            'name' => 'required|unique:attributes'
        ]);
       
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $brands = Attribute::create($request->all());
        
        return new AttributeResource($brands);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Attribute  $attributeType
     * @return \Illuminate\Http\Response
     */
    public function show(Attribute $attributeType)
    {
        $attributeType = Attribute::find($attributeType->id);
      
        if (is_null($attributeType)) {
            return $this->sendError('Brand not found.');
        }
        $response = ['status'=>true, 'data'=>$attributeType];
        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Attribute  $attributeType
     * @return \Illuminate\Http\Response
     */
    public function edit(Attribute $attributeType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Attribute  $attribute
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attribute $attribute)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
       
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $attribute->update($request->all());
        
        return new AttributeResource($attribute);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Attribute  $attributeType
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attribute $attribute)
    {
        $attribute->delete();
        $response = ['status'=>true,'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
