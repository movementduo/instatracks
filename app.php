<?php

//die(var_export($_SERVER,true));


require('config.php');
require('classes/database.php');
require('aws/vendor/autoload.php');
require('google/vendor/autoload.php');
require('vision/vendor/autoload.php');

require('test-data.php');
require('test-lyrics.php');

session_start();
use Google\Cloud\Vision\VisionClient;

// Instantiate an Amazon S3 client.
$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'eu-west-1'
]);

$polly = new \Aws\Polly\PollyClient([
  'version'     => 'latest',
  'region'      => 'eu-west-1',
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

//LYRICS USED FOR TEST RHYME SCHEME
$lyrics = json_decode($lyrics_copy);
$type_fanta = $lyrics->fanta;
$type_noun = $lyrics->noun;
$type_verb = $lyrics->verb;
$type_happy = $lyrics->happy;
$type_angry = $lyrics->angry;
$type_sad = $lyrics->sad;
$type_surprised = $lyrics->surprised;
$type_noEmotion = $lyrics->noEmotion;
$type_group = $lyrics->group;
$type_landmark = $lyrics->landmark;

// SHUFFLE IMAGES
$c = count($images->images);
$howmanytocheck = 8;
if($c <= 6) {
	print_r("\nYou have 6 or less images to check\n");
	$numberOfImages = $c;
} else if($c > 6 && $c <= $howmanytocheck){
	print_r("\nYou have less than our minimum number of images to check\n");
	$numberOfImages = $c;
} else {
	print_r("\nYou have more than enough images to check\n");
	$numberOfImages = $howmanytocheck;
}
shuffle($images->images);
$image_batch = array_slice($images->images, 0, $numberOfImages);

foreach($image_batch as $i) {

	$body = file_get_contents($i->url);

	// try {
	// 	$s3->putObject([
 //        		'Bucket' => 'instatracks',  // bucket to store in
 //        		'Key'    => 'test'.'/images/'.$i->id.'.jpg', // filename of object stored
 //        		'Body'   => $body, // image
	// 		'ACL'    => 'public-read',
	// 	]);
	// } catch (Aws\S3\Exception\S3Exception $e) {
	// 	echo "There was an error uploading the file.\n";
	// }

	$image = $vision->image(file_get_contents($i->url), ['LABEL_DETECTION','TEXT_DETECTION', 'LOGO_DETECTION','FACE_DETECTION','LANDMARK_DETECTION','SAFE_SEARCH_DETECTION']);

	$result = $vision->annotate($image);
	$safe = $result->safeSearch();

	if($safe->isAdult() || $safe->isSpoof() || $safe->isMedical() || $safe->isViolent()) {
		echo "This image is not safe.\n";
	} else {
		echo "This image is safe to use.\n";
		
		if($result->logos()){
			$first = array_shift($result->logos());
			$des = $first->description();
			print("What logo is it? ".$des."\n");

			if($des == 'Fanta' || $des == 'fanta'){
				
				//Test rhyme scheme - it gets really complicated...
				$rhymeA = $type_fanta[0];
				$lyric = "\t".$rhymeA[0]."\n";
				print_r($lyric);

				//Need to make this a function...
				$myPics[] = (object) ["type"=>'fanta', "text"=>'', "id"=>$i->id, "url"=>$i->url, "likes"=>$i->likes, "tagged"=>$i->taggedUsers, "lyrics"=>$lyric];

			} else {
				print("A different logo is found, so use it's label: ");

				foreach($result->labels() as $label) {
					$des = $label->description();
					$allLabels[] = $des;
				}

				$first = array_shift($result->labels());
				$des = $first->description();
				$ing = substr($des, -3);
				if($ing == "ing") {
					print("\tVerb: ".$des."\n");
					
					$rhymeA = $type_verb[0];
					$lyric = "\t".$rhymeA[0]." '".$des."' ".$rhymeA[1]."\n";
					print_r($lyric);

					$myPics[] = (object) ["type"=>'verb', "text"=>$allLabels, "id"=>$i->id, "url"=>$i->url, "likes"=>$i->likes, "tagged"=>$i->taggedUsers, "lyrics"=>$lyric];
					$allLabels = array();

				} else {
					print("\tNoun: ".$des."\n");

					$rhymeA = $type_noun[0];
					$lyric = "\t".$rhymeA[0]." '".$des."' ".$rhymeA[1]."\n";
					print_r($lyric);

					$myPics[] = (object) ["type"=>'noun', "text"=>$allLabels, "id"=>$i->id, "url"=>$i->url, "likes"=>$i->likes, "tagged"=>$i->taggedUsers, "lyrics"=>$lyric];
					$allLabels = array();
					
				}
			}

		} else if ($result->faces()) {

				print("Faces:\n");
				$faceCount = sizeof($result->faces());
				if($faceCount > 1){
					printf("\tGroup of friends!\n");

					$rhymeA = $type_group[0];
					$lyric = "\t".$rhymeA[0]."\n";
					print_r($lyric);

					$myPics[] = (object) ["type"=>'group', "text"=>'', "id"=>$i->id, "url"=>$i->url, "likes"=>$i->likes, "tagged"=>$i->taggedUsers, "lyrics"=>$lyric];

				} else {

					foreach ((array) $result->faces() as $face) {
						if($face->isAngry()){
							printf("\tI'm so angry\n");
							$rhymeA = $type_angry[0];
							$lyric = "\t".$rhymeA[0]."\n";
							print_r($lyric);

							$myPics[] = (object) ["type"=>'angry', "text"=>'', "id"=>$i->id, "url"=>$i->url, "likes"=>$i->likes, "tagged"=>$i->taggedUsers, "lyrics"=>$lyric];

						}
						else if($face->isJoyful()){
							printf("\tI'm so happy\n");

							$rhymeA = $type_happy[0];
							$lyric = "\t".$rhymeA[0]."\n";
							print_r($lyric);

							$myPics[] = (object) ["type"=>'happy', "text"=>'', "id"=>$i->id, "url"=>$i->url, "likes"=>$i->likes, "tagged"=>$i->taggedUsers, "lyrics"=>$lyric];

						}
						else if($face->isSorrowful()){
							printf("\tI'm so sad\n"); // 5) Check sad face
							
							$rhymeA = $type_sad[0];
							$lyric = "\t".$rhymeA[0]."\n";
							print_r($lyric);

							$myPics[] = (object) ["type"=>'happy', "text"=>'', "id"=>$i->id, "url"=>$i->url, "likes"=>$i->likes, "tagged"=>$i->taggedUsers, "lyrics"=>$lyric];

						}
						else if($face->isSurprised()){
							printf("\tI'm so surprised\n"); // 6) Check surprised face
							
							$rhymeA = $type_surprised[0];
							$lyric = "\t".$rhymeA[0]."\n";
							print_r($lyric);

							$myPics[] = (object) ["type"=>'surprised', "text"=>'', "id"=>$i->id, "url"=>$i->url, "likes"=>$i->likes, "tagged"=>$i->taggedUsers, "lyrics"=>$lyric];

						}
						else{
							printf("\tLooking good there\n"); // 7) Check no emotion detected

							$rhymeA = $type_noEmotion[0];
							$lyric = "\t".$rhymeA[0]."\n";
							print_r($lyric);

							$myPics[] = (object) ["type"=>'noEmotion', "text"=>'', "id"=>$i->id, "url"=>$i->url, "likes"=>$i->likes, "tagged"=>$i->taggedUsers, "lyrics"=>$lyric];

						}
					}

				}

		} else if ($result->landmarks()) {

			$first = array_shift($result->landmarks());
			$des = $first->description();
			print("\tLandmark: ".$des."\n");

			$rhymeA = $type_landmark[0];
			$lyric = "\t".$rhymeA[0]." '".$des."' ".$rhymeA[1]."\n";
			print_r($lyric);

			$myPics[] = (object) ["type"=>'landmark', "text"=>$des, "id"=>$i->id, "url"=>$i->url, "likes"=>$i->likes, "tagged"=>$i->taggedUsers, "lyrics"=>$lyric];

		} else {

			foreach($result->labels() as $label) {
				$des = $label->description();
				$allLabels[] = $des;
			}

			print("Labels:\n");
			$labels = $result->labels();
			$first = array_shift($labels);
			$des = $first->description();
			$ing = substr($des, -3);
			if($ing == "ing") {
				print("\tVerb: ".$des."\n");

				$rhymeA = $type_verb[0];
				$lyric = "\t".$rhymeA[0]." '".$des."' ".$rhymeA[1]."\n";
				print_r($lyric);

				$myPics[] = (object) ["type"=>'verb', "text"=>$allLabels, "id"=>$i->id, "url"=>$i->url, "likes"=>$i->likes, "tagged"=>$i->taggedUsers, "lyrics"=>$lyric];
				$allLabels = array();


			} else {
				print("\tNoun: ".$des."\n"); // 10) Check noun

				$rhymeA = $type_noun[0];
				$lyric = "\t".$rhymeA[0]." '".$des."' ".$rhymeA[1]."\n";
				print_r($lyric);

				$myPics[] = (object) ["type"=>'noun', "text"=>$allLabels, "id"=>$i->id, "url"=>$i->url, "likes"=>$i->likes, "tagged"=>$i->taggedUsers, "lyrics"=>$lyric];
				$allLabels = array();

			}

		}

	}

	print "\n";
	print "------------\n";
	print "\n";

}

// SAFE IMAGES
$safe = count($myPics);

if($safe >= 6) {
	$six = array_slice($myPics, 0, 6);
	print_r($six);

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
