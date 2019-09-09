<div class="modal fade" id="pocketAdd" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="pocketAddForm" method="post">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">添加记录</h5>
                </div>
                <div class="modal-body">
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">类别</label>
                            <div class="col-sm-9">
                                <select name="category" class="form-control">
                                    <option value="">选择类别</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->ext_name ?: $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="ext_category" class="form-group row" style="display: none;">
                                <label for="inputPassword" class="col-sm-3 col-form-label">自定义类别</label>
                                <div class="col-sm-9">
                                    <input type="text" name="ext_category" class="form-control" placeholder="类别名称">
                                </div>
                            </div>
                        <div class="form-group row">
                            <label for="inputPassword" class="col-sm-3 col-form-label">金额</label>
                            <div class="col-sm-9">
                                <input type="number" name="expenditure" class="form-control" placeholder="单位: 元">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputPassword" class="col-sm-3 col-form-label">日期</label>
                            <div class="col-sm-9">
                                <select name="expenditure_date" class="form-control">
                                    @for($i=0; $i< request('days', 7); $i++)
                                        <option value="{{ date('Y-m-d', strtotime('-'.$i.' days')) }}"
                                        >{{ date('Y-m-d', strtotime('-'.$i.' days')) }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputPassword" class="col-sm-3 col-form-label">备注</label>
                            <div class="col-sm-9">
                            <input type="text" name="comment" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">必要花销</label>
                            <div class="col-sm-9">
                                <select name="is_necessary" class="form-control">
                                    <option value="0">否</option>
                                    <option value="1" selected>是</option>
                                </select>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
                    <button type="button" id="pocketAddBtn" class="btn btn-primary">提交</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
$(function (){
    $('#pocketAddForm select[name=category]').on('change', function (){
        let id = $(this).children('option:selected').val();
        if (id == {{ $extId[0] }}) {
            $('#ext_category').show();
        } else {
            $('#ext_category').hide();
        }
    });
    $('#pocketAddBtn').on('click', function (v){
        let x = $('#pocketAddForm').serializeArray();
        let data = {};
        $.each(x, function (i, field){
            data[field.name] = field.value;
        });

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
        })
    });
});
</script>
