<html>

<head>
    <title>记账本</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://www.layuicdn.com/layui-v2.5.5/css/layui.css">
</head>

<body>
    <div class="layui-fluid" style="position: absolute; left: 0; top: 0; width: 100%">
        <div class="layui-container" style="background-color: #fff; margin-top: 12px;">
            <div class="layui-row">
                <blockquote class="layui-elem-quote">
                    <div class="layui-row">
                        <div class="layui-col-md2">
                            记账本
                        </div>
                        <div class="layui-col-md10">
                            <h1>
                                当月限额: ¥ 4000.00, 已使用: ¥ <span id="used" style="color: red">{{ $total ?? 0}}</span>
                            </h1>
                        </div>
                    </div>
                </blockquote>
            </div>
            <div class="layui-row">
                <form class="layui-form" method="post">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <input type="text" class="layui-input" name="expenditure_date" id="datepicker" readonly>
                            @csrf
                        </div>
                        <div class="layui-inline">
                            <select name="category" lay-verify="required">
                                <option value="">分类</option>
                                @foreach($categories as $v)
                                <option value="{{ $v->id }}">{{ $v->ext_name ?: $v->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="layui-inline">
                            <input type="number" class="layui-input" name="expenditure" min="0" placeholder="金额"
                                autocomplete="off" lay-verify="required">
                        </div>
                        <div class="layui-inline">
                            <input type="text" class="layui-input" name="comment" placeholder="备注" autocomplete="off">
                        </div>
                        <div class="layui-inline">
                            <button class="layui-btn" lay-submit lay-filter="*">提交</button>
                        </div>
                    </div>
                </form>
            </div>
            <hr>
            <div class="layui-row">
                <div class="layui-col-md7">
                    <div class="layui-row">
                        <div class="layui-form">
                            <label class="layui-form-label" style="width: 36px;">筛选</label>
                            <div class="layui-input-inline">
                                <div id="search-datepicker" class="layui-input"
                                    style="line-height: 38px; width: 185px;"></div>
                                <input type="hidden" name="datepicker" value="{{ $datepicker }}">
                            </div>
                            <div class="layui-input-inline">
                                <select name="search-category" lay-filter="search-category">
                                    <option value="">分类</option>
                                    @foreach($categories as $v)
                                    <option value="{{ $v->id }}" 
                                        {{ ($v->id == $category) ? 'selected' : '' }}
                                        >{{ $v->ext_name ?: $v->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="layui-row">
                        <table class="layui-table" id="pocketData" lay-filter="pocketData"></table>
                    </div>
                </div>
                <div class="layui-col-md5 layui-col-offset1">
                    <div id="dashboard" style="width:100%;height:650px;"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset("js/jquery-3.4.1.min.js") }}" charset="utf-8"></script>
    <script src="https://cdn.bootcss.com/echarts/4.2.1-rc1/echarts-en.common.min.js"></script>
    <script src="https://www.layuicdn.com/layui-v2.5.5/layui.js"></script>
    <script src="{{ asset("js/jquery.particleground.min.js") }}" charset="utf-8"></script>
    <script>
        layui.use(['element', 'form', 'laydate', 'table'], function () {
            let element = layui.element
                , form = layui.form
                , $ = layui.$
                , table = layui.table
                , laydate = layui.laydate
                , chart = echarts.init(document.getElementById('dashboard'));

            // 粒子线条背景
            $(document).ready(function () {
                $('body').particleground({
                    dotColor: '#009688',
                    lineColor: '#009688'
                });
            });

            laydate.render({
                elem: '#datepicker'
                , value: "{{ date('Y-m-d') }}"
                , showBottom: false
                , max: 1 // 最多只能选明天
            });

            laydate.render({
                elem: '#search-datepicker'
                , value: "{{ $datepicker }}"
                , range: "~"
                , max: 0 // 最多只能选明天
                , done: function (v) {
                    $('input[name=datepicker]').val(v);
                    search();
                }
            });

            form.on('select(search-category)', function(data){
                search();
            });
            
            table.render({
                elem: '#pocketData'
                , id: 'pocketData'
                , url: '{{ route("pocket.page") }}' //数据接口
                , page: true //开启分页
                , limit: 15
                , limits: [15, 50]
                , cols: [[ //表头
                    {field: 'expenditure_date', title: '日期', edit: 'text'}
                    , {field: 'category_name', title: '分类'}
                    , {field: 'expenditure', title: '金额'}
                    , {field: 'comment', title: '备注'}
                ]]
                , parseData: function (res) { //res 即为原始返回的数据
                    return {
                        "code": 0, // 解析接口状态
                        "msg": 'ok', // 解析提示文本
                        "count": res.total, // 解析数据长度
                        "data": res.data // 解析数据列表
                    };
                }
            });

            table.on('edit(pocketData)', function(obj){ //注：edit是固定事件名，test是table原始容器的属性 lay-filter="对应的值"
                console.log(obj.value); //得到修改后的值
                console.log(obj.field); //当前编辑的字段名
                console.log(obj.data); //所在行的所有相关数据 
                $.post('{{ route("pocket.update") }}', {
                    _token: '{{ csrf_token() }}'
                    , id: obj.data.id
                    , expenditure_date: obj.value
                }, function (r) {
                    table.reload('pocketData');
                    dashboard();
                });
            });

            form.on('submit(*)', function (v) {
                layer.load(2);
                let data = v.field;

                $.ajax({
                    url: "{{ route('pocket.store') }}"
                    , data: data
                    , method: 'post'
                    , success: function (r) {
                        layer.closeAll('loading');
                        if (r.code) {
                            let msg = "";
                            $.each(r.data, function (i, error) {
                                $.each(error, function (k, m) {
                                    msg += m + '<br>';
                                });
                            });
                            layer.msg(msg);
                        } else {
                            table.reload('pocketData');
                            layer.msg('操作成功');
                        }
                    }
                    , error: function (e) {
                        layer.closeAll('loading');
                        layer.msg('网络错误', {icon: 2});
                    }
                });

                // 清空金额和备注
                $('input[name=expenditure]').val('');
                $('input[name=comment]').val('');

                dashboard();

                return false;
            });

            dashboard();

            function search()
            {
                let v = {
                    datepicker: $('input[name=datepicker]').val(),
                    category: $('select[name=search-category] option:selected').val()
                };

                window.location.href = '?datepicker='+v.datepicker+'&category='+v.category;
            }

            function dashboard() {
                $.ajax({
                    url: '{{ route('pocket.chart') }}'
                    , success: function (r) {
                        chart.showLoading();
                        dashboardInit(chart, r.legend, r.yAxis, r.data);
                        chart.hideLoading()
                    }
                });
            }

            function dashboardInit(chart, legend, yAxis, data) {
                chart.setOption({
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: {            // 坐标轴指示器，坐标轴触发有效
                            type: 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                        }
                    },
                    legend: {
                        data: legend
                    },
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    xAxis: [
                        {
                            type: 'value'
                        },
                        {
                            type: 'value'
                        }
                    ],
                    yAxis: {
                        type: 'category',
                        data: yAxis
                    },
                    series: data
                });
            }
        });
    </script>
</body>

</html>