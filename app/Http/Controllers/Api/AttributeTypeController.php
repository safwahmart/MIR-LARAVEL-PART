<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttributeTypeResource;
use App\Models\Attribute;
use App\Models\AttributeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AttributeTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $attributeTypes = AttributeType::query()
        ->addSelect([
            'attribute_name' => Attribute::query()
            // You can use eloquent methods here
            ->select('name')
            ->whereColumn('attribute_type_id', 'attribute_types.id')
            ->latest()
            ->take(1)
        ])->get();
        return AttributeTypeResource::collection($attributeTypes);
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
        $brands = AttributeType::create($request->all());

        return new AttributeTypeResource($brands);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AttributeType  $attributeType
     * @return \Illuminate\Http\Response
     */
    public function show(AttributeType $attributeType)
    {
        $attributeType = AttributeType::find($attributeType->id);

        if (is_null($attributeType)) {
            return $this->sendError('Brand not found.');
        }
        $response = ['status'=>true, 'data'=>$attributeType];
        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AttributeType  $attributeType
     * @return \Illuminate\Http\Response
     */
    public function edit(AttributeType $attributeType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AttributeType  $attributeType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AttributeType $attributeType)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $attributeType->update($request->all());

        return new AttributeTypeResource($attributeType);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AttributeType  $attributeType
     * @return \Illuminate\Http\Response
     */
    public function destroy(AttributeType $attributeType)
    {
        $attributeType->delete();
        $response = ['status'=>true,'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
