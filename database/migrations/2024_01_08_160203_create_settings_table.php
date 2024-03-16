<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('hotline')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('bill_footer')->nullable();
            $table->string('bin')->nullable();
            $table->string('musak')->nullable();
            $table->string('company)_logo')->nullable();
            $table->string('favicon_icon')->nullable();
            $table->string('website_link')->nullable();
            $table->string('google_map')->nullable();
            $table->string('app_link')->nullable();
            $table->string('ios_link')->nullable();
            $table->string('meta_keyword')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('login_meta_title')->nullable();
            $table->text('login_meta_description')->nullable();
            $table->text('login_alt_text')->nullable();
            $table->string('registration_meta_title')->nullable();
            $table->text('registration_meta_description')->nullable();
            $table->text('registration_alt_text')->nullable();
            $table->text('company_logo_alt_text')->nullable();
            $table->text('top_advertisement')->nullable();
            $table->double('minimum_order_amount')->nullable();
            $table->double('maximum_order_amount')->nullable();
            $table->string('sender_name')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('mail_mailer')->nullable();
            $table->string('mail_host')->nullable();
            $table->string('mail_port')->nullable();
            $table->string('mail_encryption')->nullable();
            $table->string('mail_username')->nullable();
            $table->string('mail_password')->nullable();
            $table->string('theme_color')->nullable();
            $table->integer('product_show_per_page')->nullable();
            $table->boolean('cart_on_top')->nullable()->default(0);
            $table->boolean('shop_on_top')->nullable()->default(0);
            $table->boolean('brand_on_top')->nullable()->default(0);
            $table->boolean('wishlist_on_top')->nullable()->default(0);
            $table->boolean('notice_on_top')->nullable()->default(0);
            $table->boolean('offer_price')->nullable()->default(0);
            $table->boolean('app_price')->nullable()->default(0);
            $table->boolean('quantity')->nullable()->default(0);
            $table->boolean('discount')->nullable()->default(0);
            $table->boolean('flat_discount')->nullable()->default(0);
            $table->boolean('cart_on_right_side')->nullable()->default(0);
            $table->string('loading_image')->nullable();
            $table->boolean('home_page_slider_section')->nullable()->default(0);
            $table->string('advertise_two_section')->nullable()->default(0);
            $table->boolean('product_category_section')->nullable()->default(0);
            $table->boolean('three_forms_photos')->nullable()->default(0);
            $table->boolean('highlighted_product_section')->nullable()->default(0);
            $table->boolean('highlighted_brand_section')->nullable()->default(0);
            $table->boolean('testimonial_section')->nullable()->default(0);
            $table->boolean('article_section')->nullable()->default(0);
            $table->boolean('subscriber_section')->nullable()->default(0);
            $table->boolean('services_section')->nullable()->default(0);
            $table->boolean('time_slot')->nullable()->default(0);           
            $table->boolean('delivery_date')->nullable()->default(0);           
            $table->boolean('header_home')->nullable()->default(0);           
            $table->boolean('header_app')->nullable()->default(0);           
            $table->boolean('header_help')->nullable()->default(0);           
            $table->boolean('header_track_order')->nullable()->default(0);           
            $table->boolean('header_supply_product')->nullable()->default(0);           
            $table->boolean('header_delivery_time')->nullable()->default(0);           
            $table->boolean('header_language')->nullable()->default(0);           
            $table->boolean('header_sign_in_option')->nullable()->default(0);           
            $table->boolean('download_google_app')->nullable()->default(0);           
            $table->boolean('download_ios_app')->nullable()->default(0);           
            $table->boolean('facebook_login')->nullable()->default(0);           
            $table->boolean('google_login')->nullable()->default(0);           
            $table->boolean('point')->nullable()->default(0);           
            $table->boolean('return_order')->nullable()->default(0);           
            $table->boolean('wallet')->nullable()->default(0);            
            $table->string('product_hover_color')->nullable();
            $table->integer('notification_popup_display_limit')->nullable();
            $table->boolean('brand_active')->nullable()->default(0);   
            $table->boolean('delivery_date_time_in_print')->nullable()->default(0);   
            $table->boolean('sale_by')->nullable()->default(0);   
            $table->boolean('bin_musak_print')->nullable()->default(0);   
            $table->boolean('cod_charge_print')->nullable()->default(0);   
            $table->boolean('all_product_category')->nullable()->default(0);   
            $table->double('point_rate')->nullable();
            $table->double('minimum_point_for_withdraw')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
