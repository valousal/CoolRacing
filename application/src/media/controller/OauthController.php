<?php
namespace media\controller ;
use \media\view ;



/****************************** GUZZLE *******************************/
use GuzzleHttp\Client; //Guzzle init - Obligatoire
use GuzzleHttp\EntityBody; //Recuperation Body reponse 
use GuzzleHttp\Event\CompleteEvent; //Requete parrallèle (voir docs)

class OauthController extends AbstractController{
	

	public function __construct(){
	}

	public function jeton(){
		$auth_code = $_GET['auth_code'];
		$url_accessToken = $_GET['url_accessToken'];
		header("Location: $url_accessToken?client_id=coolracing&client_secret=azerty&url_redirect=http://coolracing/application/oauth/access_token&auth_code=$auth_code");
		exit();

		/*$client = new Client();
		$response = $client->get("$url_accessToken?client_id=coolracing&client_secret=azerty&url_redirect=http://coolracing/application/oauth/access_token&auth_code=$auth_code");
		$body = $response->getBody();
		var_dump($body);*/
	}	

	public function token(){
		$_SESSION['is_logged'] = true;
		$_SESSION['accessToken'] = $_GET['accessToken'];

		$client = new Client([
		    'base_url' => 'http://coolracing/service/oauth/',
		    'defaults' => [
		        //'headers' => ['Foo' => 'Bar'],
		        'query'   => ['accessToken' => $_SESSION['accessToken'], 'client_access_token' => '209592932550acf353b2528.70564490']
		        //'auth'    => ['username', 'password'],
		        //'proxy'   => 'tcp://localhost:80'
		    ]
		]);

		$response = $client->get('infos_users');
		$body = $response->getBody();
		$body = json_decode($body, true);
		$_SESSION['id'] = $body['id'];
		$_SESSION['login'] = $body['login'];
		$_SESSION['type'] = $body['type'];

		$app = \Slim\Slim::getInstance();
		$app->response->redirect($app->urlFor('accueil'));
	}

	public function deconnection(){
		$client = new Client([
		    'base_url' => 'http://coolracing/service/oauth/',
		    'defaults' => [
		        //'headers' => ['Foo' => 'Bar'],
		        'query'   => ['accessToken' => $_SESSION['accessToken'], 'client_access_token' => 'token']
		        //'auth'    => ['username', 'password'],
		        //'proxy'   => 'tcp://localhost:80'
		    ]
		]);
		$response = $client->get('disconnect');
		session_destroy();
		$app = \Slim\Slim::getInstance();
		$app->response->redirect($app->urlFor('accueil'));
	}

}