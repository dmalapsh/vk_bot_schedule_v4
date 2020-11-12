<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Property extends Model {
	public $guarded = [];
	public $timestamps = false;
	public $incrementing = false;
	static public function getValue($name){
		return self::where('name', $name)->first()->value;
	}
	static public function setValue($name, $value){
		return self::where('name', $name)->update(['value'=>$value]);
	}
}
