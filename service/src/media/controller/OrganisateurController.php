<?php
namespace media\controller ;
use \media\modele\Organisateurs ;
use \media\modele\Adresse ;
use \media\modele\Statut ;

class OrganisateurController extends AbstractController{
	public function __construct(){

	}	

	//get 1 organisateur
	public function get($id){
		$organisateur = Organisateurs::with('adresse')->find($id);
		
		$app = \Slim\Slim::getInstance();
		// Get request object
		$req = $app->request;
		//Get root URI
		$rootUri = $req->getUrl();
		$rootUri .= $req->getRootUri();

		if( ! empty($organisateur)){
			$organisateurArray = array();
			$organisateurArray['id'] = $organisateur->id;
			$organisateurArray['nom_structure'] = $organisateur->nom_structure;
			$organisateurArray['forme_juridique'] = $organisateur->forme_juridique;
			$organisateurArray['nomResponsable'] = $organisateur->nomResponsable;
			$organisateurArray['prenomResponsable'] = $organisateur->prenomResponsable;
			$organisateurArray['dateInscription'] = $organisateur->dateInscription;
			$organisateurArray['login'] = $organisateur->login;
			$organisateurArray['mailPub'] = $organisateur->mailPub;
			$organisateurArray['tel'] = $organisateur->tel;
			$organisateurArray['mailResponsable'] = $organisateur->mailResponsable;
			$organisateurArray['adresse'] = $organisateur->adresse;
			
			$links = array('rel' => "evenements",
									'href' => "$rootUri/api/organisateurs/".$organisateur->id."/evenements");
			

			$app->response->setStatus(200) ;
			$app->response->headers->set('Content-type','application/json') ;
			echo(json_encode(array("organisateur" => $organisateurArray, "links" => $links)));
		}else{
			/* Invalid */
		     $app->response->headers->set('Content-type','application/json') ;
	         $app->halt(500, json_encode(array("erreur_message"=>"Organisateur n'existe plus/pas")));
		}
	}

	//recupère tous les organisateurs
	public function getAll(){
		$app = \Slim\Slim::getInstance();

		// Get request object
		$req = $app->request;
		//Get root URI
		$rootUri = $req->getUrl();
		$rootUri .= $req->getRootUri();

		if(isset($_GET['nom_structure'])){
			$organisateurs = Organisateurs::where('nom_structure', 'like', '%'.$_GET['nom_structure'].'%')->with("adresse")->get();
		}else{
			$organisateurs = Organisateurs::with("adresse")->get();
		}
		
		
		if( ! empty($organisateurs)){
			$all = array();
			$organsiateurArray = array();
			foreach ($organisateurs as $key => $organisateur){
				$organisateurArray = array();
				$organisateurArray['id'] = $organisateur->id;
				$organisateurArray['nom_structure'] = $organisateur->nom_structure;
				$organisateurArray['forme_juridique'] = $organisateur->forme_juridique;
				$organisateurArray['nomResponsable'] = $organisateur->nomResponsable;
				$organisateurArray['prenomResponsable'] = $organisateur->prenomResponsable;
				$organisateurArray['dateInscription'] = $organisateur->dateInscription;
				$organisateurArray['login'] = $organisateur->login;
				$organisateurArray['mailPub'] = $organisateur->mailPub;
				$organisateurArray['tel'] = $organisateur->tel;
				$organisateurArray['mailResponsable'] = $organisateur->mailResponsable;
				$organisateurArray['adresse'] = $organisateur->adresse;
	         
	            $value['organisateur'] = $organisateurArray; //->toJson() pas sur de le mettre .. 
				$value['links'] = array(array('rel' => "self",
										'href' => "$rootUri/api/organisateurs/".$organisateur->id),
										array('rel' => "evenements",
										'href' => "$rootUri/api/organisateurs/".$organisateur->id."/evenements"),
										);
				$all[]=$value;
			}

			$nbOrganisateurs = $organisateurs->count();

			$all['links'] = array(array('rel' => 'prev',
									'href' => "$rootUri/api/organisateurs/?limit=10&offset=150"),
									array('rel' => 'next',
									'href' => "$rootUri/api/organisateurs/?limit=10&offset=0"),
									array('rel' => 'first',
									'href' => "$rootUri/api/organisateurs/?limit=10&offset=150"),
									array('rel' => 'last',
									'href' => "$rootUri/api/organisateurs/?limit=10&offset=$nbOrganisateurs"),
									);



			$app->response->setStatus(200) ;
			$app->response->headers->set('Content-type','application/json') ;
			echo json_encode($all);
		}else{
			/* Invalid */
		    $app->response->headers->set('Content-type','application/json') ;
	        $app->halt(500, json_encode(array("erreur_message"=>'Aucun organisateur')));
		}
	}


