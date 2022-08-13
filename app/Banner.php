<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table='banner';
    protected $fillable=['image','order'];

    public function item(){
        return $this->hasOne('App\Item','id','item_id');
    }
}
