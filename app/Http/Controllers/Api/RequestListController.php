<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AreaResource;
use App\Models\RequestList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequestListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $areas = RequestList::select('request_lists.*','users.name','users.phone','product_name','product_name_bn')->leftJoin('products','request_lists.product_id','=','products.id')->leftJoin('users','request_lists.user_id','=','users.id')->get();
        return AreaResource::collection($areas);
    }

    public function searchRequestList(Request $request)
    {
        $areas = RequestList::query();
        $areas->select('request_lists.*','users.name','users.phone','product_name','product_name_bn')->leftJoin('products','request_lists.product_id','=','products.id')->leftJoin('users','request_lists.user_id','=','users.id')->leftJoin('customers','customers.id','=','users.customer_id');
        if ($request->district_id) {
            $areas->where('district', $request->district_id);
        }
        if ($request->area_id) {
            $areas->where('area', $request->area_id);
        }
        if ($request->name) {
            $areas->where('users.name', $request->name)->orWhere('customers.phone', $request->name);
        }
        if ($request->product_id) {
            $areas->where('products.id', $request->product_id);
        }
        $orders = $areas->get();
        return AreaResource::collection($orders);
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
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $brands = RequestList::create($request->all());
        $request_lists = RequestList::select('request_lists.*','users.name')->leftJoin('users','request_lists.user_id','=','users.id')->where('request_lists.id',$brands->id)->where('request_lists.status',1)->first();

        // return new AreaResource($brands);
        $response = ['status'=>true,'message'=>'Added Successfully', 'data'=>$request_lists];
        return response($response, 200);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RequestList  $area
     * @return \Illuminate\Http\Response
     */
    public function show(RequestList $area)
    {
        $area = RequestList::find($area->id);

        if (is_null($area)) {
            return $this->sendError('Brand not found.');
        }
        $response = ['status'=>true, 'data'=>$area];
        return response($response, 200);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RequestList  $area
     * @return \Illuminate\Http\Response
     */
    public function edit(RequestList $area)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RequestList  $area
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // $validator = Validator::make($request->all(), [
        //     'name' => 'required'
        // ]);

        // if ($validator->fails()) {
        //     return response(['errors' => $validator->errors()->all()], 422);
        // }
        $area=RequestList::find($id);
        $area->update($request->all());

        return new AreaResource($area);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RequestList  $area
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $area=RequestList::find($id);
        $area->delete();
        $response = ['status'=>true,'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
