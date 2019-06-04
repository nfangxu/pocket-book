@extends('layouts.dashboard')

@section('option')
var option = {
        title: {
            text: '折线图'
        },
        tooltip: {
            trigger: 'axis'
        },
        legend: {
            data:@json($legendData)
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        toolbox: {
            feature: {
                saveAsImage: {}
            }
        },
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: @json($xAxisData)
        },
        yAxis: {
            type: 'value'
        },
        series: @json($series)
    };
@endsection