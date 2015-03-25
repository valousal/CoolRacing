<?php
	
namespace media\modele;
use \Illuminate\Database\Eloquent\Model ;

class Statut extends Model {
	protected $table = 'statut';
	protected $primaryKey = 'id';
	public $timestamps=false;

	

	public function evenements(){
		return $this->hasMany('media\modele\Event', 'idOrganisateur');
	}

	
}