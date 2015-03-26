<?php
	
namespace media\modele;
use \Illuminate\Database\Eloquent\Model ;

class Adresse extends Model {
	protected $table = 'adresse';
	protected $primaryKey = 'id';
	public $timestamps=false;

	

	public function organisateurs(){
		return $this->hasMany('media\modele\Organisateurs', 'idAdress');
	}

	public function participants(){
		return $this->hasMany('media\modele\Participants', 'idAdress');
	}

	
}