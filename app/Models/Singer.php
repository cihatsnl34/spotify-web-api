<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Singer extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name'
    ];

    public function songs()
    {
        return $this->hasMany(Song::class, 'singer_id', 'id'); // 'singer_id' sütunu ile 'id' sütunu arasında ilişki kuruyoruz
    }
}
