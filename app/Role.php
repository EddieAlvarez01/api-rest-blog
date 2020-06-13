<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    //NOMBRE DE LA TABLA
    protected $table = 'Role';

    //DESCATIVAR TIMESTAMPS
    public $timestamps = false;

    //RELACIONES
    public function users(){
        return $this->hasMany('App\User');
    }

}
