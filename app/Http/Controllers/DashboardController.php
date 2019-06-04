<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Pocket;
use Illuminate\Support\Facades\DB;
use App\User;

class DashboardController extends Controller
{
    protected $types = [
        'line' => '折线图', // 折线图
        'pie' => '饼图', // 饼图
    ];

    public function index(Request $request)
    {
        $type = $request->input('type');

        if (isset($this->types[$type])) {
            return $this->$type($request, $this->types);
        }

        return $this->pie($request, $this->types);
    }

    public function line(Request $request, $types)
    {
        $search = $request->all(['user']);
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

        $users = User::all();

        $pockets = Pocket::select(DB::raw('sum(expenditure) as total,category_id,expenditure_date'))
            ->datepicker($start, $end)
            ->search($search)
            ->groupBy('category_id', 'expenditure_date')
            ->orderBy('expenditure_date', 'desc')
            ->get();

        $xAxisData = []; // x 轴显示的内容 这里是日期
        $legendData = []; // 每条线代表的内容 这里是分类
        $series = []; // 详细内容

        $totals = [];
        foreach ($pockets as $pocket) {
            $totals[$pocket->category_id][$pocket->expenditure_date] = $pocket->total;
        }

        $xAxisData = $this->getDates(strtotime($start), strtotime($end));

        foreach ($categories as $category) {
            $tmp = [
                'name' => $category->ext_name ?: $category->name,
                'type' => 'line',
                'stack' => '总量',
                'data' => [],
            ];
            // 任何花费都没有的类别不显示
            if (isset($totals[$category->id])) {
                array_push($legendData, $tmp['name']);
                foreach ($xAxisData as $date) {
                    $tmp['data'][] = $totals[$category->id][$date] ?? 0; // 当天没有的花费默认补 0
                }
                array_push($series, $tmp);
            }
        }

        return view('dashboard-line', compact([
            'start',
            'end',
            'users',
            'xAxisData',
            'legendData',
            'series',
            'types',
        ]));
    }

    public function pie(Request $request, $types)
    {
        $search = $request->all(['user']);
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
            ->search($search)
            ->groupBy('category_id')
            ->get()->pluck('total', 'category_id')->toArray();

        $total = Pocket::select(DB::raw('sum(expenditure) as total'))
            ->datepicker($start, $end)
            ->search($search)
            ->pluck('total')->first();

        $titles = [];
        $data = [];
        foreach ($categories as $category) {
            $name = $category->ext_name ?: $category->name;
            $titles[] = $name;

            $cost = $pockets[$category->id] ?? 0;

            // 过滤掉花费为 0 的分类
            if (!$cost) {
                continue;
            }

            $data[] = [
                'name' => $name,
                'value' => $cost,
            ];
        }

        $users = User::all();

        return view('dashboard', compact([
            'titles',
            'data',
            'start',
            'end',
            'users',
            'total',
            'types',
        ]));
    }

    /**
     * @param int $start 开始时间戳
     * @param int $end 结束时间戳
     * @return array 日期集合
     */
    protected function getDates(int $start, int $end): array
    {
        $dates = [];

        do {
            array_push($dates, date("Y-m-d", $start));
            $start += 24 * 3600;
        } while ($end >= $start);

        return $dates;
    }
}
