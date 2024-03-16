<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = Customer::all();
        return CustomerResource::collection($customers);
    }
    public function getCustomerTypes()
    {
        $customers = CustomerType::where('status', 1)->get();
        return CustomerResource::collection($customers);
    }
    public function getCustomers()
    {
        $customers = Customer::where('status', 1)->get();
        return CustomerResource::collection($customers);
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
            'name' => 'required|unique:customer_types',
            'email' => 'required|email|unique:users',
            'phone' => 'unique:customers',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $brands = Customer::create($request->all());
        User::create([
            "name" => $request->name,
            "email" => $request->email,
            "phone" => $request->phone,
            "password" => Hash::make($request->password),
            "customer_id" => $brands->id,
        ]);

        return new CustomerResource($brands);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        $customer = Customer::find($customer->id);

        if (is_null($customer)) {
            return $this->sendError('Brand not found.');
        }
        $response = ['status' => true, 'data' => $customer];
        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::where('customer_id', $id)->first();
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $customer = Customer::find($id);
        $companyLogo = '';
        if ($request->hasFile('photo')) {
            $companyLogo = time() . '.' . $request->photo->extension();

            $request->photo->move(public_path('uploads'), $companyLogo);

            $request = new Request($request->all());
            $request->merge(['photo' => $companyLogo]);
        }
        $user->update([
            "name" => $request->name,
            "email" => $user->email,
            "photo" => $request->photo,
            "password" => Hash::make($request->password),
        ]);
        $request->replace( $request->except('photo') );
        // $request->request->remove('photo');
        // return $request;

        $customer->update($request->all());

        return new CustomerResource($customer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = Customer::find($id);
        $user = User::where('customer_id',$id)->first();
        $customer->delete();
        $user->delete();
        $response = ['status' => true, 'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
