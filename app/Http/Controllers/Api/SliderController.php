<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HighlightTypeResource;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sliders = Slider::all();
        return HighlightTypeResource::collection($sliders);
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
            // 'use_type_id' => 'required'
        ]);
        $sliderImage = '';
        if ($request->hasFile('logo')) {
            $sliderImage = time() . '.' . $request->logo->extension();
            
            $request->logo->move(public_path('uploads'), $sliderImage);
            
            $request = new Request($request->all());
            $request->merge(['logo' => $sliderImage]);
        }

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $brands = Slider::create($request->all());
        
        return new HighlightTypeResource($brands);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\Response
     */
    public function show(Slider $slider)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\Response
     */
    public function edit(Slider $slider)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $extraShippCost = Slider::find($id);
        $validator = Validator::make($request->all(), [
            // 'use_type_id' => 'required'
        ]);
        $sliderImage = $request->logo;
        if ($request->hasFile('logo')) {
            $sliderImage = time() . '.' . $request->logo->extension();
            
            $request->logo->move(public_path('uploads'), $sliderImage);
            
            $request = new Request($request->all());
            $request->merge(['logo' => $sliderImage]);
        }
       
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $extraShippCost->update($request->all());
        
        return new HighlightTypeResource($extraShippCost);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $extraShippCost = Slider::find($id);
        $extraShippCost->delete();
        $response = ['status'=>true,'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
