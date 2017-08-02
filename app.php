<?php

if($_SERVER['argc'] < 2) {
	die ('Usage: php app.php [instanceID] [-v]'."\n");
}

chdir(dirname($_SERVER['argv'][0]));

require_once('config.php');
require_once('engine/database.php');
require_once('engine/instatracks.php');

require_once('aws/vendor/autoload.php');
require_once('google/vendor/autoload.php');
require_once('vision/vendor/autoload.php');
//require_once('ffmpeg/vendor/autoload.php');

/* todo - clean up */
require('test-lyrics.php');

$lyrics_copy = json_decode($lyrics_copy);




$instanceID = $_SERVER['argv'][1];

$db = PDOManager::getInstance();

use Google\Cloud\Vision\VisionClient;
$client = new Google_Client();
$client->useApplicationDefaultCredentials();

$i = new Instatracks;
$i->setInstance($instanceID);
$i->setLyrics($lyrics_copy);
$i->setVision(new VisionClient(['projectId' => 'node-instatracks']));
$i->setPolly(new Aws\Polly\PollyClient(['version' => 'latest', 'region' => 'eu-west-1']));
$i->setS3(new Aws\S3\S3Client(['version' => 'latest', 'region' => 'eu-west-1']));
$i->setDB($db);
$i->execute();
