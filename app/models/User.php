<?php

class User extends Illuminate\Database\Eloquent\Model
{
    public $fillable = ['email', 'password'];

    public $hidden = ['password'];

    public function articles()
    {
        return $this->hasMany('Article');
    }
}