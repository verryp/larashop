<?php

namespace App\Http\Controllers;

use App\Category;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = Category::paginate(5);

        $filterKeyword = $request->get('keyword');

        if($filterKeyword){
            $categories = Category::where("name", "LIKE", "%$filterKeyword%")->paginate(5);
        }

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3|max:20',
            'image'=> 'required|image'
        ]);

        $name = $request->get('name');

        $new_category = new Category;

        $new_category->name = $name;

        if($request->file('image')){
            $image_path = $request->file('image')->store('category_images', 'public');

            $new_category->image = $image_path;
        }

        $new_category->created_by = Auth::user()->id;
        $new_category->slug = str_slug($name, '-');

        $new_category->save();

        return redirect()->route('categories.index')->with('success', 'Category successfully created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        // $categoryById = Category::findOrFail($category);

        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $this->validate($request, [
            'name' => 'required|min:3|max:20',
            // 'image'=> 'required',
            'slug' => [
                'required',
                Rule::unique('categories')->ignore($category->slug, 'slug')
            ]
        ]);
        
        $name = $request->get('name');
        $slug = $request->get('slug');

        $category->name = $name;
        $category->slug = $slug;

        
        if($request->file('image')){
            if($category->image && file_exists(storage_path('app/public/' . $category->image))){
                \Storage::delete('public/' . $category->name);
            }

            $new_image = $request->file('image')->store('category_images', 'public');

            $category->image = $new_image;
        }

        $category->updated_by = Auth::user()->id;
        $category->slug = str_slug($name);

        $category->save();

        return redirect()->route('categories.index', compact('category'))->with('success', 'Category succesfully update');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category successfully moved to trash');
    }

    public function trash(){
        $deleted_category = Category::onlyTrashed()->paginate(5);

        return view('categories.trash', compact('deleted_category'));
    }

    public function restore($id){
        $category = Category::withTrashed()->findOrFail($id); // * Belum tau cara ngambil id pake el withTrash menggunakan objek kategori, bukan id
      
        if($category->trashed()){
          $category->restore();
        } else {
          return redirect()->route('categories.trash')->with('warning', 'Category is not in trash');
        }
      
        return redirect()->route('categories.index')->with('success', 'Category successfully restored');
      }

    public function deletePermanent($id){
        $category = Category::withTrashed()->findOrFail($id);  // * Belum tau cara ngambil id pake el withTrash menggunakan objek kategori, bukan id

        if(!$category->trashed()){
            return redirect()->route('categories.trash')->with('warning', 'Cannot delete permanen active category');
        }else{
            $category->forceDelete();

            return redirect()->route('categories.index')->with('success', 'Category permanently deleted');
        }
    }

    public function ajaxSearch(Request $request){
        $keyword = $request->get('q');

        $categories = Category::where("name", "LIKE", "%$keyword%")->get();

        return $categories;
    }
}
