<?php

//die(var_export($_SERVER,true));


require('config.php');
require('classes/database.php');
require('aws/vendor/autoload.php');
require('google/vendor/autoload.php');
require('vision/vendor/autoload.php');

require('test-data.php');

session_start();
use Google\Cloud\Vision\VisionClient;

// Instantiate an Amazon S3 client.
$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'eu-west-1'
]);


$client = new Google_Client();
$client->useApplicationDefaultCredentials();

//creds/google.json

$vision = new VisionClient([
    'projectId' => 'node-instatracks',
]);

/*

	S3 Buckets:
	[authtoken]/images/[image_id].jpg
	[authtoken]/speech/[image_id].wav
	[authtoken]/rendered/video||[mungedname].mp4

*/

$images = json_decode($json);
foreach($images->images as $i) {

	$body = file_get_contents($i->url);

	try {
		$s3->putObject([
        		'Bucket' => 'instatracks',  // bucket to store in
        		'Key'    => '[oauthtoken1]'.'/images/'.$i->id.'.jpg', // filename of object stored
        		'Body'   => $body, // image
			'ACL'    => 'public-read',
		]);
	} catch (Aws\S3\Exception\S3Exception $e) {
		echo "There was an error uploading the file.\n";
	}


	$image = $vision->image(file_get_contents($i->url), ['LABEL_DETECTION','TEXT_DETECTION','FACE_DETECTION','LANDMARK_DETECTION','LOGO_DETECTION','SAFE_SEARCH_DETECTION']);
	$result = $vision->annotate($image);
print("Labels:\n");
	foreach($result->labels() as $label) {
                print("\t".$label->description()."\n");
        }

print("Faces:\n");
foreach ((array) $result->faces() as $face) {
    printf("[tAnger: %s\n", $face->isAngry() ? 'yes' : 'no');
    printf("\tJoy: %s\n", $face->isJoyful() ? 'yes' : 'no');
    printf("\tSurprise: %s\n", $face->isSurprised() ? 'yes' : 'no');
}

$result = $vision->annotate($image);
print("Logos:\n");
foreach ((array) $result->logos() as $logo) {
    print("\t".$logo->description() . PHP_EOL);
}


	$safe = $result->safeSearch();
print("SafeSearch:\n");
printf("\tAdult: %s\n", $safe->isAdult() ? 'yes' : 'no');
printf("\tSpoof: %s\n", $safe->isSpoof() ? 'yes' : 'no');
printf("\tMedical: %s\n", $safe->isMedical() ? 'yes' : 'no');
printf("\tViolence: %s\n", $safe->isViolent() ? 'yes' : 'no');

print("Landmarks:\n");
foreach ((array) $result->landmarks() as $landmark) {
    print("\t".$landmark->description() . PHP_EOL);
}

print "\n";
print "------------\n";
print "\n";


}

die();


# 1 - get the token name, creation type (random|manual) + cdn linked images - if manual, 5 stored in db, if random, 10 stored and picked from db at random

# 2 - create a database session
$db = new Database($cfg);









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