	//Recupère les evenements d'un organisateur
	public function getEventsOrg($id){
		$app = \Slim\Slim::getInstance();
		// Get request object
		$req = $app->request;
		//Get root URI
		$rootUri = $req->getUrl();
		$rootUri .= $req->getRootUri();


		if(isset($_GET['statut'])){
			$s = $_GET['statut'];
		}else{
			$s = 'noStatut';
		}

		$idstatut = null;
		if($s != 'noStatut'){
			$statut_search = Statut::where('label', '=', $s)->first();
			if($statut_search != null){
				$idstatut = $statut_search->id;
			}
		}

		$evenements = Organisateurs::find($id)->evenements()->where(function($query) use($s,$idstatut) {
		    if($s!='noStatut'){
		        $query->where('idStatut', '=', "$idstatut");
		    }})
		->get();



		if( ! empty($evenements)){
			$all = array();
			foreach ($evenements as $key => $evenement){
				$EventOrgArray = array();
				$EventOrgArray['id'] = $evenement->id;
				$EventOrgArray['titre'] = $evenement->titre;
				$EventOrgArray['dateCreation'] = $evenement->dateCreation;
				$EventOrgArray['description'] = $evenement->description;
				$EventOrgArray['lieu'] = $evenement->lieu;
				$EventOrgArray['dateCloture'] = $evenement->dateCloture;
				$EventOrgArray['dateOuverture'] = $evenement->dateOuverture;
				$EventOrgArray['dateEvenement'] = $evenement->dateEvenement;
				$EventOrgArray['sessions'] = $evenement->sessions;
				$EventOrgArray['nombrePlace'] = $evenement->nombrePlace;
				$EventOrgArray['prix'] = $evenement->prix;
				$EventOrgArray['distance'] = $evenement->distance;
				$EventOrgArray['statut'] = $evenement->statut->label;
	            
	            $value['evenement'] = $EventOrgArray; //->toJson() pas sur de le mettre .. 
				$value['links'] = array(array('rel' => "self",
										'href' => "$rootUri/api/evenements/".$evenement->id),
										array('rel' => "organisateurs",
										'href' => "$rootUri/api/organisateurs/".$evenement->organisateur->id),
										);
				$event[]=$value;
			}

			$nbEvenements = $evenements->count();
			$all['evenements'] = $event;
			$all['links'] = array(array('rel' => 'prev',
									'href' => "$rootUri/api/evenements/?limit=10&offset=150"),
									array('rel' => 'next',
									'href' => "$rootUri/api/evenements/?limit=10&offset=0"),
									array('rel' => 'first',
									'href' => "$rootUri/api/evenements/?limit=10&offset=150"),
									array('rel' => 'last',
									'href' => "$rootUri/api/evenements/?limit=10&offset=$nbEvenements"),
									);



			$app->response->setStatus(200) ;
			$app->response->headers->set('Content-type','application/json') ;
			echo json_encode($all);
		}else{
			/* Invalid */
		    $app->response->headers->set('Content-type','application/json') ;
	        $app->halt(500, json_encode(array("erreur_message"=>'Aucun evenement')));
		}
	}


