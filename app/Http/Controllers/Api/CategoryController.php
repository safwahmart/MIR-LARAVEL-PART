<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categorys = Category::all();
        return CategoryResource::collection($categorys);
    }
    public function categoryForProduct()
    {
        $categorys = Category::where('status',1)->latest()->get();
        return CategoryResource::collection($categorys);
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
            'name' => 'required|unique:categories'
        ]);
        $fileNameCategory= '';
        $fileNameIcon= '';
        $fileNameBanner= '';
        if ($request->category_image) {
            $fileNameCategory = time() . '.' . $request->category_image->extension();

            $request->category_image->move(public_path('uploads'), $fileNameCategory);
        }
        if ($request->icon) {
            $fileNameIcon = time() . '.' . $request->icon->extension();

            $request->icon->move(public_path('uploads'), $fileNameIcon);
        }
        if ($request->banner) {
            $fileNameBanner = time() . '.' . $request->banner->extension();

            $request->banner->move(public_path('uploads'), $fileNameBanner);
        }
       
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $brands = Category::create([            
            "name"=>$request->name,
            "name_bn"=>$request->name_bn,
            "slug"=>$request->slug,
            "slug_bn"=>$request->slug_bn,
            "title"=>$request->title,
            "title_bn"=>$request->title_bn,
            "parent_id"=>$request->parent_id,
            "product_type_id"=>$request->product_type_id,
            "meta_title"=>$request->meta_title,
            "meta_desc"=>$request->meta_desc,
            "alt_text"=>$request->alt_text,
            "alt_text_bn"=>$request->alt_text_bn,
            "image"=> $fileNameCategory,
            "icon"=> $fileNameIcon,
            "banner_image"=> $fileNameBanner,
            "serial_number"=> $request->serial_number,
        ]);
        
        return new CategoryResource($brands);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        $category = Category::find($category->id);
      
        if (is_null($category)) {
            return $this->sendError('Brand not found.');
        }
        $response = ['status'=>true, 'data'=>$category];
        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        $category= Category::find($id);
        $fileNameCategory= $category->image;
        $fileNameIcon= $category->icon;
        $fileNameBanner= $category->banner_image;
        if ($request->hasFile('category_image')) {
            $fileNameCategory = time() . '.' . $request->category_image->extension();

            $request->category_image->move(public_path('uploads'), $fileNameCategory);
        }
        if ($request->hasFile('icon')) {
            $fileNameIcon = time() . '.' . $request->icon->extension();

            $request->icon->move(public_path('uploads'), $fileNameIcon);
        }
        if ($request->hasFile('banner')) {
            $fileNameBanner = time() . '.' . $request->banner->extension();

            $request->banner->move(public_path('uploads'), $fileNameBanner);
        }
       
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $category->update([            
            "name"=>$request->name,
            "name_bn"=>$request->name_bn,
            "slug"=>$request->slug,
            "slug_bn"=>$request->slug_bn,
            "title"=>$request->title,
            "title_bn"=>$request->title_bn,
            "parent_id"=>$request->parent_id,
            "product_type_id"=>$request->product_type_id,
            "meta_title"=>$request->meta_title,
            "meta_desc"=>$request->meta_desc,
            "alt_text"=>$request->alt_text,
            "alt_text_bn"=>$request->alt_text_bn,
            "image"=> $fileNameCategory,
            "icon"=> $fileNameIcon,
            "banner_image"=> $fileNameBanner,
            "serial_number"=> $request->serial_number,
            "status" => $request->status??$category->status,
            "show_menu" => $request->show_menu??$category->show_menu,
            "highlight" => $request->highlight??$category->highlight
        ]);
        
        return new CategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category= Category::find($id);
        $category->delete();
        $response = ['status'=>true,'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
