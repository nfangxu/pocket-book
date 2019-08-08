<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pocket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Pocket::latest('expenditure_date')
            ->latest('id')
            ->whereUserId((string)Auth::id());

        if (request('category')) {
            $query->whereCategoryId(request('category'));
        }

        $pockets = $query->paginate(10);
        $categories = Category::all();

        $extId = Category::extCategory();

        return view('home', compact(['pockets', 'categories', 'extId']));
    }

    public function store(Request $request)
    {
        $data = $request->only(['category', 'comment', 'expenditure', 'expenditure_date', 'is_necessary']);

        $user = Auth::user();

        /** 自定义类别 Start */
        $extId = Category::extCategory()[0];

        if ($data['category'] == $extId) {
            $category = Category::create([
                'name' => "其他",
                'ext_name' => $request->input('ext_category'),
                'comment' => ($user ? $user->name : '管理员') . " 添加于 " . now(),
            ]);
            $data['category'] = $category->id;
        }
        /** 自定义类别 End */

        $validator = Validator::make($data, [
            'category' => ['required'],
            'expenditure' => ['required'],
            'expenditure_date' => ['required'],
        ], [
            '*.required' =>  ':attribute不能为空',
        ], [
            'category' => '类别',
            'comment' => '备注',
            'expenditure' => '金额',
            'expenditure_date' => '日期',
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 1, 'data' => $validator->errors()]);
        }

        $data['user_id'] = Auth::id();

        $data['is_necessary'] = $data['is_necessary'] ? 1 : 0;
        $data['category_id'] = $data['category'];
        $data['comment'] = $data['comment'] ?: "";

        Pocket::create($data);

        return response()->json(['code' => 0]);
    }
}
