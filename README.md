RockParade
==========

[![Build Status](https://scrutinizer-ci.com/g/Vehsamrak/RockParade/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Vehsamrak/RockParade/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/Vehsamrak/RockParade/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Vehsamrak/RockParade/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Vehsamrak/RockParade/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Vehsamrak/RockParade/?branch=master)

Requirements
============
* Vagrant

All requirements are listed in **composer.json**.

How to run server
=================
1. Inside project directory run `vagrant up --provider=docker`
2. Wait untill environment builds. It may take several minutes at the first time.
3. App will be available at http://127.0.0.1 once built.

API documentation
=================
Last actual documentation is available at http://127.0.0.1/api. You can try queries in sandbox.

Contribution
============
1. Take issue from issue board.
2. Create new branch with conventional name: **t-IssueNumber-feature_description** 
(please use underscore in description part of branch name)
3. When work is done, create pull request from your branch to master.
