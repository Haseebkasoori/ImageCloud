<?php

namespace App\Models;


// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;


class Post extends Eloquent
{
    use SoftDeletes;

    //if facing any error than u can declare your table here

    protected $connection = 'mongodb';

    protected $collection = 'post';

    protected $fillable = [
        'user_id',
        'visibility',
        'share_with',
        'image_name',
    ];
    protected $dates = ['updated_at,created_at,deleted_at'];

    public function User(){
        return $this->belongsTo(User::class);
    }

}
