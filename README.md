# projet-4-apet4life-back

## Déploiement du projet back

Dans le terminal:

- ssh student@prenomnom-server.eddi.cloud
- cd /var/www/html/
- Git clone git@github.com:O-clock-Yuna/projet-4-apet4life-back.git
- cd projet-4-apet4life-back
- composer install --ignore-platform-req=ext-curl
- sudo nano .env.local:  
      - Taper cette ligne dans le fichier: `DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=mariadb-10.5.8"`
      - Remplacer le db_user, db_password et le db_name par ses données personnelles(ex: user, password, apet4life)
      - ctrl+O entrer ctrl+x (pour enregistrer ce qu'on a rentré)
- Créer la bdd dans adminer qui correspondent bien avec les noms mis dans le .env.local(ou fait un php bin/console d:d:c)
- Optionnel: Faire un privilège pour la base de donnée
- Supprimer les tables de la bdd(si elle est déjà existante)
- php bin/console d:m:m
- php bin/console d:f:l
- php bin/console lexik:jwt:generate-keypair (génère la clé d'accès pour l'api)
- sudo nano .env 
      - remplacer le # APP_ENV=dev par APP_ENV=prod
- Lancer le serveur php: php -S 0.0.0.0:8080 -t public
