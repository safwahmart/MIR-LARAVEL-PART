<?php

namespace App\Http\Controllers;

use App\Http\Resources\AreaResource;
use App\Library\SslCommerz\SslCommerzNotification;
use App\Models\Appointment;
use App\Models\Area;
use App\Models\Article;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\Category;
use App\Models\CorporateForm;
use App\Models\Country;
use App\Models\Customer;
use App\Models\District;
use App\Models\Feedback;
use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderByPicture;
use App\Models\OrderProduct;
use App\Models\Page;
use App\Models\Payment;
use App\Models\PopupNotification;
use App\Models\Product;
use App\Models\Review;
use App\Models\Slider;
use App\Models\SocialLink;
use App\Models\Settings;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\SupplyRequest;
use App\Models\User;
use App\Models\WishList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function getCategory()
    {
        // $areas = Category::select('cat.*','categories.name as child_name','categories.name_bn as child_name_bn','categories.id as child_id','categories.slug as child_slug')->addSelect([
        //     // Key is the alias, value is the sub-select
        //     'total_product' => Product::query()
        //         // You can use eloquent methods here
        //         ->select(DB::raw('count(*) as value'))
        //         ->whereColumn('category_id', 'categories.id')
        //         ->groupBy('category_id')
        //         ->take(1)
        // ])->leftJoin('categories as cat','cat.id','=','categories.parent_id')->groupBy('cat.parent_id')->where('cat.status',1)->latest()->get();
        $categories = [];
        $areas = Category::addSelect([
            // Key is the alias, value is the sub-select
            'total_product' => Product::query()
                // You can use eloquent methods here
                ->select(DB::raw('count(*) as value'))
                ->whereColumn('category_id', 'categories.id')
                ->groupBy('category_id')
                ->take(1)
        ])->where('status', 1)->where('parent_id', null)->latest()->get();

        foreach ($areas as $i => $area) {
            $areas[$i]['child'] = Category::addSelect([
                // Key is the alias, value is the sub-select
                'total_product' => Product::query()
                    // You can use eloquent methods here
                    ->select(DB::raw('count(*) as value'))
                    ->whereColumn('category_id', 'categories.id')
                    ->groupBy('category_id')
                    ->take(1)
            ])->where('status', 1)->where('parent_id', $area->id)->latest()->get();
        }
        return AreaResource::collection($areas);
    }
    public function getOffers()
    {
        $areas = Offer::where('status', 1)->latest()->get();
        return AreaResource::collection($areas);
    }
    public function getSliders()
    {
        $areas = Slider::where('status', 1)->latest()->get();
        return AreaResource::collection($areas);
    }
    public function getPopup()
    {
        $areas = PopupNotification::where('status', 1)->latest()->first();
        // return AreaResource::collection($areas);
        return $areas;
    }
    public function getProducts()
    {
        $areas = Product::select('products.*', 'brands.name as brand_name', 'brands.name_bn as brand_name_bn', 'categories.name as category_name','categories.name_bn as category_name_bn', 'units.name as unit_name', 'units.name_bn as unit_name_bn')->addSelect([
            // Key is the alias, value is the sub-select
            'stock_in' => StockIn::query()
                // You can use eloquent methods here
                ->select(DB::raw('sum(qty) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->addSelect([
            // Key is the alias, value is the sub-select
            'stock_out' => StockOut::query()
                // You can use eloquent methods here
                ->select(DB::raw('sum(qty) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->addSelect([
            // Key is the alias, value is the sub-select
            'rating' => Review::query()
                // You can use eloquent methods here
                ->select(DB::raw('avg(rating) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->selectRaw('(SELECT GROUP_CONCAT(image_name) FROM product_images WHERE product_images.product_id =products.id) AS product_images')->selectRaw('(SELECT GROUP_CONCAT(name) FROM tags WHERE tags.id IN (product_tag)) AS tags')->leftJoin('categories', 'categories.id', '=', 'products.category_id')->leftJoin('brands', 'brands.id', '=', 'products.brand_id')->leftJoin('units', 'units.id', '=', 'products.unit_id')->where('products.status', 1)->latest()->get();
        return AreaResource::collection($areas);
    }
    public function getProductId($slug)
    {
        $areas = Product::select('products.*', 'brands.name as brand_name', 'brands.name_bn as brand_name_bn', 'categories.name as category_name','categories.name_bn as category_name_bn', 'units.name as unit_name', 'units.name_bn as unit_name_bn')->addSelect([
            // Key is the alias, value is the sub-select
            'stock_in' => StockIn::query()
                // You can use eloquent methods here
                ->select(DB::raw('sum(qty) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->addSelect([
            // Key is the alias, value is the sub-select
            'stock_out' => StockOut::query()
                // You can use eloquent methods here
                ->select(DB::raw('sum(qty) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->addSelect([
            // Key is the alias, value is the sub-select
            'rating' => Review::query()
                // You can use eloquent methods here
                ->select(DB::raw('avg(rating) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->selectRaw('(SELECT GROUP_CONCAT(image_name) FROM product_images WHERE product_images.product_id =products.id) AS product_images')->selectRaw('(SELECT GROUP_CONCAT(name) FROM tags WHERE tags.id IN (product_tag)) AS tags')->addSelect([
            // Key is the alias, value is the sub-select
            'stock_in' => StockIn::query()
                // You can use eloquent methods here
                ->select(DB::raw('sum(qty) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->addSelect([
            // Key is the alias, value is the sub-select
            'stock_out' => StockOut::query()
                // You can use eloquent methods here
                ->select(DB::raw('sum(qty) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->addSelect([
            // Key is the alias, value is the sub-select
            'rating' => Review::query()
                // You can use eloquent methods here
                ->select(DB::raw('avg(rating) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->leftJoin('categories', 'categories.id', '=', 'products.category_id')->leftJoin('brands', 'brands.id', '=', 'products.brand_id')->leftJoin('units', 'units.id', '=', 'products.unit_id')->where('products.product_slug', $slug)->where('products.status', 1)->latest()->first();
        return $areas;
    }
    public function getProductsByOffer($slug)
    {
        $areas = Product::select('products.*', 'brands.name as brand_name', 'brands.name_bn as brand_name_bn', 'categories.name as category_name','categories.name_bn as category_name_bn', 'units.name as unit_name', 'units.name_bn as unit_name_bn')->addSelect([
            // Key is the alias, value is the sub-select
            'stock_in' => StockIn::query()
                // You can use eloquent methods here
                ->select(DB::raw('sum(qty) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->addSelect([
            // Key is the alias, value is the sub-select
            'stock_out' => StockOut::query()
                // You can use eloquent methods here
                ->select(DB::raw('sum(qty) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->addSelect([
            // Key is the alias, value is the sub-select
            'rating' => Review::query()
                // You can use eloquent methods here
                ->select(DB::raw('avg(rating) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->selectRaw('(SELECT GROUP_CONCAT(image_name) FROM product_images WHERE product_images.product_id =products.id) AS product_images')->selectRaw('(SELECT GROUP_CONCAT(name) FROM tags WHERE tags.id IN (product_tag)) AS tags')->leftJoin('categories', 'categories.id', '=', 'products.category_id')->leftJoin('brands', 'brands.id', '=', 'products.brand_id')->leftJoin('units', 'units.id', '=', 'products.unit_id')->where('products.product_slug', $slug)->where('products.status', 1)->latest()->get();
        return $areas;
    }
    public function getProductsByBrand($slug)
    {
        $areas = Product::select('products.*', 'brands.name as brand_name', 'brands.name_bn as brand_name_bn', 'categories.name as category_name','categories.name_bn as category_name_bn', 'units.name as unit_name', 'units.name_bn as unit_name_bn')->addSelect([
            // Key is the alias, value is the sub-select
            'stock_in' => StockIn::query()
                // You can use eloquent methods here
                ->select(DB::raw('sum(qty) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->addSelect([
            // Key is the alias, value is the sub-select
            'stock_out' => StockOut::query()
                // You can use eloquent methods here
                ->select(DB::raw('sum(qty) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->addSelect([
            // Key is the alias, value is the sub-select
            'rating' => Review::query()
                // You can use eloquent methods here
                ->select(DB::raw('avg(rating) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->selectRaw('(SELECT GROUP_CONCAT(image_name) FROM product_images WHERE product_images.product_id =products.id) AS product_images')->selectRaw('(SELECT GROUP_CONCAT(name) FROM tags WHERE tags.id IN (product_tag)) AS tags')->leftJoin('categories', 'categories.id', '=', 'products.category_id')->leftJoin('brands', 'brands.id', '=', 'products.brand_id')->leftJoin('units', 'units.id', '=', 'products.unit_id')->where('brands.slug', $slug)->where('products.status', 1)->latest()->get();
        return AreaResource::collection($areas);
    }
    public function getProductsByCategory($slug)
    {
        $areas = Product::select('products.*', 'brands.name as brand_name', 'brands.name_bn as brand_name_bn', 'categories.name as category_name','categories.name_bn as category_name_bn', 'units.name as unit_name', 'units.name_bn as unit_name_bn')->addSelect([
            // Key is the alias, value is the sub-select
            'stock_in' => StockIn::query()
                // You can use eloquent methods here
                ->select(DB::raw('sum(qty) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->addSelect([
            // Key is the alias, value is the sub-select
            'stock_out' => StockOut::query()
                // You can use eloquent methods here
                ->select(DB::raw('sum(qty) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->addSelect([
            // Key is the alias, value is the sub-select
            'rating' => Review::query()
                // You can use eloquent methods here
                ->select(DB::raw('avg(rating) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->selectRaw('(SELECT GROUP_CONCAT(image_name) FROM product_images WHERE product_images.product_id =products.id) AS product_images')->selectRaw('(SELECT GROUP_CONCAT(name) FROM tags WHERE tags.id IN (product_tag)) AS tags')->leftJoin('categories', 'categories.id', '=', 'products.category_id')->leftJoin('brands', 'brands.id', '=', 'products.brand_id')->leftJoin('units', 'units.id', '=', 'products.unit_id')->where('categories.slug', $slug)->where('products.status', 1)->latest()->get();
        return AreaResource::collection($areas);
    }
    public function getBanners()
    {
        $areas = Banner::where('status', 1)->latest()->get();
        return AreaResource::collection($areas);
    }
    public function getBrands()
    {
        $areas = Brand::addSelect([
            // Key is the alias, value is the sub-select
            'total_product' => Product::query()
                // You can use eloquent methods here
                ->select(DB::raw('count(*) as value'))
                ->whereColumn('brand_id', 'brands.id')
                ->groupBy('brand_id')
                ->take(1)
        ])->where('status', 1)->latest()->get();
        return AreaResource::collection($areas);
    }
    public function getArticles()
    {
        $areas = Article::where('status', 1)->latest()->get();
        return AreaResource::collection($areas);
    }
    public function getArticlesRandom($id)
    {
        $areas = Article::inRandomOrder()->where('status', 1)->where('id','!=',$id)->limit(3)->latest()->get();
        return AreaResource::collection($areas);
    }
    public function getSocialLinks()
    {
        $areas = SocialLink::where('status', 1)->latest()->get();
        return AreaResource::collection($areas);
    }
    public function getPages()
    {
        $areas = Page::where('status', 1)->orderBy('serial','asc')->get();
        return AreaResource::collection($areas);
    }
    public function getCountries()
    {
        $areas = Country::latest()->get();
        return AreaResource::collection($areas);
    }
    public function getFeedbacks()
    {
        $areas = Feedback::latest()->get();
        return AreaResource::collection($areas);
    }
    public function getDistricts()
    {
        $areas = District::where('status', 1)->latest()->get();
        return AreaResource::collection($areas);
    }
    public function getAreaByDistrict($id)
    {
        $areas = Area::where('district_id', $id)->where('status', 1)->latest()->get();
        return AreaResource::collection($areas);
    }
    public function getSettings()
    {
        $areas = Settings::first();
        return $areas;
    }
    public function getArticleBySlug($slug)
    {
        $areas = Article::where('status', 1)->where('slug', $slug)->first();
        return $areas;
    }
    public function getSubCategory($slug)
    {
        $category = Category::where('slug', $slug)->first();
        $areas = Category::where('parent_id', $category->id)->latest()->get();
        return AreaResource::collection($areas);
    }
    public function getPageBySlug($slug)
    {
        $category = Page::where('page_slug', $slug)->first();
        return $category;
    }

    public function saveCorporate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required'
        ]);
        $companyLogo = '';
        if ($request->hasFile('image')) {
            $companyLogo = time() . '.' . $request->image->extension();

            $request->image->move(public_path('uploads'), $companyLogo);

            $request = new Request($request->all());
            $request->merge(['image' => $companyLogo]);
        }

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $brands = CorporateForm::create($request->all());

        return new AreaResource($brands);
    }
    public function saveSupplyRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required'
        ]);
        $companyLogo = '';
        if ($request->hasFile('image')) {
            $companyLogo = time() . '.' . $request->image->extension();

            $request->image->move(public_path('uploads'), $companyLogo);

            $request = new Request($request->all());
            $request->merge(['image' => $companyLogo]);
        }

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $brands = SupplyRequest::create($request->all());

        return new AreaResource($brands);
    }
    public function createOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required'
        ]);
        $post_data = array();

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $cart = Cart::where('user_id', $request->user_id)->get();
        if(isset($cart)){
        $customer_id = '';
        $checkCustomer = Customer::where('phone', $request->phone)->first();
        if (!isset($checkCustomer)) {
            $customer = Customer::create([
                'customer_type_id' => 1,
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'district' => $request->city,
                'area' => $request->area,
                'zip_code' => $request->zip_code,
            ]);

            $user = User::find($request->user_id);
            $user->update([
                'customer_id'=>$customer->id
            ]);

            # CUSTOMER INFORMATION
            $post_data['cus_name'] = $request->name;
            $post_data['cus_email'] = $request->email;
            $post_data['cus_add1'] = $request->address;
            $post_data['cus_add2'] = "";
            $post_data['cus_city'] = "";
            $post_data['cus_state'] = "";
            $post_data['cus_postcode'] = "";
            $post_data['cus_country'] = $request->country;
            $post_data['cus_phone'] = $request->phone;
            $post_data['cus_fax'] = "";
            $customer_id = $customer->id;
        } else {
            # CUSTOMER INFORMATION
            $post_data['cus_name'] = $checkCustomer->name;
            $post_data['cus_email'] = $checkCustomer->email;
            $post_data['cus_add1'] = $checkCustomer->address;
            $post_data['cus_add2'] = "";
            $post_data['cus_city'] = "";
            $post_data['cus_state'] = "";
            $post_data['cus_postcode'] = "";
            $post_data['cus_country'] = $checkCustomer->country;
            $post_data['cus_phone'] = $checkCustomer->phone;
            $post_data['cus_fax'] = "";
            $customer_id = $checkCustomer->id;
        }
        $orderLatest = Order::latest()->first();
        $invoiceNo = isset($orderLatest) ? date('ymd') . '0000' . $orderLatest->serial + 1 : date('ymd') . '00001';
        $order = Order::create([
            'invoice_no' => $invoiceNo,
            "customer_id" => $customer_id,
            "warehouse_id" => 0,
            "date" => Carbon::now(),
            "sale_by" => 0,
            "order_date" => Carbon::now(),
            // "delivery_date" => Carbon::now()->toDateTimeString(),
            // "time_slot_id" => 1,
            "discount_amount" => $request->discount,
            // "special_discount_amount" => 0,
            "order_note" => '',
            "sub_total" => $request->total_price + $request->discount,
            "vat" => $request->vat,
            "shipping_cost" => 100,
            "cod_charge" => 10,
            "rounding" => 0,
            "payable" => $request->total_price,
            'payment_type' => $request->plan,
            'source'=>1,
            'created_by'=>$request->user_id,
            'updated_by'=>$request->user_id,
            'serial' => isset($order) ? $order->serial + 1 : 1
        ]);
        foreach ($cart as $data) {
            OrderProduct::create([
                "order_id" => $order->id,
                "product_id" => $data->product_id,
                "unit_price" => $data->price/$data->qty,
                "variation_id" => 1,
                "qty" => $data->qty,
                "total_price" => $data->price,
            ]);
            StockOut::create([
                "stock_reason_id" => $order->id,
                "product_id" => $data->product_id,
                "variation_id" => 1,
                "qty" => $data->qty,
                "unit_price" => $data->price,
                "stock_reason" => 'purchase',
            ]);
        }
        # SHIPMENT INFORMATION
        $post_data['ship_name'] = "Store Test";
        $post_data['ship_add1'] = "Dhaka";
        $post_data['ship_add2'] = "Dhaka";
        $post_data['ship_city'] = "Dhaka";
        $post_data['ship_state'] = "Dhaka";
        $post_data['ship_postcode'] = "1000";
        $post_data['ship_phone'] = "";
        $post_data['ship_country'] = "Bangladesh";

        $post_data['shipping_method'] = "NO";
        $post_data['product_name'] = "Computer";
        $post_data['product_category'] = "Goods";
        $post_data['product_profile'] = "physical-goods";
        $post_data['total_amount'] = $request->total_price; # You cant not pay less than 10
        $post_data['currency'] = "BDT";
        $post_data['tran_id'] = uniqid(); // tran_id must be unique

        Payment::create([
            "invoice_no" => $invoiceNo,
            "user_id" => $request->user_id,
            "transaction_id" => $post_data['tran_id'],
            "paid_date" => Carbon::now(),
            "paid_amount" => $request->total_price,
        ]);

        # SHIPMENT INFORMATION
        // $post_data['ship_name'] = "Store Test";
        // $post_data['ship_add1'] = "Dhaka";
        // $post_data['ship_add2'] = "Dhaka";
        // $post_data['ship_city'] = "Dhaka";
        // $post_data['ship_state'] = "Dhaka";
        // $post_data['ship_postcode'] = "1000";
        // $post_data['ship_phone'] = "";
        // $post_data['ship_country'] = "Bangladesh";

        // $post_data['shipping_method'] = "NO";
        // $post_data['product_name'] = "Computer";
        // $post_data['product_category'] = "Goods";
        // $post_data['product_profile'] = "physical-goods";

        // # OPTIONAL PARAMETERS
        // $post_data['value_a'] = "ref001";
        // $post_data['value_b'] = "ref002";
        // $post_data['value_c'] = "ref003";
        // $post_data['value_d'] = "ref004";

        $sslc = new SslCommerzNotification();
        if ($request->plan == 1) {
            $payment_options = $sslc->makePayment($post_data, 'checkout', 'json');
            // return $payment_options;
            return response(['status' => true, 'redirect' => json_decode($payment_options), 'transaction_id' => $post_data['tran_id']], 200);
        }
        Cart::where('user_id', $request->user_id)->delete();
        return response(['status' => true], 200);
    }else{
        return response(['status' => false], 200);
    }

        // $brands = CorporateForm::create($request->all());

        // return new AreaResource($brands);
    }

    public function updatePayment(Request $request)
    {
        $payment = Payment::where('transaction_id', $request->transaction_id)->first();
        $payment->update([
            "status" => 1
        ]);
        $order = Order::where('invoice_no', $payment->invoice_no)->first();
        $order->update([
            "paid_amount" => $payment->paid_amount
        ]);
        return true;
    }

    public function sslcommerzSuccess(Request $request)
    {
        return $request;
    }
    public function saveOrderByPicture(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required'
        ]);
        $companyLogo = '';
        if ($request->hasFile('image')) {
            $companyLogo = time() . '.' . $request->image->extension();

            $request->image->move(public_path('uploads'), $companyLogo);

            $request = new Request($request->all());
            $request->merge(['image' => $companyLogo]);
        }

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $brands = OrderByPicture::create($request->all());

        return new AreaResource($brands);
    }
    public function saveAppointment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required'
        ]);
        $companyLogo = '';
        if ($request->hasFile('image')) {
            $companyLogo = time() . '.' . $request->image->extension();

            $request->image->move(public_path('uploads'), $companyLogo);

            $request = new Request($request->all());
            $request->merge(['image' => $companyLogo]);
        }

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $brands = Appointment::create($request->all());

        return new AreaResource($brands);
    }

    public function totalBrand(){
        $brands = Brand::where('status',1)->count();
        return $brands;
    }
    public function totalProduct(){
        $products = Product::where('status',1)->count();
        return $products;
    }
    public function popup(){
        $popup = PopupNotification::where('status',1)->first();
        return $popup;
    }
    public function newArrivals(){
        $products = Product::select('products.*', 'brands.name as brand_name', 'brands.name_bn as brand_name_bn', 'categories.name as category_name','categories.name_bn as category_name_bn', 'units.name as unit_name', 'units.name_bn as unit_name_bn')->addSelect([
            // Key is the alias, value is the sub-select
            'stock_in' => StockIn::query()
                // You can use eloquent methods here
                ->select(DB::raw('sum(qty) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->addSelect([
            // Key is the alias, value is the sub-select
            'stock_out' => StockOut::query()
                // You can use eloquent methods here
                ->select(DB::raw('sum(qty) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->addSelect([
            // Key is the alias, value is the sub-select
            'rating' => Review::query()
                // You can use eloquent methods here
                ->select(DB::raw('avg(rating) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->selectRaw('(SELECT GROUP_CONCAT(image_name) FROM product_images WHERE product_images.product_id =products.id) AS product_images')->selectRaw('(SELECT GROUP_CONCAT(name) FROM tags WHERE tags.id IN (product_tag)) AS tags')->leftJoin('categories', 'categories.id', '=', 'products.category_id')->leftJoin('brands', 'brands.id', '=', 'products.brand_id')->leftJoin('units', 'units.id', '=', 'products.unit_id')->where('products.status',1)->latest()->take(8)->get();
        return $products;
    }
    public function getCategoryWiseProduct($id,$product_id){
        $products = Product::select('products.*', 'brands.name as brand_name', 'brands.name_bn as brand_name_bn', 'categories.name as category_name','categories.name_bn as category_name_bn', 'units.name as unit_name', 'units.name_bn as unit_name_bn')->addSelect([
            // Key is the alias, value is the sub-select
            'stock_in' => StockIn::query()
                // You can use eloquent methods here
                ->select(DB::raw('sum(qty) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->addSelect([
            // Key is the alias, value is the sub-select
            'stock_out' => StockOut::query()
                // You can use eloquent methods here
                ->select(DB::raw('sum(qty) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->addSelect([
            // Key is the alias, value is the sub-select
            'rating' => Review::query()
                // You can use eloquent methods here
                ->select(DB::raw('avg(rating) as value'))
                ->whereColumn('product_id', 'products.id')
                ->groupBy('product_id')
                ->take(1)
        ])->selectRaw('(SELECT GROUP_CONCAT(image_name) FROM product_images WHERE product_images.product_id =products.id) AS product_images')->selectRaw('(SELECT GROUP_CONCAT(name) FROM tags WHERE tags.id IN (product_tag)) AS tags')->leftJoin('categories', 'categories.id', '=', 'products.category_id')->leftJoin('brands', 'brands.id', '=', 'products.brand_id')->leftJoin('units', 'units.id', '=', 'products.unit_id')->where('products.status',1)->where('products.category_id',$id)->where('products.id','!=',$product_id)->latest()->take(8)->get();
        return $products;
    }
    public function totalOrderForUser($id){
        $user = User::find($id);
        $order = Order::where('customer_id',$user->customer_id)->count();
        return $order;
    }
    public function totalPriceForUser($id){
        $user = User::find($id);
        $order = Order::where('customer_id',$user->customer_id)->sum('payable');
        return $order;
    }
    public function customerForUser($id){
        $user = User::find($id);
        $customer = Customer::where('id',$user->customer_id)->first();
        return $customer;
    }
    public function allOrderForUser($id){
        $user = User::find($id);
        $order = Order::addSelect([
            // Key is the alias, value is the sub-select
            'total_qty' => OrderProduct::query()
                // You can use eloquent methods here
                ->select(DB::raw('sum(qty) as value'))
                ->whereColumn('order_id', 'orders.id')
                ->groupBy('order_id')
                ->take(1)
        ])->where('customer_id',$user->customer_id)->get();
        return $order;
    }

    public function reviewListsByUser($id)
    {
        $articles = Review::select('reviews.*','users.name')->leftJoin('users','reviews.user_id','=','users.id')->where('product_id',$id)->where('reviews.status',1)->get();
        return AreaResource::collection($articles);
    }
}
