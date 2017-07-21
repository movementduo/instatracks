<?php

# config values

define("DBHOST","localhost");
define("DBUSER","root");
define("DBPASS","columbia");
define("DBFILE","instatracks");

define("APP_ROOT","/Users/jasonluu/Documents/localhost/instatrackslamp/");

define("TEMPLATES",APP_ROOT.'templates/');
define("COMPONENTS",APP_ROOT.'components/');


date_default_timezone_set('Europe/London');

putenv('GOOGLE_APPLICATION_CREDENTIALS=creds/google.json');

