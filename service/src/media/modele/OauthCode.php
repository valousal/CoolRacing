<?php
	
namespace media\modele;
use \Illuminate\Database\Eloquent\Model ;

class OauthCode extends Model {
	protected $table = 'oauth_code';
	protected $primaryKey = 'auth_code';
	public $timestamps=false;
}