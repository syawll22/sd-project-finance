<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mutasi extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function rekening()
    {
        return $table = $this->belongsTo(Rekening::class);
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
}