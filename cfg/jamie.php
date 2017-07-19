<?php

# config values

define("DBHOST","localhost");
define("DBUSER","root");
define("DBPASS","columbia");
define("DBFILE","instatracks");

define("APP_ROOT","/var/www/instatracks/");

define("TEMPLATES",APP_ROOT.'templates/');
define("COMPONENTS",APP_ROOT.'components/');


date_default_timezone_set('Europe/London');

putenv('GOOGLE_APPLICATION_CREDENTIALS=creds/google.json');

