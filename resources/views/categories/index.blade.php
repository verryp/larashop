@extends('layouts.global')

@section('title')
    Category List
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <form action="{{route('categories.index')}}">

                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Filter by category name" name="keyword">
                    
                    <div class="input-group-append">
                        <input type="submit" value="Filter" class="btn btn-primary">
                    </div>
                </div>

            </form>
        </div>

        <div class="col-md-6">
            <ul class="nav nav-pills card-header-pills">
                <li class="nav-item">
                    <a href="{{route('categories.index')}}" class="nav-link active">Published</a>
                </li>
                <li class="nav-item">
                    <a href="{{route('categories.trash')}}" class="nav-link">Trash</a>
                </li>
            </ul>
        </div>
    </div>

    <hr class="my-3">

    @include('layouts.flash-message')

    <div class="row">
        <div class="col-md-12 text right">
            <a href="{{route('categories.create')}}" class="btn btn-primary">
                Create Category
            </a>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-stripped">
            <thead>
                <tr>
                <th><b>Name</b></th>
                <th><b>Slug</b></th>
                <th><b>Image</b></th>
                <th><b>Actions</b></th>
                </tr>
            </thead>

            <tbody>
                @foreach ($categories as $category)
                <tr>
                    <td>{{$category->name}}</td>
                    <td>{{$category->slug}}</td>
                    <td>
                    @if($category->image)
                        <img src="{{asset('storage/' . $category->image)}}" width="48px"/>
                    @else 
                        No image
                    @endif
                    </td>
                    <td>
                        <a href="{{route('categories.edit', $category->id)}}" class="btn btn-info btn-sm"> 
                            Edit 
                        </a>

                        <a href="{{route('categories.show', $category->id)}}" class="btn btn-primary btn-sm">
                            Show
                        </a>

                        <form action="{{route('categories.destroy', $category->id)}}" class="d-inline" method ="POST" onsubmit="return confirm('Move category trash?')">
                            @csrf
                            @method('DELETE')

                            <input type="submit" class="btn btn-danger btn-sm" value = "trash">

                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td colSpan="10">
                    {{$categories->appends(Request::all())->links()}}
                </td>
            </tr>
            </tfoot>
            </table>
        </div>
    </div>
@endsection