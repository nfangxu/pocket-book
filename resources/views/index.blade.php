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
<div class="layui-container">
    <div class="layui-row">
        <blockquote class="layui-elem-quote">记账本</blockquote>
    </div>
    <div class="layui-row">
        <form class="layui-form" method="post">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <input type="text" class="layui-input"
                           name="expenditure_date"
                           id="datepicker"
                           readonly>
                    @csrf
                </div>
                <div class="layui-inline">
                    <select name="category" lay-verify="required">
                        <option value="">分类</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->ext_name ?: $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="layui-inline">
                    <input type="number" class="layui-input"
                           name="expenditure" min="0"
                           placeholder="金额"
                           autocomplete="off"
                           lay-verify="required">
                </div>
                <div class="layui-inline">
                    <input type="text" class="layui-input"
                           name="comment"
                           placeholder="备注"
                           autocomplete="off">
                </div>
                <div class="layui-inline">
                    <button class="layui-btn" lay-submit lay-filter="*">提交</button>
                </div>
            </div>
        </form>
    </div>
    <div class="layui-row">
        <div class="layui-col-md7">
            <table class="layui-table" id="pocketData"></table>
        </div>
        <div class="layui-col-md5 layui-col-offset1">
            <div id="dashboard" style="width:100%;height:650px;"></div>
        </div>
    </div>
</div>
<script src="https://cdn.bootcss.com/echarts/4.2.1-rc1/echarts-en.common.min.js"></script>
<script src="https://www.layuicdn.com/layui-v2.5.5/layui.js"></script>
<script type="text/html" id="tools">
    <span class="layui-btn layui-btn-xs">编辑</span>
    <span class="layui-btn layui-btn-xs layui-btn-danger">删除</span>
</script>
<script>
    layui.use(['element', 'form', 'laydate', 'table'], function () {
        let element = layui.element
            , form = layui.form
            , $ = layui.$
            , table = layui.table
            , laydate = layui.laydate
            , chart = echarts.init(document.getElementById('dashboard'));

        laydate.render({
            elem: '#datepicker'
            , value: "{{ date('Y-m-d') }}"
            , showBottom: false
            , max: 1 // 最多只能选明天
        });

        table.render({
            elem: '#pocketData'
            , id: 'pocketData'
            , url: '{{ route("pocket.page") }}' //数据接口
            , page: true //开启分页
            , limit: 15
            , limits: [15, 50]
            , cols: [[ //表头
                {field: 'expenditure_date', title: '日期'}
                , {field: 'category_name', title: '分类'}
                , {field: 'expenditure', title: '金额'}
                , {field: 'comment', title: '备注'}
                , {fixed: 'right', title: '操作', width: 120, align: 'left', toolbar: '#tools'}
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

            return false;
        });

        dashboard();

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