	//Ajouter un organisateur
	public function addOrg(){  //TODO verifier que y'a pas deux login pareil dans la m^mee table
		$app = \Slim\Slim::getInstance();
		$req = $app->request->getBody();
		$data = (json_decode($req, true)); // decode les données envoyées sous forme de tableau associatif

		//adresse
		$dataAdresse = $data['adresse'];
		
		if (isset($data['nom_structure'], $data['forme_juridique'], $data['mailPrivate'], $data['login'], $data['password'], $data['nomResponsable'],$data['prenomResponsable'],$data['mailResponsable'])){ //si les variables existent
			$nom_structure = filter_var($data['nom_structure'], FILTER_SANITIZE_STRING);
			$forme_juridique = filter_var($data['forme_juridique'], FILTER_SANITIZE_STRING);
			$mailPrivate = filter_var($data['mailPrivate'], FILTER_SANITIZE_EMAIL);
			$login = filter_var($data['login'], FILTER_SANITIZE_STRING);
			$password = filter_var($data['password'], FILTER_SANITIZE_STRING);
			$nomResponsable = filter_var($data['nomResponsable'], FILTER_SANITIZE_STRING);
			$prenomResponsable = filter_var($data['prenomResponsable'], FILTER_SANITIZE_STRING);
			$mailResponsable = filter_var($data['mailResponsable'], FILTER_SANITIZE_EMAIL);
		}else{
			$app->response->headers->set('Content-type','application/json') ;
	        $app->halt(400, json_encode(array("erreur_message"=>"Requête incomplete, données obligatoires manquantes")));
		}

		$organisateur = new Organisateurs; // $organisateur = new Organisateurs($data);
		$organisateur->nom_structure = $nom_structure;
		$organisateur->forme_juridique = $forme_juridique;
		$organisateur->mailPrivate = $mailPrivate;
		$organisateur->login = $login;
		$organisateur->password = password_hash($password, PASSWORD_BCRYPT, array("cost" => 10));
		$organisateur->mailPrivate = $mailPrivate;
		$organisateur->nomResponsable = $nomResponsable;
		$organisateur->prenomResponsable = $prenomResponsable;
		$organisateur->mailResponsable = $mailResponsable;

		if(!empty($data['tel'])){
			$organisateur->tel = filter_var($data['tel'], FILTER_SANITIZE_NUMBER_INT);
		}
		if(!empty($data['mailPub'])){
			$organisateur->mailPub = filter_var($data['mailPub'], FILTER_SANITIZE_EMAIL);
		}

		$organisateur->dateInscription = date('Y-m-d');

		//Adresse 
		// $adresse = new Adresse($dataAdresse);
		$adresse = new Adresse;
		$adresse->num = $dataAdresse['num'];
		$adresse->type = $dataAdresse['type'];
		$adresse->rue = $dataAdresse['rue'];
		$adresse->CP = $dataAdresse['CP'];
		$adresse->ville = $dataAdresse['ville'];
		

		try {
			$adresse->save();
			$organisateur->idAdress = $adresse->id;
			$organisateur->save();
		} catch (Exception $e) {
		    $app->response->headers->set('Content-type','application/json') ;
	        $app->halt(400, json_encode(array("erreur_message"=>"Mauvais formats des données envoyées")));
		}

		/***************************************************************************/
		/*TODO: licences, description licences et Adresse Cryptage Pas*******************/
		/***************************************************************************/
		$rootUri = $app->request->getUrl();
		$rootUri .= $app->request->getRootUri();

		$app->response->setStatus(201) ;
		$app->response->headers->set('Content-type','application/json') ;
		$res = array("organisateur"=>array("nom_structure"=>$organisateur->nom_structure, "forme_juridique"=>$organisateur->forme_juridique, 
					"login"=>$organisateur->login, "nomResponsable"=>$organisateur->nomResponsable, 
					"prenomResponsable"=>$organisateur->prenomResponsable, "mailResponsable"=>$organisateur->mailResponsable, 
					"tel"=>$organisateur->tel, "mailPub"=>$organisateur->mailPub, "dateInscription"=>$organisateur->dateInscription, "adresse" => $adresse),
					 "links"=>array("href"=>"$rootUri/api/organisateurs/".$organisateur->id));
		// echo json_encode(array('organisateur'=>$organisateur /*->toJson()*/, 'href'=>"$rootUri/api/organisateurs/".$organisateur->id));
		echo json_encode($res);
	}	


