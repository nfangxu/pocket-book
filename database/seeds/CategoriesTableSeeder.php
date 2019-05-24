<?php

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            '饮食' => '正常吃饭',
            '交通' => '上下班路费',
            '购物' => '网上购物等',
            '社交' => '请人吃饭等',
            '其他' => "自定义",
        ];

        foreach ($categories as $category => $comment) {
            Category::create([
                'name' => $category,
                'comment' => $comment,
            ]);
        }
    }
}
