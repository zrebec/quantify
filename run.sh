#!/usr/bin/env zsh
#Using generateCSV.php is now obsolete. Primary init data is in JSON file
#php generateCSV.php && \
composer run import && \
php -S localhost:8000 -t public