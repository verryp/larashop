<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    public function books()
    {
        return $this->belongsToMany(Category::class, 'book_category', 'category_id', 'book_id');
    }
}
