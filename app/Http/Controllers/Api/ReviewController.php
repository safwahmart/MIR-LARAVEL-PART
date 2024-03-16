<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AreaResource;
use App\Models\Area;
use App\Models\Country;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $areas = Review::select('reviews.*','users.name','product_name','product_name_bn')->leftJoin('products','reviews.product_id','=','products.id')->leftJoin('users','reviews.user_id','=','users.id')->get();
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
            'review' => 'required'
        ]);
       
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $brands = Review::create($request->all());
        $reviews = Review::select('reviews.*','users.name')->leftJoin('users','reviews.user_id','=','users.id')->where('reviews.id',$brands->id)->where('reviews.status',1)->first();
        
        // return new AreaResource($brands);
        $response = ['status'=>true,'message'=>'Added Successfully', 'data'=>$reviews];
        return response($response, 200);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Review  $area
     * @return \Illuminate\Http\Response
     */
    public function show(Review $area)
    {
        $area = Review::find($area->id);
      
        if (is_null($area)) {
            return $this->sendError('Brand not found.');
        }
        $response = ['status'=>true, 'data'=>$area];
        return response($response, 200);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Review  $area
     * @return \Illuminate\Http\Response
     */
    public function edit(Review $area)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Review  $area
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
        $area=Review::find($id);
        $area->update($request->all());
        
        return new AreaResource($area);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Review  $area
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $area=Review::find($id);
        $area->delete();
        $response = ['status'=>true,'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
