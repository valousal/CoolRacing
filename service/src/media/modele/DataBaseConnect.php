<?php
namespace media\modele ;
use \Illuminate\Database\Capsule\Manager as Capsule;

class DataBaseConnect{

	private static $capsule;


	public static function setConfig($file){
		if (isset($file)){
				self::$capsule = new Capsule;
				$config = parse_ini_file($file);

				self::$capsule->addConnection($config);  //Faire un config.ini dans le même ordre, et le foutre dans le .gitignore
				self::$capsule->bootEloquent();
		}else{ echo "no config_file found"; }
	}
}