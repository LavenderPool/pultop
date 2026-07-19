<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoPage extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'title',
        'description',
        'keywords',
        'h1',
    ];
}
