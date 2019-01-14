@extends('layouts.global')

@section('title') 
    Create Category 
@endsection 

@section('content')

    <div class="col-md-8">
        @include('layouts.flash-message')
        <form enctype="multipart/form-data" class="bg-white shadow-sm p-3" action="{{route('categories.store')}}" method="POST">
            @csrf

            <label>Category name</label><br>
            <input value="{{old('name')}}" type="text" class="form-control {{$errors->first('name') ? "is-invalid" : ""}}" name="name">
            <div class="invalid-feedback">
                {{$errors->first('name')}}
            </div>
            <br>

            <label>Category image</label>
            <input type="file" value="{{old('image')}}" class="form-control {{$errors->first('image') ? "is-invalid" : ""}}" name="image">
            <div class="invalid-feedback">
                {{$errors->first('image')}}
            </div>
            <br>

            <input type="submit" class="btn btn-primary" value="Save">
        </form>
    </div>

@endsection