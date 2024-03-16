<?php

use App\Http\Controllers\Api\AccountGroupController;
use App\Http\Controllers\Api\AreaController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AttributeController;
use App\Http\Controllers\Api\AttributeTypeController;
use App\Http\Controllers\Api\BannerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CustomerTypeController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DeliveryDiscountController;
use App\Http\Controllers\Api\DeliveryManController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\DistrictController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\HighlightTypeController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\PassportAuthController;
use App\Http\Controllers\Api\PopupNotificationController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\ProductTypeController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\VariationController;
use App\Http\Controllers\Api\WareHouseController;
use App\Http\Controllers\Api\ExtraShipCostController;
use App\Http\Controllers\Api\FeedbackNotificationController;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\NewPasswordController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\PointController;
use App\Http\Controllers\Api\PosController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\RequestListController;
use App\Http\Controllers\Api\ReturnReasonController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\ShippCostDiscountController;
use App\Http\Controllers\Api\SliderController;
use App\Http\Controllers\Api\SocialLinkController;
use App\Http\Controllers\Api\TimeSlotController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WishListController;
use App\Http\Controllers\HomeController;
use App\Models\WishList;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('register', [PassportAuthController::class, 'register']);
Route::post('login', [PassportAuthController::class, 'login']);
Route::post('checkOtp', [PassportAuthController::class, 'checkOtp']);
Route::post('logout', [PassportAuthController::class, 'logout'])->middleware('auth:api');

Route::post('email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail']);
Route::get('verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify')->middleware('auth:sanctum');

// Route::post('forgot-password', [NewPasswordController::class, 'forgotPassword']);
Route::post('reset-password', [NewPasswordController::class, 'reset']);

