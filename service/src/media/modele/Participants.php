<?php
	
namespace media\modele;
use \Illuminate\Database\Eloquent\Model ;

class Participants extends Model {
	protected $table = 'participants';
	protected $primaryKey = 'id';
	public $timestamps=false;


	//Plusieurs vers plusieurs (n:n)
	public function event(){
		return $this->belongsToMany('media\modele\Event', 'participe', 'idEvent', 'idParticipants')->withPivot('dossard', 'sessionDepart',
			'sasDepart','certificatMedical','club');
	}

	//Plusieurs vers plusieurs (n:n)
	public function classement(){
		return $this->belongsToMany('media\modele\Event', 'classement', 'idEvent', 'idParticipants')->withPivot('positionFinale', 'statut',
			'tempsTotal','tempsIntermediaire');
	}
	
	public function adresse(){
		return $this->belongsTo('media\modele\Adresse','idAdress');
	}

}