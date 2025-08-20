<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'body', 'status', 'approved_by', 'approved_at', 'reject_reason', 'cover_image'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCoverImageUrlAttribute()
{
    return $this->cover_image 
        ? asset('storage/' . $this->cover_image) 
        : null;
}

}
