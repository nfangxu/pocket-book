@extends('layouts.app')

@section('content')

@include('pocket')

<div class="container">
    <div class="row justify-content-end">
        <form id="search" class="form-inline">
            <div class="form-group mx-sm-3 mb-2">
                <select name="category" class="form-control">
                    <option value="">不限类别</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" 
                            {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->ext_name ?: $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <a class="nav-link btn btn-sm btn-dark text-white mb-2 mx-sm-2" 
                data-toggle="modal" 
                data-target="#pocketAdd"
                >新增</a>
        </form>
    </div>
    <div class="row justify-content-center">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">类别</th>
                    <th scope="col">支出</th>
                    <th scope="col">时间</th>
                    <th scope="col">是否必要</th>
                    <th scope="col">备注</th>
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
                        <td>{{ $pocket->is_necessary ? "" : '否' }}</td>
                        <td>{{ $pocket->comment }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="row justify-content-end">
        {{ $pockets->appends(request()->get())->links() }}
    </div>
</div>
@endsection

@section('js')
<script>
    $('#search select').on('change', function (){
        $('#search').submit();
    });
</script>
@endsection