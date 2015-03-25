<?php
	
namespace media\modele;
use \Illuminate\Database\Eloquent\Model ;

class OauthClient extends Model {
	protected $table = 'oauth_client';
	protected $primaryKey = 'id';
	public $timestamps=false;
}