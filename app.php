<?php

if($_SERVER['argc'] < 2) {
	die ('Usage: app.php [instanceID] [-v]'."\n");
}


require_once('config.php');
require_once('engine/database.php');
require_once('engine/instatracks.php');
require_once('aws/vendor/autoload.php');
require_once('google/vendor/autoload.php');
require_once('vision/vendor/autoload.php');
require_once('ffmpeg/vendor/autoload.php');


require('test-data.php');
require('test-lyrics.php');

session_start();
$images = json_decode($json);
$lyrics_copy - json_decode($lyrics_copy);




$db = PDOManager::getInstance();


use Google\Cloud\Vision\VisionClient;
$client = new Google_Client();
$client->useApplicationDefaultCredentials();

$it = new Instatracks($cfg);

$it->setImages($images);
$it->setLyrics($lyrics_copy);

$it->setVision(new VisionClient(['projectId' => 'node-instatracks']));
$it->setPolly(new Aws\Polly\PollyClient(['version' => 'latest', 'region' => 'eu-west-1']));
$it->setS3(new Aws\S3\S3Client(['version' => 'latest', 'region' => 'eu-west-1']));
$it->setDB($db);

//  "SELECT * FROM instanceSlides WHERE instanceId = :x1 ORDER BY RAND()",array($instanceId) 


/*

	S3 Buckets:
	[authtoken]/images/[image_id].jpg
	[authtoken]/speech/[image_id].wav
	[authtoken]/rendered/video||[mungedname].mp4

*/



// SHUFFLE IMAGES

$used_lyrics = array();


$it->execute();








// foreach($six as $key=>$p){
// 	$pollySpeech = $polly->synthesizeSpeech([
//     'OutputFormat' => 'mp3', // REQUIRED
//     'Text' => $p->lyrics, // REQUIRED
//     'TextType' => 'text',
//     'VoiceId' => 'Salli', // REQUIRED
// 	]);

// 	try {
// 		$s3->putObject([
// 	       		'Bucket' => 'instatracks',  // bucket to store in
// 	       		'Key'    => 'test'.'/speech/'.$key.'.mp3', // filename of object stored
// 	       		'Body'   => $pollySpeech->get('AudioStream')->getContents(),
// 	       		'ContentType' => 'audio/mpeg', // image
// 			'ACL'    => 'public-read',
// 		]);
// 	} catch (Aws\S3\Exception\S3Exception $e) {
// 		echo "There was an error uploading the file.\n";
// 	}
// }

die();


# 1 - get the token name, creation type (random|manual) + cdn linked images - if manual, 5 stored in db, if random, 10 stored and picked from db at random

# 2 - create a database session


# 3 - create s3 project folder inside bucket
# 4 - get images from instagram and store in s3 and db against token
# 5 - get images from db {

// SELECT s.* FROM instanceSlides s JOIN instances I ON s.instanceID = i.id ORDER BY RAND()


	# 6 - send images to cloud vision OR aws rekognition - store output in db
	# 	this is where we decide to reject an image. 
	# 7 - generate lyrics from tags via polly, store in s3

	# SELECT s.* FROM instanceSlides s JOIN instances I ON s.instanceID = i.id  WHERE status = 'accepted'
	# if (slides < 4) {
		# random? pick another couple of images
		# manual - bail out message
	#}

# }

/*
STEPS:
1) concat 10 speech tracks into one file

         ffmpeg -i aud001.wav -i aud002.wav -i aud003.wav -i aud004.wav -i aud005.wav -i aud006.wav -i aud007.wav -i aud008.wav -i aud009.wav -i aud010.wav -f lavfi -i anullsrc -filter_complex \
"[10]atrim=duration=0.7[g1];[10]atrim=duration=0.7[g2];[10]atrim=duration=0.7[g3];[10]atrim=duration=0.7[g4];[10]atrim=duration=0.7[g5];[10]atrim=duration=0.7[g6];[10]atrim=duration=0.7[g7];[10]atrim=duration=0.7[g8];[10]atrim=duration=0.7[g9];
 [0][g1][1][g2][2][g3][3][g4][4][g5][5][g6][6][g7][7][g8][8][g9][9]concat=n=19:v=0:a=1"  out.wav


2) merge speech track with backing track

ffmpeg -i backing.mp3 -i out.wav -filter_complex amerge -ac 2 -c:a libmp3lame -q:a 4 finished.mp3

3) merge video with final audio track

ffmpeg -i video.mp4 -i finished.mp3 \
-c:v copy -c:a aac -strict experimental \
-map 0:v:0 -map 1:a:0 fin.mp4


overlay image ("overlay=20:20 top position in pixels", "between(t,0,25) appears from 0-25 seconds")
ffmpeg -i fin.mp4 -i fanta_logo2.png -filter_complex "[0:v][1:v] overlay=20:20:enable='between(t,0,25)'" fin-overlay2.mp4
*/

# 11 - store video in s3
# 12 - update db + kill this session

session_destroy();
