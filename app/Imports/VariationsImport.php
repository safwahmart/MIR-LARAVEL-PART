<?php

namespace App\Imports;

use App\Models\Variation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VariationsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // dd($row);
        return new Variation([
            "product_upload_id" => $row['product_upload_id'],
            "name" => $row['name'],
            "name_bn" => $row['name_bn'],
            "code" => $row['code'],
            "sku" => $row['sku'],
            "product_price" => $row['purchase_price'],
            "sale_price" => $row['sale_price'],
            "opening_quantity" => $row['opening_quantity'],
            "warehouse_id" => $row['warehouse_id'],
        ]);
    }
}
