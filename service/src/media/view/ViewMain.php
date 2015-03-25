<?php
namespace media\view;

abstract class ViewMain extends View{
	protected $url = null;

	public function __construct(){ 
		$app = new \Slim\Slim();

		// Get request object
		$req = $app->request;

		//Get root URI
		$rootUri = $req->getRootUri();

		$url = $this->addVar("rootUri", $rootUri);
		$app = \Slim\Slim::getInstance();
	}

}