#### Déploiement

- Démarrer sa VM sur Kourou (Démarrer la VM) [https://kourou.oclock.io/ressources/vm-cloud/]

- On s'y connecte via le terminal ssh student@martinferret-server.eddi.cloud

- Commencer le déploiement

  - Se placer dans cd /var/www/html/

  - Cloner son projet (pour avoir le lien : git remote -v) puis git clone git@github.com:O-clock-Yuna/oflix-MartinFerret.git

  - Se placer dans le dossier et faire un composer install

  - Creer et éditer le fichier .env.local en lancant sudo nano .env.local et copier le lien de Database (CTRL + 0, Entrée, CTRL + X)

  - On créer la base de donnée : bin/console d:d:c

  - Lancer le dⓂ️m

  - Lancer les fixtures

  - Lancer composer require symfony/apache-pack pour creer le fichier htaccess, répondre yes pour les recipes

    - Si problème dans les liens = problème htaccess, lancer :

    ```
    sudo php -r "file_put_contents('/etc/apache2/apache2.conf', str_replace('AllowOverride None', 'AllowOverride All', file_get_contents('/etc/apache2/apache2.conf')));"
    sudo service apache2 restart
    ```
    


  - Lancer APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear pour vider le cache

  - Lancer bin/console lexik:jwt:generate-keypair pour le JWT

  - Lancer sudo nano .env et modifier en APP_ENV=prod

  - Si le login ne marche pas pour se connecter au site, mettre cette fonction dans src/Security/LoginFormAuthenticator :

    
    public function supports(Request $request): bool
{
    return $request->isMethod('POST') && '/login' === $request->getPathInfo();
}
    


  - Ne pas oublier de push puis pull pour prendre en compte les modifications