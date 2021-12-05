Next steps are necessary to run project:
   1) install dependencies by "composer install"
   2) php bin/console doctrine:migration:migrate
   3) symfony server:start -d
   4) php bin/console messenger:consume async