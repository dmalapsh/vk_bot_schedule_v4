<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class Background extends Model {
	public $guarded = [];
	public $timestamps = false;

	public function users(){
		return $this->hasMany(User::class, 'background_id', 'id');
	}
}