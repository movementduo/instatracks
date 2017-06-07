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
$safeImages = array();
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

	$image = $vision->image(file_get_contents($i->url), ['LABEL_DETECTION','TEXT_DETECTION','FACE_DETECTION','LANDMARK_DETECTION','SAFE_SEARCH_DETECTION']);
	$result = $vision->annotate($image);

	$safe = $result->safeSearch();

	if($safe->isAdult() || $safe->isSpoof() || $safe->isMedical() || $safe->isViolent()) {
		echo "This image is not safe.\n";
	} else {
		echo "This image is safe to use.\n";

		if($result->logos()){

			//Fanta first priority
			foreach ((array) $result->logos() as $logo) {
				if($logo->description() == "Fanta") {
					print("\tIt's fanta baby!\n"); // 1) Check logo is fanta
				}
			}

		} else if ($result->faces()) {

				//Check faces
				print("Faces:\n");
				$faceCount = sizeof($result->faces());
				if($faceCount > 1){
					printf("\tGroup of friends!\n"); // 2) Check if it's a group of friends
				} else {

					foreach ((array) $result->faces() as $face) {
						if($face->isAngry()){
							printf("\tI'm so angry\n"); // 3) Check angry face
						}
						else if($face->isJoyful()){
							printf("\tI'm so happy\n"); // 4) Check happy face
						}
						else if($face->isSorrowful()){
							printf("\tI'm so sad\n"); // 5) Check sad face
						}
						else if($face->isSurprised()){
							printf("\tI'm so surprised\n"); // 6) Check surprised face
						}
						else{
							printf("\tLooking good there\n"); // 7) Check no emotion detected
						}
					}

				}

		} else if ($result->landmarks()) {

			print("Landmarks:\n");
			foreach ((array) $result->landmarks() as $landmark) {
			    print("\t".$landmark->description() . PHP_EOL); // 8) Check landmark
			}

		} else {

			print("Labels:\n");
			foreach($result->labels() as $label) {
				$des = $label->description();
				$ing = substr($des, -3);
				if($ing == "ing") {
					print("\tVerb: ".$des."\n"); // 9) check verb
				} else {
					print("\tNoun: ".$des."\n"); // 10) check non-verb/noun
				}
		  }

		}

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
