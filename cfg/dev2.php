<?php

# config values

define("DBHOST","localhost");
define("DBUSER","root");
define("DBPASS","m0bilem3");
define("DBFILE","instatracks");

define("APP_ROOT","/var/www/instatracks/");
define("WEB_ROOT","http://instagram.movement.co.uk/");

define("TEMPLATES",APP_ROOT.'templates/');
define("COMPONENTS",APP_ROOT.'components/');


date_default_timezone_set('Europe/London');

putenv('GOOGLE_APPLICATION_CREDENTIALS=creds/google.json');

define("INSTAGRAM_KEY","ac5f0035b15e4224a8fd703ed9dc6887");
define("INSTAGRAM_SECRET","7f750742907941f8a59a1c59927625e4");

