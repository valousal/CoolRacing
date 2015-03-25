<?php
	
namespace media\modele;
use \Illuminate\Database\Eloquent\Model ;

class Event extends Model {
	protected $table = 'event';
	protected $primaryKey = 'id';
	public $timestamps=false;
	

	public function organisateur(){
		return $this->belongsTo('media\modele\Organisateurs','idOrganisateur');
	}

	public function statut(){
		return $this->belongsTo('media\modele\Statut','idStatut');
	}

	//Plusieurs vers plusieurs (n:n)
	public function participants(){
		return $this->belongsToMany('media\modele\Participants', 'participe', 'idEvent', 'idParticipants')->withPivot('dossard', 'sessionDepart',
			'sasDepart','certificatMedical','club');
	}

	
	//Plusieurs vers plusieurs (n:n)
	public function classement(){
		return $this->belongsToMany('media\modele\Participants', 'classement', 'idEvent', 'idParticipants')->withPivot('positionFinale', 'statut',
			'tempsTotal','tempsIntermediaire');
	}
}