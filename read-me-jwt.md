Commande : Composer install 

Dans le .env : 

Remplir le JWT_PASSPHRASE=

par exemple : JWT_PASSPHRASE=apet4life

ensuite faire cette commande : 
php bin/console lexik:jwt:generate-keypair

Dans insomnia : 

route POST : http://localhost:8080/api/login_check

modifier le header : Content-Type - application/json

dans le JSON mettre : 

{
	"username":"meetic-pets@exemple.com",
	"password":"password"
}

Et vous avez un token !

Pour le rentrer dans insomnia: 
Auth -> Bearer Token