<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeOption extends Model
{
  protected $fillable = ['attribute_id', 'name', 'keyword', 'color_image', 'price','stock'];

  public function attribute() {
    return $this->belongsTo('App\Models\Attribute')->withDefault();
  }

  public $timestamps = false;

}
