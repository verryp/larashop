@extends('layouts.global')

@section('title') Edit Category @endsection 

@section('content')
  <div class="col-md-8">
    <form action="{{route('categories.update', $category->id)}}" enctype="multipart/form-data" method="POST" class="bg-white shadow-sm p-3">

        {{ csrf_field() }}
        {{ method_field('PATCH')}}

        <label>Category name</label> <br>
        <input type="text" class="form-control {{$errors->first('name') ? "is-invalid" : "" }}" value="{{old('name') ? old('name') : $category->name}}" name="name">
        <div class="invalid-feedback">
          {{$errors->first('name')}}
        </div>

        <br><br>

        <label>Cateogry slug</label>
        <input type="text" class="form-control {{$errors->first('slug') ? "is-invalid" : ""}}" value="{{old('slug') ? old('slug') : $category->slug}}" name="slug">
        <div class="invalid-feedback">
          {{$errors->first('slug')}}
        </div>

        <br><br>

        <label>Category image</label><br>
        @if($category->image)
            <span>Current image</span><br>
            <img src="{{asset('storage/'. $category->image)}}" width="120px">
            <br><br>
        @endif

      <input type="file" class="form-control" name="image">
        <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar</small>
        <br><br>

        <input type="submit" class="btn btn-primary" value="Update">

    </form>
  </div>
@endsection 