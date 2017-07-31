<?php

# config values

require_once('config-local.php');

define("TEMPLATES",APP_ROOT.'templates/');
define("COMPONENTS",APP_ROOT.'components/');


date_default_timezone_set('Europe/London');

putenv('GOOGLE_APPLICATION_CREDENTIALS=creds/google.json');

define("INSTAGRAM_KEY","ac5f0035b15e4224a8fd703ed9dc6887");
define("INSTAGRAM_SECRET","7f750742907941f8a59a1c59927625e4");

define('APP_LANGUAGE', 'en-gb');

define('VOCODER_API_LOC', 'http://vocoder.mattjarvis.co.uk/vocoder-api.php');
define('VOCODER_API_SECRET', 'kMrAew7KRpqk5geg5LKSVm9uWAZ6w7VMPJWxj9cC');

define('S3_WEB_ROOT', 'https://s3-eu-west-1.amazonaws.com/instatracks/');
define('S3_BUCKET', 'instatracks');