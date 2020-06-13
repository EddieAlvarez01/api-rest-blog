<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

    //NOMBRE DE LA TABLA
    protected $table = 'Post';

    //RELACIONES
    public function user(){
        return $this->belongsTo('App\User');
    }

    public function category(){
        return $this->belongsTo('App\Category');
    }

}
