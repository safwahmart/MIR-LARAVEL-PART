<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product_types')->insert([
            [
                'name' => 'Fashion',
                'name_bn' => 'ফ্যাশন',
                'slug' => 'fashion',
                'slug_bn' => 'ফ্যাশন',
                'description' => '',
            ],
            [
                'name' => 'Medicine',
                'name_bn' => 'মেডিসিন',
                'slug' => 'medicine',
                'slug_bn' => 'মেডিসিন',
                'description' => '',
            ],
            [
                'name' => 'Healthcare',
                'name_bn' => 'স্বাস্থ্যসেবা',
                'slug' => 'healthcare',
                'slug_bn' => 'স্বাস্থ্যসেবা',
                'description' => '',
            ],
            [
                'name' => 'Book',
                'name_bn' => 'বই',
                'slug' => 'book',
                'slug_bn' => 'বই',
                'description' => '',
            ],
            [
                'name' => 'Food',
                'name_bn' => 'খাদ্য',
                'slug' => 'food',
                'slug_bn' => 'খাদ্য',
                'description' => '',
            ],
            [
                'name' => 'General',
                'name_bn' => 'জেনারেল',
                'slug' => 'general',
                'slug_bn' => 'জেনারেল',
                'description' => '',
            ]
        ]);
    }
}
