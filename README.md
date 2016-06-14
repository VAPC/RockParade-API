RockParade
==========

[![Build Status](https://scrutinizer-ci.com/g/Vehsamrak/RockParade/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Vehsamrak/RockParade/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/Vehsamrak/RockParade/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Vehsamrak/RockParade/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Vehsamrak/RockParade/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Vehsamrak/RockParade/?branch=master)

Requirements
============
* PHP 7
* MySQL database

All requirements are listed in **composer.json**.

How to run server
=================
1. Install composer dependencies and answer interactive configuration questions: `composer install`.
2. If composer points on unmet system requirements such as php version or extensions, install them.
3. Create database `bin/console doctrine:database:create`.
4. Apply database schema `bin/console doctrine:schema:update --force`.
5. Run local application webserver `bin/console server:run`. Server will run on http://127.0.0.1:8000

API documentation
=================
Run server and go to http://127.0.0.1:8000/api. You can view documentation and try queries in sandbox.

Contribution
============
1. Take issue from issue board.
2. Create new branch with conventional name: **t-IssueNumber-feature_description** 
(please use underscore in description part of branch name)
3. When work is done, create pull request from your branch to master.