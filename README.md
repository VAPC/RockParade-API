RockParade
==========

A Symfony project created on May 22, 2016, 10:50 pm.

Requirements
============
All requirements are listed in *composer.json*.

How to run server
=================
1. Install composer dependencies and answer interactive configuration questions: `composer install`.
2. If composer points on unmet system requirements such as php version or extensions, install them.
3. Create database `bin/console doctrine:database:create`.
4. Apply database schema `bin/console doctrine:schema:update --force`.
5. Run local application webserver `bin/console server:run`. Server would run on http://127.0.0.1:8000

API documentation
=================
Run server and go to http://localhost:8000/api 

You can view documentation and try queries in sandbox.
