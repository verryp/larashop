<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Book;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Auth;
use DB;

// * Bebas mau pake yang mana, saran pake collection aja
use App\Http\Resources\Book as BookResource; //Api Resource buat data tunggal
use App\Http\Resources\Books as BookCollectionResource; //Api Resource buat data collection


class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $books = DB::table('books')->get();
        // $books = new BookCollectionResource(Book::all());

        // * Api include pagination dengan limit data 5
        $books = new BookCollectionResource(Book::paginate(5));

        // * ngambil 10 data yang statusnya publish diurutkan ascending
        // $published_books = Book::where('status', 'PUBLISH')
        //     ->orderBy('title', 'asc')
        //     ->limit(10)
        //     ->get();

        // * ngambil data yang statusnya bukan draft
        // $published_books = $books->reject(function($book){
        //     return $book->status == 'DRAFT';
        // });

        // * ngambil data yang statusnya hanya draft
        // $published_books = $books->filter(function($book) {
        //     return $book->status == 'DRAFT';
        // });

        // * ngambil 2 data random dari query yang ada
        // $published_books = $books->random(2)->all();

        return $books;
        // return $published_books;
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|min:5|max:200',
            'description' => 'required|min:10|max:1000',
            'author' => 'required|min:3|max:200',
            'publisher' => 'required|min:3|max:200',
            'price' => 'numeric|digits_between:0,10',
            'stock' => 'numeric|digits_between:0,10',
            'cover' => 'required|image'
        ]);
        
        $newBook = new Book;

        $newBook->title = $request->get('title');
        $newBook->description = $request->get('description');
        $newBook->author = $request->get('author');
        $newBook->publisher = $request->get('publisher');
        $newBook->price = $request->get('price');
        $newBook->stock = $request->get('stock');

        $newBook->status = $request->get('save_action');
        
        $cover = $request->file('cover');

        if($cover){
            $cover_path = $cover->store('books-cover', 'public');

            $newBook->cover = $cover_path;
        }

        $newBook->slug = str_slug($request->get('title'));
        $newBook->created_by = Auth::user()->id;

        $newBook->save();

        $newBook->categories()->attach($request->get('categories'));

        if($request->get('save_action') == 'PUBLISH'){
            return redirect()->route('books.index')->with('success', 'Book successfully published');
        }else {
            return redirect()->route('books.index')->with('success', 'Book saved as draft');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        // $books = new BookResource();

        return $book;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function edit(Book $book)
    {
        return view('books.edit', compact('book'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
        $this->validate($request, [
            'title' => 'required|min:5|max:200',
            'description' => 'required|min:10|max:1000',
            'author' => 'required|min:3|max:200',
            'publisher' => 'required|min:3|max:200',
            'price' => 'numeric|digits_between:0,10',
            'stock' => 'numeric|digits_between:0,10',
            // 'cover' => 'required',
            'slug' => [
                'required',
                Rule::unique('books')->ignore($book->slug, 'slug')
            ]
        ]);

        $book->title = $request->get('title');
        $book->slug = $request->get('slug');
        $book->description = $request->get('description');
        $book->author = $request->get('author');
        $book->publisher = $request->get('publisher');
        $book->stock = $request->get('stock');
        $book->price = $request->get('price');

        $new_cover = $request->file('cover');

        if($new_cover){
            if($book->cover && file_exists(storage_path('app/public/' . $book->cover))){
                \Storage::delete('public/'. $book->cover);
            }

            $new_cover_path = $new_cover->store('books-covers', 'public');

            $book->cover = $new_cover_path;
        }

        $book->updated_by = \Auth::user()->id;

        $book->status = $request->get('status');

        $book->save();

        $book->categories()->sync($request->get('categories'));

        return redirect()->route('books.index', compact('book'))->with('success', 'Book successfully updated');

        // * Update judul buku dengan id 26
        // $books = Book::findOrFail(26);
        // $books->fill(
        //     ['title' => 'Updated Buku']
        // );

        // * updated data, tapi jika gk ada maka langsung jalanin query insert
        // $books = Book::updateOrCreate(
        //     ['id' => '28', 'title' => 'Test updateOrCreate', 'slug' => 'test-updateorcreate'],
        //     ['price' => 99]
        // );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()->route('books.index', compact('book'))->with('success', 'Book move to trashed');
    }

    public function trash(){
        $books = Book::onlyTrashed()->paginate(5);

        return view('books.trash', compact('books'));
    }

    public function restore($id){
        $book = Book::withTrashed()->findOrFail($id);

        if($book->trashed()){
            $book->restore();

            return redirect()->route('books.trash')->with('success', 'Book succesfully restored');
        }else{
            return redirect()->route('books.trash')->with('warning', 'Book is not in trash');
        }
    }

    public function deletePermanent($id){
        $book = Book::withTrashed()->findOrFail($id);

        if(!$book->trashed()){
            return redirect()->route('books.trash')->with('warning', 'Book is not in trash!');
        }else{
            $book->categories()->detach();
            $book->forceDelete();

            return redirect()->route('books.trash')->with('success', 'Book permanently deleted!');
        }
    }
}
