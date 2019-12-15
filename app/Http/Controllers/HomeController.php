<?php

namespace App\Http\Controllers;

use App\Helpers\T;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Pocket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();

        [$category, $start, $stop, $datepicker] = $this->params($request);

        $total = Pocket::datepicker($start, $stop)->search(['category' => $category])->sum('expenditure');

        return view('index', compact('categories', 'total', 'datepicker', 'category'));
    }

    public function store(Request $request)
    {
        $data = $request->all(['category', 'comment', 'expenditure', 'expenditure_date', 'is_necessary']);

        $validator = Validator::make($data, [
            'category' => ['required'],
            'expenditure' => ['required'],
            'expenditure_date' => ['required'],
        ], [
            '*.required' => ':attribute不能为空',
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

    public function page(Request $request)
    {
        [$category, $start, $stop, $datepicker] = $this->params($request);

        $limit = $request->input('limit', 15);

        return Pocket::query()
            ->datepicker($start, $stop)
            ->search(['category' => $category, 'user' => Auth::id()])
            ->orderByDesc('expenditure_date')
            ->orderByDesc('id')
            ->paginate($limit);
    }

    public function chart(Request $request)
    {
        [$categoryId, $start, $stop, $datepicker] = $this->params($request);

        $category = Pocket::query()
            ->datepicker($start, $stop)
            ->search(['category' => $categoryId])
            ->groupBy('category_id')
            ->orderByDesc('expenditure')
            ->get(['category_id', DB::raw('sum(expenditure) as expenditure')]);

        $pockets = Pocket::query()
            ->datepicker($start, $stop)
            ->search(['category' => $categoryId])
            ->groupBy('expenditure_date')
            ->get(['expenditure_date', DB::raw('sum(expenditure) as expenditure')])
            ->pluck('expenditure', 'expenditure_date')->toArray();

        $legend = $category->pluck('category_name');

        $dates = T::dates($start, $stop);

        $data = [
            [
                'name' => '总计',
                'type' => 'line',
                'stack' => '总计',
                'label' => [
                    'show' => true,
                    'position' => 'right',
                ],
                'data' => [],
            ],
        ];

        foreach ($dates as $date) {
            $data[0]['data'][] = $pockets[$date] ?? 0;
        }

        foreach ($category as $item) {
            $tmp = [
                'name' => $item->category_name,
                'type' => 'bar',
                'stack' => '分类',
                'xAxisIndex' => 1,
                'data' => [],
            ];

            foreach ($dates as $date) {
                // 总计里面有数据再去数据库里查, 没有直接赋值给 0
                if (isset($pockets[$date])) {
                    $tmp['data'][] = Pocket::query()
                        ->datepicker($start, $stop)
                        ->where('category_id', $item->category_id)
                        ->where('expenditure_date', $date)->sum('expenditure');
                } else {
                    $tmp['data'][] = 0;
                }
            }

            array_push($data, $tmp);
        }

        return [
            'legend' => $legend,
            'yAxis' => $dates,
            'data' => $data,
        ];
    }

    public function update(Request $request)
    {
        $data = $request->all([
            'id',
            'expenditure_date'
        ]);

        Pocket::whereId($data['id'])->update(['expenditure_date' => $data['expenditure_date']]);

        return $data;
    }

    protected function params($request)
    {
        $datepicker = $request->input('datepicker', date('Y-m-01') . ' ~ ' . date('Y-m-t'));

        $category = $request->input('category');

        $start = trim(explode('~', $datepicker)[0]);
        $stop = trim(explode('~', $datepicker)[1]);

        if ($stop > now()->format('Y-m-d')) {
            $stop = now()->format('Y-m-d');
            $datepicker = "{$start} ~ {$stop}";
        }

        return [$category, $start, $stop, $datepicker];
    }
}