Route::middleware('auth:api')->group(function () {
    Route::apiResource('brands', BrandController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('units', UnitController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('highlightTypes', HighlightTypeController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('attributeTypes', AttributeTypeController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('attributes', AttributeController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('tags', TagController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('units', UnitController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('customerTypes', CustomerTypeController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('productTypes', ProductTypeController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('customers', CustomerController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('categories', CategoryController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('districts', DistrictController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('areas', AreaController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('accountCharts', AreaController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('wareHouses', WareHouseController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('popupNotifications', PopupNotificationController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('products', ProductController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('offers', OfferController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('deliveryMans', DeliveryManController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('suppliers', SupplierController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('variations', VariationController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('discounts', DiscountController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('purchases', PurchaseController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('timeSlotes', TimeSlotController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('returnReasons', ReturnReasonController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('extraShippCosts', ExtraShipCostController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('shippingCostDiscounts', ShippCostDiscountController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('points', PointController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('users', UserController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('coupons', CouponController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('deliveryDiscounts', DeliveryDiscountController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('orders', OrderController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('settings', SettingController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('sales', PosController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('banners', BannerController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('sliders', SliderController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('articles', ArticleController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('socialLinks', SocialLinkController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('pages', PageController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('reviews', ReviewController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('requestLists', RequestListController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('feedbacks', FeedbackNotificationController::class)->except([
        'create', 'edit'
    ]);
    Route::apiResource('carts', CartController::class);
    Route::apiResource('wishLists', WishListController::class);
    Route::get('wishListsByUser/{id}/{productId}', [WishListController::class,'wishListsByUser']);
    Route::get('accountGroups', [AccountGroupController::class,'index']);
    Route::get('accountControls', [AccountGroupController::class,'getAccountControl']);
    Route::get('corporate', [FormController::class,'corporate']);
    Route::get('order_by_picture', [FormController::class,'order_by_picture']);
    Route::get('appointment', [FormController::class,'appointment']);
    Route::get('supplyRequest', [FormController::class,'supplyRequest']);
    Route::delete('supplyRequest/{id}', [FormController::class,'supplyRequestDelete']);
    Route::get('categoryForProduct', [CategoryController::class,'categoryForProduct']);
    Route::get('highlightTypeForProduct', [HighlightTypeController::class,'highlightTypeForProduct']);
    Route::get('getTagForProduct', [TagController::class,'getTagForProduct']);
    Route::get('customerTypeForProduct', [CustomerTypeController::class,'customerTypeForProduct']);
    Route::get('brandForProduct', [BrandController::class,'brandForProduct']);
    Route::get('unitForProduct', [UnitController::class,'unitForProduct']);
    Route::get('supplierForPurchase', [SupplierController::class,'supplierForPurchase']);
    Route::get('warehouseForPurchase', [WareHouseController::class,'warehouseForPurchase']);
    Route::get('offerForPurchase', [OfferController::class,'offerForPurchase']);
    Route::get('accountSubsidarys', [AccountGroupController::class,'getAccountSubsidary']);
    Route::get('getArea/{id}',[AreaController::class,'getArea']);
    Route::get('getCountry',[AreaController::class,'getCountry']);
    Route::get('getProduct/{id}',[ProductController::class,'getProduct']);
    Route::get('getPurchase/{id}',[PurchaseController::class,'getPurchase']);
    Route::get('getPurchaseProduct/{id}',[PurchaseController::class,'getPurchaseProduct']);
    Route::get('getOrder/{id}',[OrderController::class,'getOrder']);
    Route::get('getSale/{id}',[PosController::class,'getSale']);
    Route::get('getOrderProduct/{id}',[OrderController::class,'getOrderProduct']);
    Route::get('couriour',[OrderController::class,'couriour']);
    Route::get('redxTrackingById/{id}',[OrderController::class,'redxTrackingById']);
    Route::post('redxCreateOrder',[OrderController::class,'redxCreateOrder']);
    Route::post('statusUpdate',[OrderController::class,'statusUpdate']);
    Route::get('getSaleProduct/{id}',[PosController::class,'getSaleProduct']);
    Route::get('getVariation/{id}',[VariationController::class,'getVariation']);
    Route::get('getOfferSerial',[OfferController::class,'getOfferSerial']);
    Route::post('productUploads',[ProductController::class,'productUploads']);
    Route::get('getproductUploads',[ProductController::class,'getproductUploads']);
    Route::get('allProduct',[ProductController::class,'allProduct']);
    Route::get('totalCustomer',[DashboardController::class,'totalCustomer']);
    Route::get('totalProduct',[DashboardController::class,'totalProduct']);
    Route::get('totalSale',[DashboardController::class,'totalSale']);
    Route::get('todaySale',[DashboardController::class,'todaySale']);
    Route::get('totalOrder',[DashboardController::class,'totalOrder']);
    Route::get('saleBarcode',[DashboardController::class,'saleBarcode']);
    Route::get('totalWishList/{id}',[DashboardController::class,'totalWishList']);
    Route::post('searchProduct',[ProductController::class,'searchProduct']);
    Route::post('searchOrder',[OrderController::class,'searchOrder']);
    Route::post('searchTrack',[OrderController::class,'searchTrack']);
    Route::post('searchRequestList',[RequestListController::class,'searchRequestList']);
    Route::post('searchPurchase',[PurchaseController::class,'searchPurchase']);
    Route::post('getProduct',[ProductController::class,'getProductBarcode']);
    Route::post('updateCart',[CartController::class,'update']);
    Route::post('updateWishList',[WishListController::class,'update']);
    Route::post('updatePayment',[HomeController::class,'updatePayment']);
    Route::post('storePermission',[UserController::class,'storePermission']);
    Route::post('changePassword',[PassportAuthController::class,'change_password']);
    Route::get('totalOrderForUser/{id}', [HomeController::class, 'totalOrderForUser']);
    Route::get('totalPriceForUser/{id}', [HomeController::class, 'totalPriceForUser']);
    Route::get('customerForUser/{id}', [HomeController::class, 'customerForUser']);
    Route::get('allOrderForUser/{id}', [HomeController::class, 'allOrderForUser']);
    Route::get('getCustomerTypes', [CustomerController::class, 'getCustomerTypes']);
    Route::get('getCustomers', [CustomerController::class, 'getCustomers']);
    Route::put('purchaseStatus/{id}', [PurchaseController::class, 'purchaseStatus']);
});
Route::post('forgetPassPhone', [PassportAuthController::class, 'forgetPassPhone']);
Route::post('forgot_password', [PassportAuthController::class, 'forgot_password']);
Route::get('getCategories', [HomeController::class, 'getCategory']);
Route::get('reviewListsByUser/{id}', [HomeController::class,'reviewListsByUser']);
Route::get('getOffers', [HomeController::class, 'getOffers']);
Route::get('getSliders', [HomeController::class, 'getSliders']);
Route::get('getPopup', [HomeController::class, 'getPopup']);
Route::get('getProducts', [HomeController::class, 'getProducts']);
Route::get('getProductId/{slug}', [HomeController::class, 'getProductId']);
Route::get('getProductsByOffer/{slug}', [HomeController::class, 'getProductsByOffer']);
Route::get('getProductsByBrand/{slug}', [HomeController::class, 'getProductsByBrand']);
Route::get('getProductsByCategory/{slug}', [HomeController::class, 'getProductsByCategory']);
Route::get('getArticleBySlug/{slug}', [HomeController::class, 'getArticleBySlug']);
Route::get('getSubCategory/{slug}', [HomeController::class, 'getSubCategory']);
Route::get('getPageBySlug/{slug}', [HomeController::class, 'getPageBySlug']);
Route::get('getBanners', [HomeController::class, 'getBanners']);
Route::get('getBrands', [HomeController::class, 'getBrands']);
Route::get('getArticles', [HomeController::class, 'getArticles']);
Route::get('getArticlesRandom/{id}', [HomeController::class, 'getArticlesRandom']);
Route::get('getPages', [HomeController::class, 'getPages']);
Route::get('getFeedbacks', [HomeController::class, 'getFeedbacks']);
Route::post('saveAppointment', [HomeController::class, 'saveAppointment']);
Route::post('saveOrderByPicture', [HomeController::class, 'saveOrderByPicture']);
Route::post('saveCorporate', [HomeController::class, 'saveCorporate']);
Route::post('saveSupplyRequest', [HomeController::class, 'saveSupplyRequest']);
Route::post('createOrder', [HomeController::class, 'createOrder']);
Route::get('getSocialLinks', [HomeController::class, 'getSocialLinks']);
Route::get('getSettings', [HomeController::class, 'getSettings']);
Route::get('getCountries', [HomeController::class, 'getCountries']);
Route::get('getDistricts', [HomeController::class, 'getDistricts']);
Route::get('totalBrand', [HomeController::class, 'totalBrand']);
Route::get('totalProduct', [HomeController::class, 'totalProduct']);
Route::get('getCategoryWiseProduct/{id}/{product_id}', [HomeController::class, 'getCategoryWiseProduct']);
Route::get('popup', [HomeController::class, 'popup']);
Route::get('popup', [HomeController::class, 'popup']);
Route::get('newArrivals', [HomeController::class, 'newArrivals']);
Route::get('getAreaByDistrict/{id}', [HomeController::class, 'getAreaByDistrict']);
Route::get('/clear', function() {
    Artisan::call('clear-compiled');
    // Artisan::call('dump-autoload');
    return 'Application cache has been cleared';
});
