<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HighlightTypeResource;
use App\Models\WishList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WishListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = WishList::all();
        return HighlightTypeResource::collection($articles);
    }
    public function wishListsByUser($id,$productId)
    {
        $articles = WishList::where('user_id',$id)->where('product_id',$productId)->get();
        return HighlightTypeResource::collection($articles);
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

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $cart = WishList::where('product_id', $request->product_id)->where('user_id', $request->user_id)->first();
        if (isset($cart)) {
            $request->merge(['qty' => $cart->qty + 1]);
            $request->merge(['price' => $cart->price + $request->price]);
            $request->merge(['discount' => $cart->discount + $request->discount]);
            $request->merge(['vat' => $cart->vat + $request->vat]);
            $cart->update($request->all());
            return new HighlightTypeResource($cart);
        } else {
            $brands = WishList::create($request->all());
            return new HighlightTypeResource($brands);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WishList  $article
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $articles = WishList::select('products.*', 'brands.name as brand_name', 'categories.name as category_name', 'units.name as unit_name', 'qty as quantity')->selectRaw('(SELECT GROUP_CONCAT(image_name) FROM product_images WHERE product_images.product_id =products.id) AS product_images')->selectRaw('(SELECT GROUP_CONCAT(name) FROM tags WHERE tags.id IN (product_tag)) AS tags')->leftJoin('products', 'products.id', '=', 'wish_lists.product_id')->leftJoin('categories', 'categories.id', '=', 'products.category_id')->leftJoin('brands', 'brands.id', '=', 'products.brand_id')->leftJoin('units', 'units.id', '=', 'products.unit_id')->where('wish_lists.user_id', $id)->get();
        return HighlightTypeResource::collection($articles);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WishList  $article
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $articles = WishList::where('user_id', $id);
        return HighlightTypeResource::collection($articles);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WishList  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $extraShippCost = WishList::where('user_id', $request->user_id)->where('product_id', $request->product_id)->first();
        $validator = Validator::make($request->all(), [
            // 'use_type_id' => 'required'
        ]);
        if ($request->type === 'increment') {
            $request->merge(['qty' => $extraShippCost->qty + 1]);
            $request->merge(['price' => $extraShippCost->price + $request->price]);
            $request->merge(['discount' => $extraShippCost->discount + $request->discount]);
            $request->merge(['vat' => $extraShippCost->vat + $request->vat]);
        }
        if ($request->type === 'decrement') {
            $request->merge(['qty' => $extraShippCost->qty - 1]);
            $request->merge(['price' => $extraShippCost->price - $request->price]);
            $request->merge(['discount' => $extraShippCost->discount - $request->discount]);
            $request->merge(['vat' => $extraShippCost->vat - $request->vat]);
        }

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        if ($request->type === 'remove') {
            $extraShippCost->delete();
        } else {
            $extraShippCost->update($request->except('type'));
        }

        return new HighlightTypeResource($extraShippCost);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WishList  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $extraShippCost = WishList::where('product_id',$id);
        $extraShippCost->delete();
        $response = ['status' => true, 'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
