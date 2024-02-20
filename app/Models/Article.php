<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class);
    }

    
}
