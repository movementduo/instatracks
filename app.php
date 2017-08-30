<?php

if($_SERVER['argc'] < 2) {
	die ('Usage: php app.php [instanceID] [-v]'."\n");
}

chdir(dirname($_SERVER['argv'][0]));

require_once('config.php');
require_once('engine/database.php');
require_once('app/instatracks.php');
require_once('app/ffmpeg.php');

require_once('lib/aws/vendor/autoload.php');
require_once('lib/google/vendor/autoload.php');
require_once('lib/vision/vendor/autoload.php');

$instanceID = $_SERVER['argv'][1];

$db = PDOManager::getInstance();

use Google\Cloud\Vision\VisionClient;
$client = new Google_Client();
$client->useApplicationDefaultCredentials();

use Aws\Polly\PollyClient;
$polly = new PollyClient([
	'version' => 'latest',
	'profile' => AWS_PROFILE,
	'region' => AWS_REGION
]);

$i = new Instatracks;
$i->setInstance($instanceID);
$i->setLyrics(json_decode(file_get_contents('lyrics/'.APP_LANGUAGE.'.json')));
$i->setVision(new VisionClient(['projectId' => GOOGLE_PROJECTID]));
$i->setPolly($polly);
$i->setS3(new Aws\S3\S3Client(['version' => 'latest', 'region' => S3_REGION]));
$i->setDB($db);
$i->execute();
