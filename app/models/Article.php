<?php

class Article extends Illuminate\Database\Eloquent\Model
{
    protected $fillable = ['title', 'content', 'user_id'];

    public function tags()
    {
        return $this->belongsToMany('Tag');
    }

    public function author()
    {
        return $this->belongsTo('User');
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon\Carbon::parse($value)->diffForHumans();
    }

    public function getTagsListAttribute()
    {
        return $this->tags->lists('id');
    }
}
