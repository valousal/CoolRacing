<?php
namespace media\controller ;
use \media\modele ;
use \media\view ;

class DocController extends AbstractController{
	public function __construct(){

	}	

	public function displayIndex(){
		$v = new View\ViewIndex;
		$v->display();
	}

	public function displayDoc(){
		$v = new View\ViewDoc;
		$v->display();
	}

	public function displayContact(){
		$v = new View\ViewContact;
		$v->display();
	}

	public function signin(){
		$v = new View\ViewSignInClient;
		$v->display();
	}
}