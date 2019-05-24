@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-end">
        <div id="dateRange"
            style="height: 38px; line-height: 38px; cursor: pointer; border-bottom: 1px solid #e2e2e2;"></div>
    </div>
    <div class="row justify-content-center">
        <div id="dashboard" style="width:100%;height:500px;"></div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('lib/layui/css/layui.css') }}">
@endsection

@section('js')
<script src="https://cdn.bootcss.com/echarts/4.2.1-rc1/echarts-en.common.min.js"></script>
<script src="{{ asset('lib/layui/layui.js') }}"></script>
<script>
        layui.use(['laydate'], function () {
            let laydate = layui.laydate;

            laydate.render({
                elem: '#dateRange'
                , range: "~"
                , value: '{{ $start }} ~ {{ $end }}'
                , max: 0
                , done: function (value, date) {
                    window.location.href = "{{ route('dashboard') }}?date=" + value;
                }
            });
        });
        // 基于准备好的dom，初始化echarts实例
        var myChart = echarts.init(document.getElementById('dashboard'));
        // 指定图表的配置项和数据
        var option = {
                title : {
                    text: '各类花费统计',
                    subtext: '{{ $start }} ~ {{ $end }}',
                    x:'center'
                },
                tooltip : {
                    trigger: 'item',
                    formatter: "{a} <br/>{b} : {c} ({d}%)"
                },
                legend: {
                    orient: 'vertical',
                    left: 'left',
                    data: @json($titles)
                },
                series : [
                    {
                        name: '金额',
                        type: 'pie',
                        radius : '55%',
                        center: ['50%', '60%'],
                        data:@json($data),
                        itemStyle: {
                            emphasis: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                    }
                ]
            };

        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption(option);
</script>
@endsection