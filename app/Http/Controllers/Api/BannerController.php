<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\BrandResource;
use App\Models\Banner;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brands = Banner::all();
        return BrandResource::collection($brands);
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
            'banner_name' => 'required|unique:banners',
        ]);
        $fileName= '';
        if ($request->logo) {
            $fileName = time() . '.' . $request->logo->extension();

            $request->logo->move(public_path('uploads'), $fileName);
        }

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        // dd($fileName);
        // $brand->save();
        $brands = Banner::create([
            "banner_name"=>$request->banner_name,
            "banner_name_bn"=>$request->banner_name_bn,
            "banner_url"=>$request->slug,
            "meta_title"=>$request->meta_title,
            "meta_title_bn"=>$request->meta_title_bn,
            "meta_description"=>$request->meta_description,
            "meta_description_bn"=>$request->meta_description_bn,
            "alt_text"=>$request->alt_text,
            "alt_text_bn"=>$request->alt_text_bn,
            "image"=> $fileName,
        ]);

        return new BrandResource($brands);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Banner  $brand
     * @return \Illuminate\Http\Response
     */
    public function show(Banner $brand)
    {
        $brand = Banner::find($brand->id);

        if (is_null($brand)) {
            return $this->sendError('Banner not found.');
        }
        $response = ['status' => true, 'data' => $brand];
        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Banner  $brand
     * @return \Illuminate\Http\Response
     */
    public function edit(Banner $brand)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Banner  $brand
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Banner $brand)
    {
        $validator = Validator::make($request->all(), [
            'banner_name' => 'required',
        ]);
        $fileName= $brand->logo;
        if ($request->hasFile('logo')) {
            $fileName = time() . '.' . $request->logo->extension();

            $request->logo->move(public_path('uploads'), $fileName);
        }
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $brand->update([
            "banner_name"=>$request->banner_name,
            "banner_name_bn"=>$request->banner_name_bn,
            "banner_url"=>$request->slug,
            "meta_title"=>$request->meta_title,
            "meta_title_bn"=>$request->meta_title_bn,
            "meta_description"=>$request->meta_description,
            "meta_description_bn"=>$request->meta_description_bn,
            "alt_text"=>$request->alt_text,
            "alt_text_bn"=>$request->alt_text_bn,
            "image"=> $fileName,            
            "status" => $request->status??$brand->status,
        ]);

        return new BrandResource($brand);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Banner  $brand
     * @return \Illuminate\Http\Response
     */
    public function destroy(Banner $brand)
    {
        $brand->delete();
        $response = ['status' => true, 'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
