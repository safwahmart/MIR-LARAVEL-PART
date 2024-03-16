<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductTypeResource;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductTypeController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customerTypes = ProductType::all();
        return ProductTypeResource::collection($customerTypes);
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
            'name' => 'required|unique:customer_types'
        ]);
       
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $brands = ProductType::create($request->all());
        
        return new ProductTypeResource($brands);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductType  $customerType
     * @return \Illuminate\Http\Response
     */
    public function show(ProductType $customerType)
    {
        $customerType = ProductType::find($customerType->id);
      
        if (is_null($customerType)) {
            return $this->sendError('Brand not found.');
        }
        $response = ['status'=>true, 'data'=>$customerType];
        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductType  $customerType
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductType $customerType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductType  $customerType
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
        $customerType = ProductType::find($id);
        $customerType->update($request->all());
        
        return new ProductTypeResource($customerType);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductType  $customerType
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customerType = ProductType::find($id);
        $customerType->delete();
        $response = ['status'=>true,'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
