<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HighlightTypeResource;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $extraShippCosts = Settings::all();
        return HighlightTypeResource::collection($extraShippCosts);
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
        $companyLogo = '';
        $loadingImage = '';
        $faviconImage = '';
        if ($request->hasFile('company_logo')) {
            $companyLogo = time() . '.' . $request->company_logo->extension();
            
            $request->company_logo->move(public_path('uploads'), $companyLogo);
            
            $request = new Request($request->all());
            $request->merge(['company_logo' => $companyLogo]);
        }
        if ($request->hasFile('favicon_icon')) {
            $faviconImage = time() . '.' . $request->favicon_icon->extension();
            
            $request->favicon_icon->move(public_path('uploads'), $faviconImage);
            
            $request = new Request($request->all());
            $request->merge(['favicon_icon' => $faviconImage]);
        }
        if ($request->hasFile('loading_image')) {
            $loadingImage = time() . '.' . $request->loading_image->extension();
            
            $request->loading_image->move(public_path('uploads'), $loadingImage);
            
            $request = new Request($request->all());
            $request->merge(['loading_image' => $loadingImage]);
        }

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $brands = Settings::create($request->all());
        
        return new HighlightTypeResource($brands);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Settings  $extraShippCost
     * @return \Illuminate\Http\Response
     */
    public function show(Settings $extraShippCost)
    {
        $extraShippCost = Settings::find($extraShippCost->id);
      
        if (is_null($extraShippCost)) {
            return $this->sendError('Brand not found.');
        }
        $response = ['status'=>true, 'data'=>$extraShippCost];
        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Settings  $extraShippCost
     * @return \Illuminate\Http\Response
     */
    public function edit(Settings $extraShippCost)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Settings  $extraShippCost
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $extraShippCost = Settings::find($id);
        $validator = Validator::make($request->all(), [
            // 'use_type_id' => 'required'
        ]);
        $companyLogo = $request->company_logo;
        $loadingImage = $request->loading_image;
        $faviconImage = $request->favicon_icon;
        if ($request->hasFile('company_logo')) {
            $companyLogo = time() . '.' . $request->company_logo->extension();
            
            $request->company_logo->move(public_path('uploads'), $companyLogo);
            
            $request = new Request($request->all());
            $request->merge(['company_logo' => $companyLogo]);
        }
        if ($request->hasFile('favicon_icon')) {
            $faviconImage = time() . '.' . $request->favicon_icon->extension();
            
            $request->favicon_icon->move(public_path('uploads'), $faviconImage);
            
            $request = new Request($request->all());
            $request->merge(['favicon_icon' => $faviconImage]);
        }
        if ($request->hasFile('loading_image')) {
            $loadingImage = time() . '.' . $request->loading_image->extension();
            
            $request->loading_image->move(public_path('uploads'), $loadingImage);
            
            $request = new Request($request->all());
            $request->merge(['loading_image' => $loadingImage]);
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
     * @param  \App\Models\Settings  $extraShippCost
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $extraShippCost = Settings::find($id);
        $extraShippCost->delete();
        $response = ['status'=>true,'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
