<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Imports\ProductsImport;
use App\Models\Product;
use App\Models\ProductImages;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::select('products.*','brands.name as brand_name','brands.name_bn as brand_name_bn','categories.name as category_name','categories.name_bn as category_name_bn','product_types.name as product_type_name','product_types.name_bn as product_type_name_bn')->leftJoin('brands','brands.id','=','products.brand_id')->leftJoin('categories','categories.id','=','products.category_id')->leftJoin('product_types','product_types.id','=','categories.product_type_id')->latest()->get();
        return BrandResource::collection($products);
    }
    public function searchProduct(Request $request)
    {
        $product = Product::query();
        if ($request->brand_id) {
            $product->where('brand_id', $request->brand_id);
        }
        if ($request->category_id) {
            $product->where('category_id', $request->category_id);
        }
        if ($request->name) {
            $product->where('product_name', $request->name)->orWhere('product_sku', $request->name);
        }
        $products = $product->get();
        return BrandResource::collection($products);
        // dd($request);
        // $products = Product::latest()->get();
        // return BrandResource::collection($products);
    }
    public function allProduct()
    {
        $products = Product::select('products.*', 'categories.name as category_name', 'categories.name as category_name_bn', 'units.name as unit_name', 'units.name_bn as unit_name_bn','discounts.discount_percent','discounts.discount_flat')->leftJoin('categories', 'categories.id', '=', 'products.category_id')->leftJoin('discounts', 'discounts.product_id', '=', 'products.id')->leftJoin('units', 'units.id', '=', 'products.unit_id')->addSelect([
            // Key is the alias, value is the sub-select
            'stock_in' => StockIn::query()
                // You can use eloquent methods here
                ->select(DB::raw('sum(qty) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->limit(1)
        ])->addSelect([
            // Key is the alias, value is the sub-select
            'stock_out' => StockOut::query()
                // You can use eloquent methods here
                ->select(DB::raw('sum(qty) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->limit(1)
        ])->latest()->get();
        return BrandResource::collection($products);
    }
    public function getProductBarcode(Request $request)
    {
        $products = Product::select('products.*')->where('products.id', request('product_id'))
            ->leftJoin('variations', 'variations.product_upload_id', '=', 'products.id')->get();
        // $products->when(request('product_id'), function ($q) {
        //     return $q->where('products.id', request('product_id'));
        // });
        // $products->where('products.id',$request->product_id);
        // $products->get();
        // return $products;
        // dd($products);
        return BrandResource::collection($products);
    }
    public function getproductUploads()
    {
        $products = Product::where('is_upload', 1)->latest()->get();
        return BrandResource::collection($products);
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
            'product_name' => 'required',
            'category_id' => 'required',
            'unit' => 'required',
            'unit_id' => 'required',
        ]);
        $fileName = '';

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        // dd($fileName);
        $products = Product::create($request->all());

        return new BrandResource($products);
    }
    public function productUploads(Request $request)
    {
        $fileName = '';
        if ($request->product_upload_file) {
            // $fileName = time() . '.' . $request->logo->extension();

            // $request->logo->move(public_path('uploads'), $fileName);
            Excel::import(new ProductsImport, $request->product_upload_file);
        }

        // dd($fileName);
        $products = Product::all();
        return new BrandResource($products);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $product = Product::find($product->id);

        if (is_null($product)) {
            return $this->sendError('Product not found.');
        }
        $response = ['status' => true, 'data' => $product];
        return response($response, 200);
    }

    public function getProduct($id)
    {
        $area = Product::find($id);

        if (is_null($area)) {
            return $this->sendError('District not found.');
        }
        $response = ['status' => true, 'data' => $area];
        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        $validator = Validator::make($request->all(), [
            'product_name' => 'required',
            'category_id' => 'required',
            'unit' => 'required',
            'unit_id' => 'required',
        ]);
        $thumbnail_image = $product->thumbnail_image;
        if ($request->hasFile('product_thumbnail')) {
            $thumbnail_image = time() . '.' . $request->product_thumbnail->extension();

            $request->product_thumbnail->move(public_path('uploads'), $thumbnail_image);
        }
        $video_thumbnail = $product->video_thumbnail;
        if ($request->hasFile('video_thumbnail')) {
            $video_thumbnail = time() . '.' . $request->video_thumbnail->extension();

            $request->video_thumbnail->move(public_path('uploads'), $video_thumbnail);
        }
        if ($request->hasFile('product_multiple_images')) {
            // dd($_FILES);
            // $video_thumbnail = time() . '.' . $request->video_thumbnail->extension();

            // $request->video_thumbnail->move(public_path('uploads'), $video_thumbnail);
            ProductImages::where('product_id', $product->id)->delete();
            foreach ($request->file('product_multiple_images') as $key => $file) {
                // $name = $file->getClientOriginalName();
                $video_thumbnail = time() .$key. '.' . $file->extension();
                $file->move(public_path() . '/files/', $video_thumbnail);
                ProductImages::create([
                    "product_id" => $product->id,
                    "image_name" => $video_thumbnail,
                ]);
            }
        }
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        // $product->update($request->all());
        $product->update([
            "assign_user_id" => $request->assign_user_id,
            "hightlight_type_id" => $request->hightlight_type_id,
            "category_id" => $request->category_id,
            "brand_id" => $request->brand_id,
            "warehouse_id" => $request->warehouse_id,
            "unit_id" => $request->unit_id,
            "country_id" => $request->country_id,
            "product_tag" => $request->product_tag,
            "unit" => $request->unit,
            "product_name" => $request->product_name,
            "product_name_bn" => $request->product_name_bn,
            "product_slug" => $request->product_slug,
            "product_code" => $request->product_code,
            "product_sku" => $request->product_sku,
            "mfg_model_no" => $request->mfg_model_no,
            "barcode" => $request->barcode,
            "weight" => $request->weight,
            "alert_quantity" => $request->alert_quantity,
            "max_order_quantity" => $request->max_order_quantity,
            "purchase_price" => $request->purchase_price,
            "wholesale_price" => $request->wholesale_price,
            "sale_price" => $request->sale_price,
            "app_price" => $request->app_price,
            "vat" => $request->vat,
            "vat_type" => $request->vat_type,
            "discount_flat" => $request->discount_flat,
            "discount" => $request->discount,
            "video_link" => $request->video_link,
            "meta_title" => $request->meta_title,
            "expire_date" => $request->expire_date,
            "expire_note" => $request->expire_note,
            "opening_qty" => $request->opening_qty,
            "short_desc" => $request->short_desc,
            "meta_desc" => $request->meta_desc,
            "alt_text" => $request->alt_text,
            "desc" => $request->desc,
            "lot" => $request->lot,
            "video_thumbnail" => $video_thumbnail,
            "thumbnail_image" => $thumbnail_image,
            "status" => $request->status??$product->status,
        ]);

        return new BrandResource($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        $product->delete();
        $response = ['status' => true, 'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
