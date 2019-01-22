<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Book;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Auth;
use DB;

// * Bebas mau pake yang mana, saran pake collection aja
use App\Http\Resources\Bookss as BooksResourceCollection;
use App\Http\Resources\Book as BookResource;
// use App\Http\Resources\Book as BookResourceCollection; //Api Resource buat data collection

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
        // $books = new BookResourceCollection(Book::all());

        // * Api include pagination dengan limit data 6
        $books = new BooksResourceCollection(Book::paginate(6));

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
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        //
    }

    public function top($count)
    {
        $criteria = Book::select('*')
            ->orderBy('views', 'DESC')
            ->limit($count)
            ->get();        
        return new BooksResourceCollection($criteria);
    }

    public function slug($slug){
        $criteria = Book::where('slug', $slug)->first();
        return new BookResource($criteria);
    }
}
