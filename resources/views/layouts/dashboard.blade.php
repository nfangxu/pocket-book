@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-start">
        <form id="search" class="layui-form" action="">
            <div class="layui-input-inline">
                <select name="user" lay-filter='users'>
                    <option value="">不限</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user') == $user->id ? "selected" : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="layui-input-inline">
                <select name="type" lay-filter='types'>
                    @foreach($types as $k => $type)
                        <option value="{{ $k }}" {{ request('type', 'pie') == $k ? "selected" : '' }}
                            >{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <input type="hidden" name="date" value="{{ $start }}~{{ $end }}">
        </form>
    </div>
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
        layui.use(['laydate', 'form'], function () {
            let laydate = layui.laydate;
            let form = layui.form;

            form.on('select(users)', function(data){
                $('#search').submit();
            });

            form.on('select(types)', function(data){
                $('#search').submit();
            });

            laydate.render({
                elem: '#dateRange'
                , range: "~"
                , value: '{{ $start }} ~ {{ $end }}'
                , max: 0
                , done: function (value, date) {
                    window.location.href = "{{ route('dashboard') }}?user={{ request('user') }}&type={{ request('type') }}&date=" + value;
                }
            });
        });
        // 基于准备好的dom，初始化echarts实例
        var myChart = echarts.init(document.getElementById('dashboard'));
        // 指定图表的配置项和数据
        @yield('option')

        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption(option);
</script>
@endsection