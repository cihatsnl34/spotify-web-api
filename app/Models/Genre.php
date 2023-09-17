<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    public function songs()
    {
        return $this->hasMany(Song::class, 'genre_id', 'id'); // 'genre_id' sütunu ile 'id' sütunu arasında ilişki kuruyoruz
    }
}
