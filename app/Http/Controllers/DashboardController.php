<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Pocket;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $start = date('Y-m-d', strtotime('first day of this month'));
        $end = date('Y-m-d', strtotime('last day of this month'));

        $date = $request->input('date');

        $dates = explode('~', $date);

        if ($dates) {
            if (isset($dates[0]) && isset($dates[1])) {
                $start = $dates[0];
                $end = $dates[1];
            }
        }

        $categories = Category::all();

        $pockets = Pocket::select(DB::raw('sum(expenditure) as total,category_id'))
            ->datepicker($start, $end)
            ->groupBy('category_id')
            ->get()->pluck('total', 'category_id')->toArray();

        $titles = [];
        $data = [];
        foreach ($categories as $category) {
            $name = $category->ext_name ?: $category->name;
            $titles[] = $name;
            $data[] = [
                'name' => $name,
                'value' => $pockets[$category->id] ?? 0,
            ];
        }

        return view('dashboard', compact(['titles', 'data', 'start', 'end']));
    }
}
