@extends('layouts.dashboard')

@section('option')
// 指定图表的配置项和数据
var option = {
        title : {
            text: '花销饼图: (总花销: ¥ {{ $total }})',
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
@endsection