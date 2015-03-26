<?php
namespace media\controller ;
use \media\modele ;
use \media\view ;
use \media\script\CRSFGuard ;

class OauthController extends AbstractController{
	public function __construct(){

	}	

	public function form_signin(){ //si URL redirect différente de celle renseignée dans la BDD au moment de l'enregistrement du client, on l'envoit se faire foutre
		if (isset($_GET['url_redirect'], $_GET['id_client'])){
			//CRSFGuard
			$redirect_url = filter_var($_GET['url_redirect'], FILTER_SANITIZE_STRING);
			$id_client = filter_var($_GET['id_client'], FILTER_SANITIZE_STRING);

			$client = modele\OauthClient::find($id_client);
			$nameClient = $client->nom;
			if (!empty($client) && $client != null){
				if ($client->redirect_auth_code == $redirect_url){
					$v = new view\ViewFormSignin($redirect_url, $id_client, $nameClient) ;
					$v->display();
				}else{
					$v = new View\ViewErreur("erreur dans l'url fourni");
					$v->display();
					exit();
				}
			}else{
				$v = new View\ViewErreur("erreur client inconnu");
				$v->display();
				exit();
			}
		}else{
			$v = new View\ViewErreur("données manquantes");
			$v->display();
			exit();
		}
	}

	public function traitement_signin(){ //TODO verifier que y'a pas deux login pareil dans la m^mee table
		$app = \Slim\Slim::getInstance();
		$rootUri = $app->request->getUrl();
		$rootUri .= $app->request->getRootUri();

		$crsfGuard = new CRSFGuard();
		if ($crsfGuard::csrfguard_validate_token($_POST['CSRFName'], $_POST['CSRFToken'])){
			if (isset($_POST['login'], $_POST['password']/*, $_POST['type']*/, $_POST['url_redirect'], $_POST['id_client'])){
				//CRSFGuard
				//$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
				$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
				$login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
				$url_redirect = filter_input(INPUT_POST, 'url_redirect', FILTER_SANITIZE_STRING);
				$id_client = filter_input(INPUT_POST, 'id_client', FILTER_SANITIZE_STRING);

				//TODO : Verifier si url_redirect correspond à l'id_client

				/*if ($type == "participant"){
					$user = modele\Participants::where("login", "=", $login)->first();
				}else if($type == "organisateur"){
					$user = modele\Organisateurs::where("login", "=", $login)->first();
				}else{
					echo "mauvais login";
				}*/

				$user = modele\Participants::where("login", "=", $login)->first();
				if ($user == null){
					$user = modele\Organisateurs::where("login", "=", $login)->first();
				}
				if($user == null){
					//echo "mauvais login, introuvable dans Particpant et Organisateurs";
					$v = new View\ViewErreur("Erreur de login");
					$v->display();
					exit();
				}			

				if ($user != null){
					$password_serveur = $user->password;
					if(password_verify($password, $password_serveur)){
						//faire code
						//$auth_code = "test";

						$client = modele\OauthClient::find($id_client);
						$url_redirect = $client->redirect_auth_code; //faire verif avec celui envoyé dans le POST

						$token = uniqid(mt_rand(), true);

						$authCode = new modele\OauthCode ;
						$authCode->auth_code = $token;
						$authCode->id_client = $id_client;
						$authCode->owner_id = $login;
						//$authCode->redirect_uri = $url_redirect; //récup via l'objet Client!
						$authCode->save(); //try catch
						//TODO : expire time

						header( "Location: $url_redirect?auth_code=$token&url_accessToken=$rootUri/oauth/exangeTokenAccess");  //renvoyer en parametre une URI de retour vers le serveur pour échanger le code contre l'access token?
						exit();
						  //header("Location: mapage.php");   

					}else{
						$v = new View\ViewErreur("Erreur de login");
						$v->display();
						exit();
					}
				}else{
					//echo "user = null";
					$v = new View\ViewErreur("Erreur de login");
					$v->display();
					exit();
				}
			}else{
				//echo "erreur dans les POST";
				$v = new View\ViewErreur("Erreur de login");
				$v->display();
				exit();
			}
		}else{
			//CRFSGuard
			$v = new View\ViewErreur();
			$v->display();
			exit();
		}
	}

