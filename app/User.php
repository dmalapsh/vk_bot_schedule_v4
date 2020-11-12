<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model {
	protected $table = 'users_beta';
	public $guarded = [];
//	public $timestamps = false;
	public $incrementing = false;

	public function subscribe($status, $str = null){
		$this->update([
			'subscribe_status' => $status,
			'search_string'    => $str,
		]);
	}
	public function background(){
		return $this->hasOne(Background::class,'id', 'background_id');
	}
}
