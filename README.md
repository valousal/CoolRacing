# Installation #


## Fichier service/config/config.php ##
Le fichier n'est pas suivi par git, pour éviter de divulguer les identifiants de la DB.
Configurez les accès à la DB. La base de données doit exister avant de commencer.

## Composer install ##
A la racine des dossiers "applications" et "services" : un **composer install** est indispensable

## La base de données ##
Importation de la base de données **coolracing** 

## Utilisateurs par défaut ##
### Organisateurs ###
Login : CoursPoney
Password : CoursPoney
### Participants###
Login : Gerard
Password : Gerard

## Liens utiles ##
### Accueil de la doc ###
coolracing/service/doc/
### Accueil de l'application ###
coolracing/application/


## Virtual Host simple - SLIM ##
Pensez à modifier le PATH du DocumentRoot et du Directory

```


<VirtualHost *:80>
    ServerName      coolracing
	DocumentRoot 	/wamp/www/coolracing/application
	ErrorDocument 	404 /404.html
	<Directory "/wamp/www/coolracing/application">
		AllowOverride All
		Order allow,deny
		Allow from all
	</Directory>
</VirtualHost>
```



# Documentation #
Lien : 
