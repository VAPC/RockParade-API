RockParade
==========

[![Build Status](https://scrutinizer-ci.com/g/VAPC/RockParade-API/badges/build.png?b=master)](https://scrutinizer-ci.com/g/VAPC/RockParade-API/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/VAPC/RockParade-API/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/VAPC/RockParade-API/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/VAPC/RockParade-API/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/VAPC/RockParade-API/?branch=master)

Conversation
============
Join our Slack channel: https://rockparade.slack.com

Requirements
============
All requirements are listed in **composer.json**.

How to run server
=================
1. Run composer install
2. Run symfony server: `php bin/console server:run`
3. Server will be available on [http://localhost:8000](http://localhost:8000)

API documentation
=================
Last actual documentation is available at http://rockparade.creora.ru/api. You can try queries in sandbox.

Contribution
============
1. Take issue from [task tracker board](http://redmine.rockparade.creora.ru/).
* There are several types of tasks in pipeline:
  * New - feature proposals.
  * Architecture - working on requirements, architecture or discussions.
  * Ready for development - features waiting to be implemented. You can pick one of theese.
  * In progress - current tasks in work. There can be only one issue per assignee.
  * Done - issue it must be placed when ready.

2. Create new branch with conventional name: **t-IssueNumber-feature_description** 
(please use underscore in description part of branch name)
3. When work is done, create pull request from your branch to master.
