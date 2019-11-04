<html>

<head>
  <title>记账本</title>
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
            <input type="text" class="layui-input" name="expenditure_date" id="datepicker" readonly>
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
            <input type="number" class="layui-input" name="expenditure" min="0" placeholder="金额" autocomplete="off"
              lay-verify="required">
          </div>
          <div class="layui-inline">
            <input type="text" class="layui-input" name="comment" placeholder="备注" autocomplete="off"
              lay-verify="required">
          </div>
          <div class="layui-inline">
            <button class="layui-btn" lay-submit lay-filter="*">提交</button>
          </div>
        </div>
      </form>
    </div>
    <div class="layui-row">
      <div class="layui-col-md7">
        <table class="layui-table">
          <colgroup>
          </colgroup>
          <thead>
            <tr>
              <th>#</th>
              <th>类别</th>
              <th>支出</th>
              <th>时间</th>
              <th>备注</th>
            </tr>
          </thead>
          <tbody>
            @foreach($pockets as $pocket)
              <tr>
                <th scope="row">{{ $pocket->id }}</th>
                <td title="{{ $pocket->category->comment }}">
                  {{ $pocket->category->ext_name ?: $pocket->category->name }}
                </td>
                <td>{{ $pocket->expenditure }}</td>
                <td>{{ $pocket->expenditure_date }}</td>
                <td>{{ $pocket->comment }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="layui-col-md5 layui-col-offset1">
        <div id="dashboard" style="width:100%;height:400px;"></div>
      </div>
    </div>
  </div>
  <script src="https://cdn.bootcss.com/echarts/4.2.1-rc1/echarts-en.common.min.js"></script>
  <script src="https://www.layuicdn.com/layui-v2.5.5/layui.js"></script>
  <script>
    layui.use(['element', 'form', 'laydate'], function(){
      let element = layui.element
       , form = layui.form
       , $ = layui.$
       , laydate = layui.laydate;

      laydate.render({
          elem: '#datepicker'
          , value: "{{ date('Y-m-d') }}"
          , showBottom: false
      });

      form.on('submit(*)', function (v) {
        let data = v.field;
        
        $.ajax({
            url: "{{ route('pocket.store') }}"
            , data: data
            , method: 'post'
            , success: function (r) {
                if (r.code) {
                    let msg = ""
                    $.each(r.data, function (i, error) {
                        $.each(error, function (k, m) {
                            msg += m+'<br>';
                        });
                    });
                    layer.msg(msg);
                } else {
                    layer.msg('操作成功');
                    window.location.reload();
                }
            }
            , error: function (e) {
                layer.msg('网络错误', {icon: 2});
            }
        });

        return false;
      });

      let dashboard = echarts.init(document.getElementById('dashboard'));

      let option = {
          legend: {},
          tooltip: {
              trigger: 'axis',
              showContent: false
          },
          dataset: {
              source: [
                  ['product', '2019-06', '2019-07', '2019-08', '2019-09', '2019-10', '2019-11'],
                  ['Matcha', 41.1, 30.4, 65.1, 53.3, 83.8, 98.7],
                  ['Milk', 86.5, 92.1, 85.7, 83.1, 73.4, 55.1],
                  ['Cheese', 24.1, 67.2, 79.5, 86.4, 65.2, 82.5],
                  ['Walnut', 55.2, 67.1, 69.2, 72.4, 53.9, 39.1]
              ]
          },
          xAxis: {type: 'category'},
          yAxis: {gridIndex: 0},
          grid: {top: '55%'},
          series: [
              {type: 'line', smooth: true, seriesLayoutBy: 'row'},
              {type: 'line', smooth: true, seriesLayoutBy: 'row'},
              {type: 'line', smooth: true, seriesLayoutBy: 'row'},
              {type: 'line', smooth: true, seriesLayoutBy: 'row'},
              {
                  type: 'pie',
                  id: 'pie',
                  radius: '30%',
                  center: ['50%', '25%'],
                  label: {
                      formatter: '{b}: {@2012} ({d}%)'
                  },
                  encode: {
                      itemName: 'product',
                      value: '2012',
                      tooltip: '2012'
                  }
              }
          ]
      };

      dashboard.on('updateAxisPointer', function (event) {
        var xAxisInfo = event.axesInfo[0];
        if (xAxisInfo) {
            var dimension = xAxisInfo.value + 1;
            dashboard.setOption({
                series: {
                    id: 'pie',
                    label: {
                        formatter: '{b}: {@[' + dimension + ']} ({d}%)'
                    },
                    encode: {
                        value: dimension,
                        tooltip: dimension
                    }
                }
            });
        }
      });

    dashboard.setOption(option);
  });
  </script>
</body>

</html>