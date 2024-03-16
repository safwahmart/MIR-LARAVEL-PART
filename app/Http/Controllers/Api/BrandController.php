<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Resources\BrandResource;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brands = Brand::all();
        return BrandResource::collection($brands);
    }
    public function brandForProduct()
    {
        $brands = Brand::where('status',1)->latest()->get();
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
            'name' => 'required|unique:brands',
            'slug' => 'required'
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
        $brands = Brand::create([
            "name"=>$request->name,
            "name_bn"=>$request->name_bn,
            "slug"=>$request->slug,
            "slug_bn"=>$request->slug_bn,
            "title"=>$request->title,
            "title_bn"=>$request->title_bn,
            "meta_title"=>$request->meta_title,
            "meta_desc"=>$request->meta_desc,
            "alt_text"=>$request->alt_text,
            "alt_text_bn"=>$request->alt_text_bn,
            "position"=>$request->position,
            "logo"=> $fileName,
        ]);

        return new BrandResource($brands);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function show(Brand $brand)
    {
        $brand = Brand::find($brand->id);

        if (is_null($brand)) {
            return $this->sendError('Brand not found.');
        }
        $response = ['status' => true, 'data' => $brand];
        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function edit(Brand $brand)
    {
        //
    }   

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required'
        ]);
        $brand = Brand::find($id);
        $fileName= $brand->logo;
        if ($request->hasFile('logo')) {
            $fileName = time() . '.' . $request->logo->extension();

            $request->logo->move(public_path('uploads'), $fileName);
        }
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $brand->update([
            "name"=>$request->name,
            "name_bn"=>$request->name_bn,
            "slug"=>$request->slug,
            "slug_bn"=>$request->slug_bn,
            "title"=>$request->title,
            "title_bn"=>$request->title_bn,
            "meta_title"=>$request->meta_title,
            "meta_desc"=>$request->meta_desc,
            "alt_text"=>$request->alt_text,
            "alt_text_bn"=>$request->alt_text_bn,
            "position"=>$request->position,
            "logo"=> $fileName,
            "status" => $request->status??$brand->status,
            "highlight" => $request->highlight??$brand->highlight,
            "show_menu" => $request->show_menu??$brand->show_menu
        ]);

        return new BrandResource($brand);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $brand = Brand::find($id);
        $brand->delete();
        $response = ['status' => true, 'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
