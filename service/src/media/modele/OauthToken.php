<?php
	
namespace media\modele;
use \Illuminate\Database\Eloquent\Model ;

class OauthToken extends Model {
	protected $table = 'oauth_token';
	protected $primaryKey = 'access_token';
	public $timestamps=false;
}