<?php
	
namespace media\modele;
use \Illuminate\Database\Eloquent\Model ;

class Classement extends Model {
	protected $table = 'classement';
	// protected $primaryKey = 'id';
	public $timestamps=false;

	//Plusieurs vers plusieurs (n:n)
	public function event(){
		return $this->belongsToMany('media\modele\Event', 'classement', 'idEvent', 'idParticipants')->withPivot('positionFinale', 'statut',
			'tempsTotal','tempsIntermediaire');
	}

	//Plusieurs vers plusieurs (n:n)
	public function participants(){
		return $this->belongsToMany('media\modele\Participants', 'classement', 'idEvent', 'idParticipants')->withPivot('positionFinale', 'statut',
			'tempsTotal','tempsIntermediaire');
	}


	
}