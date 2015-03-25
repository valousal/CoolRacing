<?php
	
namespace media\modele;
use \Illuminate\Database\Eloquent\Model ;

class Participe extends Model {
	protected $table = 'participe';
	//protected $primaryKey = 'idParticipants, idEvent';
	public $timestamps=false;

	//Plusieurs vers plusieurs (n:n)
	public function event(){
		return $this->belongsToMany('media\modele\Event', 'participe', 'idEvent', 'idParticipants')->withPivot('dossard', 'sessionDepart',
			'sasDepart','certificatMedical','club');
	}

	//Plusieurs vers plusieurs (n:n)
	public function participants(){
		return $this->belongsToMany('media\modele\Participants', 'participe', 'idEvent', 'idParticipants')->withPivot('dossard', 'sessionDepart',
			'sasDepart','certificatMedical','club');
	}


	
}