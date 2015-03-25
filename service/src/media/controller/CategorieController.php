<?php
namespace media\controller ;
use \media\modele\Event ;
use \media\modele\Organisateurs ;
use \media\modele\Participants ;
use \media\modele\Categorie ;
use \media\modele\Participe ;

class CategorieController extends AbstractController{
	public function __construct(){

	}

	public function getAll(){ //rajouter des champs dans categories, pour les droits, les truk speciaux, ect...
		$app = \Slim\Slim::getInstance();
		$req = $app->request;
		$rootUri = $req->getUrl();
		$rootUri .= $req->getRootUri();

		$categories = Categorie::get();

		$res = array();
		foreach ($categories as $categorie){
			$res[] = $categorie;
		}
		
		$nbCategories = $categories->count();

		$links = array(array('rel' => 'prev',
								'href' => "$rootUri/api/categories/?limit=10&offset=150"),
								array('rel' => 'next',
								'href' => "$rootUri/api/categories/?limit=10&offset=0"),
								array('rel' => 'first',
								'href' => "$rootUri/api/categories/?limit=10&offset=150"),
								array('rel' => 'last',
								'href' => "$rootUri/api/categories/?limit=10&offset=$nbCategories"),
								);



		$app->response->setStatus(200) ;
		$app->response->headers->set('Content-type','application/json') ;
		echo json_encode(array('categories'=>$res, "links"=>$links));
	}

	public function getById($id){ //faire si id non connu
		$app = \Slim\Slim::getInstance();
		$req = $app->request;
		$rootUri = $req->getUrl();
		$rootUri .= $req->getRootUri();

		$categories = Categorie::find($id);

		if(!empty($categories)){

			$app->response->setStatus(200) ;
			$app->response->headers->set('Content-type','application/json') ;
			echo $categories->toJson();
		}else{
			$app->response->headers->set('Content-type','application/json') ;
	        $app->halt(404, json_encode(array("erreur_message"=>"categorie non trouvée")));
		}
	}

	public function delete($id){
		$categorie = Categorie::find($id);
		
		$app = \Slim\Slim::getInstance();
		try{
			$categorie->delete(); //rajouter une confirmation?
		}catch(Exception $e){
		    $app->response->headers->set('Content-type','application/json') ;
	        $app->halt(400, json_encode(array("erreur_message"=>"Impossible de supprimer la ressource")));
		}
		$app->response->setStatus(200) ;
		$app->response->headers->set('Content-type','application/json') ;
		echo json_encode(array("delete"=>"ok"));
	}

	public function put($id){
		$app = \Slim\Slim::getInstance();
		$req = $app->request->getBody();
		$data = (json_decode($req, true));

		$categorie = Categorie::find($id);

		if (!empty($categorie)){

			if(!empty($data['nom'])){
				$categorie->nom = filter_var($data['nom'], FILTER_SANITIZE_STRING);
			}

			try {
			   $categorie->save();
			} catch (Exception $e) {
			    $app->response->headers->set('Content-type','application/json') ;
		        $app->halt(400, json_encode(array("erreur_message"=>"Mauvais formats des donées envoyées")));
			}

			$rootUri = $app->request->getUrl();
			$rootUri .= $app->request->getRootUri();

			$app->response->setStatus(200) ;
			$app->response->headers->set('Content-type','application/json') ;
			echo $categorie->toJson();
		}else{
		     $app->response->headers->set('Content-type','application/json') ;
	         $app->halt(404, json_encode(array("erreur_message"=>"categorie non trouvée")));
		}
	}

	public function post(){
		$app = \Slim\Slim::getInstance();
		$req = $app->request->getBody();
		$data = (json_decode($req, true)); 

		if(isset($data['nom'])){
			$categorie = new Categorie;
			$categorie->nom = filter_var($data['nom'], FILTER_SANITIZE_STRING);

			try {
			   $categorie->save();
			} catch (Exception $e) {
			    $app->response->headers->set('Content-type','application/json') ;
		        $app->halt(400, json_encode(array("erreur_message"=>"Mauvais formats des donées envoyées")));
			}

			$rootUri = $app->request->getUrl();
			$rootUri .= $app->request->getRootUri();

			$app->response->setStatus(200) ;
			$app->response->headers->set('Content-type','application/json') ;
			echo $categorie->toJson();
		}else{
			$app->response->headers->set('Content-type','application/json') ;
	        $app->halt(500, json_encode(array("erreur_message"=>"Impossible de créer la ressource")));
		}
	}

	public function getEvent($id){
		$app = \Slim\Slim::getInstance();
		// Get request object
		$req = $app->request;
		//Get root URI
		$rootUri = $req->getUrl();
		$rootUri .= $req->getRootUri();

		$categorie = Categorie::find($id);
		if (!empty($categorie)){
			//$evenements = Event::where("idCategorie", "=", $categorie->id)->get();
			
			$evenements = Event::select('id','titre','description', 'lieu', 'idOrganisateur', 'idStatut', 'dateEvenement')->where("idCategorie", "=", $categorie->id)->get();
		
		if( ! empty($evenements)){
			$all = array();
			foreach ($evenements as $key => $evenement){
	            $value['evenement'] = $evenement; //->toJson() pas sur de le mettre .. 
				$value['links'] = array(array('rel' => "self",
										'href' => "$rootUri/api/evenements/".$evenement->id),
										array('rel' => "organisateur",
										'href' => "$rootUri/api/organisateurs/".$evenement->idOrganisateur),
										);
				$event[]=$value;
			}

			$nbOrganisateurs = $evenements->count();
			$all['evenements'] = $event;
			$all['links'] = array(array('rel' => 'prev',
									'href' => "$rootUri/api/evenements/?limit=10&offset=150"),
									array('rel' => 'next',
									'href' => "$rootUri/api/evenements/?limit=10&offset=0"),
									array('rel' => 'first',
									'href' => "$rootUri/api/evenements/?limit=10&offset=150"),
									array('rel' => 'last',
									'href' => "$rootUri/api/evenements/?limit=10&offset=$nbOrganisateurs"),
									);
		}
			/*
			$res = array();
			//$res['categorie'] = $categorie->nom;
			foreach ($evenements as $event){
				$res[] = array("evenement"=>$event, "href"=> "$rootUri/api/evenements/$event->id");
			}
			$all['evenements'] = $res;

			$all['links'] = array(array('rel' => 'prev',
									'href' => "$rootUri/api/categories/$id/evenements/?limit=10&offset=150"),
									array('rel' => 'next',
									'href' => "$rootUri/api/categories/$id/evenements/?limit=10&offset=0"),
									array('rel' => 'first',
									'href' => "$rootUri/api/categories/$id/evenements/?limit=10&offset=150"),
									array('rel' => 'last',
									'href' => "$rootUri/api/categories/$id/evenements/?limit=10&offset=200"),
									);*/


			$app->response->setStatus(200) ;
			$app->response->headers->set('Content-type','application/json') ;
			echo json_encode($all);
		}else{
			$app->response->headers->set('Content-type','application/json') ;
	        $app->halt(404, json_encode(array("erreur_message"=>"categorie non trouvée")));
		}
	}
}