	//Modifie un organisateur
	public function putOrg($id){
		$app = \Slim\Slim::getInstance();
		$req = $app->request->getBody();
		$data = (json_decode($req, true)); // decode les données envoyées sous forme de tableau associatif
		
		//adresse
		$dataAdresse = $data['adresse'];

		if (isset($data['nom_structure'], $data['forme_juridique'], $data['mailPrivate'], $data['login'], $data['password'], $data['nomResponsable'],$data['prenomResponsable'],$data['mailResponsable'])){ //si les variables existent
			$nom_structure = filter_var($data['nom_structure'], FILTER_SANITIZE_STRING);
			$forme_juridique = filter_var($data['forme_juridique'], FILTER_SANITIZE_STRING);
			$mailPrivate = filter_var($data['mailPrivate'], FILTER_SANITIZE_EMAIL);
			$login = filter_var($data['login'], FILTER_SANITIZE_STRING);
			$password = filter_var($data['password'], FILTER_SANITIZE_STRING);
			$nomResponsable = filter_var($data['nomResponsable'], FILTER_SANITIZE_STRING);
			$prenomResponsable = filter_var($data['prenomResponsable'], FILTER_SANITIZE_STRING);
			$mailResponsable = filter_var($data['mailResponsable'], FILTER_SANITIZE_EMAIL);
		}else{
			$app->response->headers->set('Content-type','application/json') ;
	        $app->halt(400, json_encode(array("erreur_message"=>"Requête incomplete, données obligatoires manquantes")));
		}

		$organisateur = Organisateurs::find($id);
		$organisateur->nom_structure = $nom_structure;
		$organisateur->forme_juridique = $forme_juridique;
		$organisateur->mailPrivate = $mailPrivate;
		$organisateur->login = $login;
		$organisateur->password = password_hash($password, PASSWORD_BCRYPT, array("cost" => 10));
		$organisateur->mailPrivate = $mailPrivate;
		$organisateur->nomResponsable = $nomResponsable;
		$organisateur->prenomResponsable = $prenomResponsable;
		$organisateur->mailResponsable = $mailResponsable;

		if(!empty($data['tel'])){
			$organisateur->tel = filter_var($data['tel'], FILTER_SANITIZE_NUMBER_INT);
		}
		if(!empty($data['mailPub'])){
			$organisateur->mailPub = filter_var($data['mailPub'], FILTER_SANITIZE_NUMBER_INT);
		}

		$organisateur->dateInscription = date('Y-m-d');

		//Adresse 
		// $adresse = new Adresse($dataAdresse);
		$adresse = Adresse::where('id', '=', $organisateur->idAdress)->first();
		$adresse->num = $dataAdresse['num'];
		$adresse->type = $dataAdresse['type'];
		$adresse->rue = $dataAdresse['rue'];
		$adresse->CP = $dataAdresse['CP'];
		$adresse->ville = $dataAdresse['ville'];

		try {
		  	$adresse->save();
			$organisateur->save();
		} catch (Exception $e) {
		    $app->response->headers->set('Content-type','application/json') ;
	        $app->halt(400, json_encode(array("erreur_message"=>"Mauvais formats des données envoyées")));
		}

		/***************************************************************************/
		/*TODO: licences, description licences et Adresse Cryptage Pas*******************/
		/***************************************************************************/
		$rootUri = $app->request->getUrl();
		$rootUri .= $app->request->getRootUri();

		$app->response->setStatus(201) ;
		$app->response->headers->set('Content-type','application/json') ;
		$res = array("organisateur"=>array("nom_structure"=>$organisateur->nom_structure, "forme_juridique"=>$organisateur->forme_juridique, 
					"login"=>$organisateur->login, "nomResponsable"=>$organisateur->nomResponsable, 
					"prenomResponsable"=>$organisateur->prenomResponsable, "mailResponsable"=>$organisateur->mailResponsable, 
					"tel"=>$organisateur->tel, "mailPub"=>$organisateur->mailPub, "dateInscription"=>$organisateur->dateInscription,"adresse" => $adresse),
					 "links"=>array("href"=>"$rootUri/api/organisateurs/".$organisateur->id));
		// echo json_encode(array('organisateur'=>$organisateur /*->toJson()*/, 'href'=>"$rootUri/api/organisateurs/".$organisateur->id));
		echo json_encode($res);
	}


	//Supprime un organisateur
	public function deleteOrg($id){
		$organisateur = Organisateurs::find($id);
		
		$app = \Slim\Slim::getInstance();
		try{
			$organisateur->delete(); //rajouter une confirmation?
		}catch(Exception $e){
		    $app->response->headers->set('Content-type','application/json') ;
	        $app->halt(400, json_encode(array("erreur_message"=>"Impossible de supprimer la ressource")));
		}
		$app->response->setStatus(200) ;
		$app->response->headers->set('Content-type','application/json') ;
		echo json_encode(array("delete"=>"ok"));
	}
}