@extends('layouts.global')

@section('footer-scripts')
    <link href="{{asset('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css')}}" rel="stylesheet" />
    <script src="{{asset('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js')}}"></script>

    <script>
        $('#categories').select2({
            ajax: {
                url           : '{{route('categories.ajax')}}',
                processResults: function(data){
                    return {
                    results: data.map(function(item){return {id: item.id, text: item.name} })
                    }
                }
            }
        });
    </script>
@endsection

@section('title')
    Create book
@endsection

@section('content')
    @include('layouts.flash-message')
    <div class="row">
        <div class="col-md-8">
        <form action="{{route('books.store')}}" method="POST" enctype="multipart/form-data" class="shadow-sm p-3 bg-white">

            @csrf

            <label for="title">Title</label> <br>
            <input value="{{old('title')}}" type="text" class="form-control {{$errors->first('title') ? "is-invalid" : ""}}" name="title" required placeholder="Book title">
            <div class="invalid-feedback">
                {{$errors->first('title')}}
            </div>
            <br>

            <label for="cover">Cover</label>
            <input type="file" class="form-control {{$errors->first('description') ? "is-invalid" : ""}}" name="cover">
            <div class="invalid-feedback">
                {{$errors->first('cover')}}
            </div>
            <br>

            <label for="description">Description</label><br>
            <textarea name="description" id="description" class="form-control {{$errors->first('description') ? "is-invalid" : ""}}" required placeholder="Give a description about this book">
                {{old('description')}}
            </textarea>
            <div class="invalid-feedback">
                {{$errors->first('description')}}
            </div>
            <br>

            <label for="stock">Stock</label><br>
            <input value="{{old('stock')}}" type="number" class="form-control {{$errors->first('stock') ? "is-invalid" : ""}}" required id="stock" name="stock" min=0 value=0>
            <div class="invalid-feedback">
                {{$errors->first('stock')}}
            </div>
            <br>

            <label for="author">Author</label><br>
            <input value="{{old('author')}}" type="text" class="form-control {{$errors->first('author') ? "is-invalid" : ""}}" required name="author" id="author" placeholder="Book author">
            <div class="invalid-feedback">
                {{$errors->first('author')}}
            </div>
            <br>

            <label for="publisher">Publisher</label>  <br>
            <input value="{{old('publisher')}}" type="text" class="form-control {{$errors->first('publisher') ? "is-invalid" : ""}}" required id="publisher" name="publisher" placeholder="Book publisher">
            <div class="invalid-feedback">
                {{$errors->first('publisher')}}
            </div>
            <br>

            <label for="Price">Price</label> <br>
            <input value="{{old('price')}}" type="number" class="form-control {{$errors->first('price') ? "is-invalid" : ""}}" required name="price" id="price" placeholder="Book price">
            <div class="invalid-feedback">
                {{$errors->first('price')}}
            </div>
            <br>

            <label for="categories">Categories</label><br>
            <select name="categories[]" multiple id="categories" class="form-control"></select>
            <br><br/>

            <button class="btn btn-primary" name="save_action" value="PUBLISH">Publish</button>
            <button class="btn btn-secondary" name="save_action" value="DRAFT">Save as draft</button>

        </form>
        </div>
    </div>
@endsection