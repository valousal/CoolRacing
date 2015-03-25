## Fichier service/config/config.php
Le fichier n'est pas suivi par git, pour éviter de divulguer les identifiants de la DB.
Configurez les accès à la DB. La base de données doit exister avant de commencer.

## Virtual Host simple - SLIM

Pensez à modifier le PATH du DocumentRoot et du Directory


```
#!php

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
