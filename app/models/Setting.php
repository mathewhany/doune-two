<?php

class Setting extends \Illuminate\Database\Eloquent\Model
{
    public $fillable = ['name', 'value'];

    public $timestamps = false;
}
