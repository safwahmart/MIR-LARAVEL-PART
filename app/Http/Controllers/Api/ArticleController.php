<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HighlightTypeResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = Article::all();
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
        $articleImage = '';
        if ($request->hasFile('image')) {
            $articleImage = time() . '.' . $request->image->extension();
            
            $request->image->move(public_path('uploads'), $articleImage);
            
            $request = new Request($request->all());
            $request->merge(['image' => $articleImage]);
        }

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $brands = Article::create($request->all());
        
        return new HighlightTypeResource($brands);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function edit(Article $article)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $extraShippCost = Article::find($id);
        $validator = Validator::make($request->all(), [
            // 'use_type_id' => 'required'
        ]);
        $articleImage = $request->image;
        if ($request->hasFile('image')) {
            $articleImage = time() . '.' . $request->image->extension();
            
            $request->image->move(public_path('uploads'), $articleImage);
            
            $request = new Request($request->all());
            $request->merge(['image' => $articleImage]);
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
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $extraShippCost = Article::find($id);
        $extraShippCost->delete();
        $response = ['status'=>true,'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
