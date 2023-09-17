<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    public function singer()
    {
        return $this->belongsTo(Singer::class, 'singer_id', 'id'); // 'singer_id' sütunu ile 'id' sütunu arasında ilişki kuruyoruz
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class, 'genre_id', 'id'); // 'genre_id' sütunu ile 'id' sütunu arasında ilişki kuruyoruz
    }
}
