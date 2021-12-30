<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use SoftDeletes; 
    
    protected $table = 'news';

    protected $fillable = [
        'topic_id',
        'title',
        'thumbnail',
        'content',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s',
        'deleted_at' => 'datetime:Y-m-d H:m:s'
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }
}
