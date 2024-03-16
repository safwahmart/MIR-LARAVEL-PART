<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Imports\VariationsImport;
use App\Models\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class VariationController extends Controller
{
    public function index()
    {
        $products = Variation::select('variations.*', 'product_name', 'product_name_bn')->leftJoin('products', 'products.id', '=', 'variations.product_upload_id')->latest()->get();
        return BrandResource::collection($products);
    }
    public function getVariation($id)
    {
        $products = Variation::select('variations.*', 'product_name', 'product_name_bn')->leftJoin('products', 'products.id', '=', 'variations.product_upload_id')->where('variations.product_upload_id', $id)->latest()->get();
        return BrandResource::collection($products);
    }
    public function store(Request $request)
    {
        if ($request->variation_upload_file) {
            $fileName = '';
            if ($request->variation_upload_file) {
                // $fileName = time() . '.' . $request->logo->extension();

                // $request->logo->move(public_path('uploads'), $fileName);
                Excel::import(new VariationsImport, $request->variation_upload_file);
            }

            // dd($fileName);
            $products = Variation::all();
            return new BrandResource($products);
        }else{
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:products'
            ]);
           
            if ($validator->fails()) {
                return response(['errors' => $validator->errors()->all()], 422);
            }
            $brands = Variation::create($request->all());
            
            return new BrandResource($brands);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
       
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $area = Variation::find($id);
        $area->update($request->all());
        
        return new BrandResource($area);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $area = Variation::find($id);
        $area->delete();
        $response = ['status'=>true,'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
