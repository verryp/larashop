@extends('layouts.global')

@section('title')
    Trashed Category
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <form action="{{route('categories.index')}}">
                <div class="input-group">
                    <input type="text" placeholder ="Filter by name" value = "{{Request::get('name')}}" class="form-control" name="name">
                    <div class="input-group-append">
                        <input type="submit" value ="Filter" class="btn btn-primary">
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-6">
            <ul class="nav nav-pills card-header-pills">
                <li class="nav-item">
                    <a class="nav-link" href="{{route('categories.index')}}">Published</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" href="{{route('categories.trash')}}">Trash</a>
                </li>
            </ul>
        </div>
    </div>
    
    @include('layouts.flash-message')

    <hr class="my-3">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Slug</th>
                            <th>Image</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deleted_category as $category)
                        <tr>
                            <td>{{$category->name}}</td>
                            <td>{{$category->slug}}</td>

                            <td>
                                @if($category->image)
                                    <img src="{{asset('storage/' . $category->image)}}" width="48px"/>
                                @endif
                            </td>

                            <td>
                                <a href="{{route('categories.restore', ['id' => $category->id])}}" class="btn btn-success btn-sm">
                                    Restore
                                </a>

                                <form class="d-inline" action="{{route('categories.delete-permanent', ['id' => $category->id])}}" method="POST" onsubmit="return confirm('Delete this category permanently?')">

                                    @csrf
                                    @method('DELETE')

                                    <input type="submit" class="btn btn-danger btn-sm" value="Delete"/>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr>
                            <td colSpan="10">
                                {{$deleted_category->appends(Request::all())->links()}}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
@endsection