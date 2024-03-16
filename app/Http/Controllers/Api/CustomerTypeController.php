<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerTypeResource;
use App\Models\CustomerType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerTypeController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customerTypes = CustomerType::all();
        return CustomerTypeResource::collection($customerTypes);
    }
    public function customerTypeForProduct()
    {
        $customerTypes = CustomerType::where('status',1)->latest()->get();
        return CustomerTypeResource::collection($customerTypes);
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
        $brands = CustomerType::create($request->all());
        
        return new CustomerTypeResource($brands);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CustomerType  $customerType
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerType $customerType)
    {
        $customerType = CustomerType::find($customerType->id);
      
        if (is_null($customerType)) {
            return $this->sendError('Brand not found.');
        }
        $response = ['status'=>true, 'data'=>$customerType];
        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CustomerType  $customerType
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerType $customerType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomerType  $customerType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomerType $customerType)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
       
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $customerType->update($request->all());
        
        return new CustomerTypeResource($customerType);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomerType  $customerType
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomerType $customerType)
    {
        $customerType->delete();
        $response = ['status'=>true,'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
