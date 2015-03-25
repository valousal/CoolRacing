<?php
	
namespace media\modele;
use \Illuminate\Database\Eloquent\Model ;

class Organisateurs extends Model {
	protected $table = 'organisateurs';
	protected $primaryKey = 'id';
	public $timestamps=false;

	

	public function adresse(){
		return $this->belongsTo('media\modele\Adresse','idAdress');
	}

	public function evenements(){
		return $this->hasMany('media\modele\Event', 'idOrganisateur');
	}
	
}