	public function exangeTokenAccess(){
		if (isset($_GET['client_id'], $_GET['client_secret'], $_GET['url_redirect'], $_GET['auth_code'])){
			$client_id = filter_var($_GET['client_id'], FILTER_SANITIZE_STRING);
			$client_secret = filter_var($_GET['client_secret'], FILTER_SANITIZE_STRING);
			$url_redirect = filter_var($_GET['url_redirect'], FILTER_SANITIZE_STRING);
			$auth_code = filter_var($_GET['auth_code'], FILTER_SANITIZE_STRING);

			$client = modele\OauthClient::find($client_id);
			$modeleAuthCode = modele\OauthCode::find($auth_code); //faire un belongs To

			if ($client != null && $modeleAuthCode != null){
				if(($client->redirect_access_token == $url_redirect) && ($client->secret == $client_secret) && ($client->id == $modeleAuthCode->id_client) ){
					$accessToken = uniqid(mt_rand(), true);
					$oauth_token = new modele\OauthToken;

					$oauth_token->access_token = $accessToken;
					$oauth_token->id_client = $client_id;
					$oauth_token->owner_id = $modeleAuthCode->owner_id;


					//gestion des scopes
					$login = $modeleAuthCode->owner_id;
					$user = modele\Participants::where("login", "=", $login)->first();
					if ($user != null){
						$scope = "Participant";
					}else{
						$user = modele\Organisateurs::where("login", "=", $login)->first();
						if ($user != null){
							$scope = "Organisateur";
						}else{
							$scope = "no_scope";
						}
					}
					$oauth_token->scope = $scope;

					$oauth_token->save(); //try catch
					//TODO : expire Time

					$modeleAuthCode->delete();

					header( "Location: $url_redirect?accessToken=$accessToken");  //renvoyer en parametre une URI de retour vers le serveur pour échanger le code contre l'access token?
					exit();

				}else{
					//echo "erreur les données envoyées ne coresspondent pas à ce qui est en base";
					$v = new View\ViewErreur("Erreur dans les données envoyées");
					$v->display();
					exit();
				}
			}else{
				//echo "erreur client null || modeleAuthCode null";
				$v = new View\ViewErreur("Erreur d'authentification");
				$v->display();
				exit();
			}
		}else{
			//echo "erreur dans les GET";
			$v = new View\ViewErreur("Erreur dans les données envoyées");
			$v->display();
			exit();
		}
	}

	public function disconnect(){
		$accessToken = $_GET['accessToken'];
		$token = modele\OauthToken::find($accessToken);
		$token->delete();
	}

	public function infos(){
		$app = \Slim\Slim::getInstance();
		$accessToken = $_GET['accessToken'];
		$token = modele\OauthToken::find($accessToken);
		$owner_id = $token->owner_id;

		$user = modele\Participants::where("login", "=", $owner_id)->first();
		$type = "Participant";
		if ($user == null){
			$user = modele\Organisateurs::where("login", "=", $owner_id)->first();
			$type = 'Organisateur';
		}
		if ($user == null){
			/* Invalid */
		    $app->response->headers->set('Content-type','application/json') ;
	        $app->halt(500, json_encode(array("erreur_message"=>'Aucun user')));
		}

		$app->response->setStatus(200) ;
		$app->response->headers->set('Content-type','application/json') ;
		echo json_encode(array('id' => $user->id, 'login' => $user->login, 'type' => $type));

	}

	public function signinClient(){
		$crsfGuard = new CRSFGuard();
		if ($crsfGuard::csrfguard_validate_token($_POST['CSRFName'], $_POST['CSRFToken'])){
			if (isset($_POST['login'],$_POST['secret'])){
				$secret = filter_input(INPUT_POST, 'secret', FILTER_SANITIZE_STRING);
				$login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);

				$client = modele\OauthClient::where("nom", "=", $login)->first();
				if ($client != null){
					$v = new View\ViewErreur("Login déja utilisé");
					$v->display();
					exit();
				}else{
					$client = new modele\OauthClient();
					$client->nom = $login;
					$client->secret = $secret;
					$client->id = uniqid(mt_rand(), true); //TODO : Un truk plus lisible peu ètre?

					$client_access_token = uniqid(mt_rand(), true);
					$client->client_access_token = $client_access_token;

					$client->save();

					$v = new View\ViewRegistrationOK($client_access_token);
					$v->display();
					exit();
				}
			}else{
				$v = new View\ViewErreur("Erreur dans les données envoyées");
				$v->display();
				exit();
			}
		}else{
			//CRFSGuard
			$v = new View\ViewErreur();
			$v->display();
			exit();
		}
	}
}