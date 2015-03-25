<?php
	
namespace media\modele;
use \Illuminate\Database\Eloquent\Model ;

class Categorie extends Model {
	protected $table = 'categorie';
	protected $primaryKey = 'id';
	public $timestamps=false;

	

	/*public function evenements(){
		return $this->hasMany('media\modele\Event', 'idCategorie');
	}*/

	
}