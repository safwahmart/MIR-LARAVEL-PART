<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // dd($row['country_id']);
        return new Product([
            "category_id" => $row['category_id'],
            "brand_id" => $row['brand_id'],
            "warehouse_id" => $row['warehouse_id'],
            "unit_id" => $row['unit_id'],
            "country_id" => $row['country_id'],
            "unit" => $row['unit'],
            "product_name" => $row['name'],
            "product_name_bn" => $row['name_bn'],
            "product_slug" => $row['slug'],
            "product_code" => $row['code'],
            "product_sku" => $row['sku'],
            "alert_quantity" => $row['alert_quantity'],
            "max_order_quantity" => $row['max_order_quantity'],
            "purchase_price" => $row['purchase_price'],
            "wholesale_price" => $row['wholesale_price'],
            "sale_price" => $row['sale_price'],
            "mfg_model_no" => $row['manufacture_model_no'],
            "barcode" => $row['barcode'],
            "weight" => $row['weight'],
            "vat" => $row['vat'],
            "vat_type" => $row['vat_type'],
            // "discount_flat" => $row['discount_flat'],
            // "discount" => $row['discount'],
            "video_link" => $row['video_link'],
            // "meta_title" => $row['meta_title'],
            // "expire_date" => $row['expire_date'],
            // "expire_note" => $row['expire_note'],
            "opening_qty" => $row['opening_qty'],
            "is_upload" => 1,
            // "meta_desc" => $row['meta_desc'],
            // "alt_text" => $row['alt_text'],
            // "desc" => $row['desc'],
            // "lot" => $row['lot'],
        ]);
    }
}
