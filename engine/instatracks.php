<?php

error_reporting(E_ALL);

class Instatracks {

	var $images = [];
	var $lyrics;
	var $db;
	var $isVerbose = false;
	var $polly;


	var $instanceID;
	
	function __construct() {
		if(in_array('-v',$_SERVER['argv'])) {
			$this->setVerbose();
		}

	}
	
	function setInstance($instanceID) {
		$this->instanceID = $instanceID;
	}
	
	function setLyrics($lyrics) {
		$this->lyrics = $lyrics;
		
		$this->type_fanta = $this->lyrics->fanta;
		$this->type_noun = $this->lyrics->noun;
		$this->type_verb = $this->lyrics->verb;
		$this->type_happy = $this->lyrics->happy;
		$this->type_angry = $this->lyrics->angry;
		$this->type_sad = $this->lyrics->sad;
		$this->type_surprised = $this->lyrics->surprised;
		$this->type_noEmotion = $this->lyrics->noEmotion;
		$this->type_group = $this->lyrics->group;
		$this->type_landmark = $this->lyrics->landmark;
	}
	
	function setVerbose() {
		$this->isVerbose = true;
	}
	
	function instanceExists() {
		if(!$this->instanceID) {
			return false;
		}
		$instanceQ = $this->db->executeSql("SELECT * FROM instances WHERE id = :x1 AND status = 'pending' LIMIT 1",[$this->instanceID]);
		if($instanceQ->rowCount()) {
			$this->db->executeSql("UPDATE instances SET status = 'active' WHERE id = :x1 LIMIT 1",[$this->instanceID]);
			return $instanceQ->fetchAssoc()[0];
		}
		return false;
	}

	function updateState($state) {
		$this->db->executeSql("UPDATE instances SET creationState = :x1 WHERE id = :x2 LIMIT 1",[$state, $this->instanceID]);
	}

	function destroy() {
		$this->db->executeSql("UPDATE instances SET status = 'rejected' WHERE id = :x1",[$this->instanceID]);
		$this->updateState("rejected");
		exit;
	}

	function getImagetype($image) {
		if($image->logos()){
			return 'logo';
		}

	}

	function setDB($db) {
		$this->db = $db;
	}

	function setPolly($polly) {
		$this->polly = $polly;
	}

	function setVision($vision) {
		$this->vision = $vision;
	}

	function setS3($s3) {
		$this->s3 = $s3;
	}

	function createImageObject($type,$image,$text) {

		$metadata = unserialize($image['metadata']);

		return (object) [
			"type"		=> $type,
			"text"		=> $text,
			"id"		=> $image['id'],
			"url"		=> $image['cdnURL'],
			"likes"		=> $metadata[0],
			"lyrics"	=> '',
			"width"		=> $metadata[1],
			"height"	=> $metadata[2],
		];
	}

	function setImages() {
		$imagesQ = $this->db->executeSql("SELECT * FROM instanceSlides WHERE instanceId = :x1 ORDER BY RAND() LIMIT 8",array($this->instanceID));
		if($imagesQ->rowCount()) {
			$this->images = $imagesQ->fetchAssoc();
		}
	}

	function execute() {
/*		if(!$this->instanceExists()) {
			$this->debug("No instance");
			$this->destroy();
		}*/
		$this->setImages();
		$this->updateState("analyzing");

		
	
	$c = count($this->images);

$howmanytocheck = 8;

if($c <= 6) {
	$this->debug("\nYou have 6 or less images to check\n");
	$numberOfImages = $c;
} else if($c > 6 && $c <= $howmanytocheck){
	$this->debug("\nYou have less than our minimum number of images to check\n");
	$numberOfImages = $c;
} else {
	$this->debug("\nYou have more than enough images to check\n");
	$numberOfImages = $howmanytocheck;
}

$myPics = [];

$image_batch = array_slice($this->images, 0, $numberOfImages);

	
			foreach($image_batch as $i) {

				$allLabels = array();
				$imageStream = file_get_contents($i['cdnURL']);
/*
				try {
					$this->s3->putObject([
						'Bucket' => 'instatracks',  // bucket to store in
						'Key'    => $this->instanceID.'/images/'.$i->id.'.jpg', // filename of object stored
						'Body'   => $imageStream, // image
						'ACL'    => 'public-read',
					]);
				} catch (Aws\S3\Exception\S3Exception $e) {
					echo "There was an error uploading the file.\n";
				}
*/

			$image = $this->vision->image($imageStream, ['LABEL_DETECTION','TEXT_DETECTION', 'LOGO_DETECTION','FACE_DETECTION','LANDMARK_DETECTION','SAFE_SEARCH_DETECTION']);
			$result = $this->vision->annotate($image);

			$safe = $result->safeSearch();

			if($safe->isAdult() || $safe->isSpoof() || $safe->isMedical() || $safe->isViolent()) {
				$this->db->executeSql("UPDATE instanceSlides SET status = 'rejected' WHERE id = :x1",[$i['id']]);
			} else {
				$this->db->executeSql("UPDATE instanceSlides SET status = 'accepted' WHERE id = :x1",[$i['id']]);
				$myPics[] = $this->analyseImage($result,$i);
			}

		}


	// SAFE IMAGES
$safe = count($myPics);

if($safe < 4) {
	$this->destroy();
}

	$this->updateState("lyrics");

if($safe == 4 ) {
	$selected = array_slice($myPics, 0, 4);
	$n = array(0, 1, 2, 3);
	shuffle($n);
	$rGroup_1 = array($n[0], $n[1], $n[0], $n[1]);
	$rGroup_2 = array($n[0], $n[0], $n[1], $n[1]);
	$rGroup_3 = array($n[0], $n[1], $n[1], $n[0]);
	$whichGroup = array($rGroup_1, $rGroup_2, $rGroup_3);
	$anotherRandom = rand(1,3);
}
if($safe == 5 ) {
	$selected = array_slice($myPics, 0, 5);
	$n = array(0, 1, 2, 3, 4);
	shuffle($n);
	$rGroup_1 = [$n[0], $n[1], $n[0], $n[1], $n[1]];
	$rGroup_2 = [$n[0], $n[1], $n[2], $n[1], $n[0]];
	$rGroup_3 = [$n[0], $n[1], $n[0], $n[1], $n[0]];
	$rGroup_4 = [$n[0], $n[1], $n[1], $n[0], $n[0]];
	$whichGroup = array($rGroup_1, $rGroup_2, $rGroup_3, $rGroup_4);
	$anotherRandom = rand(1,4);
}
if($safe >= 6) {
	$selected = array_slice($myPics, 0, 6);
	$n = array(0, 1, 2, 3, 4, 5, 6);
	shuffle($n);
	$rGroup_1 = [$n[0], $n[1], $n[0], $n[1], $n[2], $n[2]];
	$rGroup_2 = [$n[0], $n[1], $n[2], $n[0], $n[1], $n[2]];
	$rGroup_3 = [$n[0], $n[1], $n[2], $n[2], $n[1], $n[0]];
	$rGroup_4 = [$n[0], $n[1], $n[0], $n[2], $n[3], $n[2]];
	$rGroup_5 = [$n[0], $n[0], $n[1], $n[1], $n[2], $n[2]];

	$whichGroup = array($rGroup_1, $rGroup_2, $rGroup_3, $rGroup_4, $rGroup_5);
	$anotherRandom = rand(1,5);
}
//$scheme = $whichGroup[$anotherRandom];
$scheme = array(0,0,0,0,0,0);
//$this->debug($scheme);

foreach($selected as $key => $s){
	$rhyme = $scheme[$key];
	$type = $s->type;
	$t = $this->lyrics->$type;
	$r = $t[$rhyme];

	$total = count($selected);

	if($key == $total-1){
		$line = $r[2];
		if($type == 'noun' || $type == 'verb' || $type == 'landmark') {
			$lyrics = $line[0].' '.$s->text.' '.$line[1];
			$this->debug("\n lyrics: ".$lyrics."\n");
			$s->lyrics = $lyrics;
		} else {
			$lyrics = $line[0];
			$this->debug("\n lyrics: ".$lyrics."\n");
			$s->lyrics = $lyrics;
		}
	} else {
		$line = $r[0];
		if($type == 'noun' || $type == 'verb' || $type == 'landmark') {
			$lyrics = $line[0].' '.$s->text.' '.$line[1];
			$this->debug("\n lyrics: ".$lyrics."\n");
			$s->lyrics = $lyrics;
		} else {
			$lyrics = $line[0];
			$this->debug("\n lyrics: ".$lyrics."\n");
			$s->lyrics = $lyrics;
		}
	}
	
	$this->db->executeSql("UPDATE instanceSlides SET lyrics = :x1 WHERE id = :x2",[$lyrics, $s->id]);
	

}

$this->debug($selected);	

// next step - get lyrics sorted (api call)
// $this->updateState('audio');
// store audio in tmp w/ instance id

	$getVars = [
		'salt'		=>	$this->instanceID,
		'pepper'	=>	VOCODER_API_SECRET,
		'sequence'	=>	'A01B02C01D02D03E09',
		'file'		=> [
			S3_WEB_ROOT.'dynamic/speech_20170727163851959.mp3',
			S3_WEB_ROOT.'dynamic/speech_20170727163851959.mp3',
			S3_WEB_ROOT.'dynamic/speech_20170727164053615.mp3',
			S3_WEB_ROOT.'dynamic/speech_20170727164104675.mp3',
			S3_WEB_ROOT.'dynamic/speech_20170727164141219.mp3',
			S3_WEB_ROOT.'dynamic/speech_20170727164157447.mp3',
		],
	
	];


	$getVar = $this->createVocoderRequest($getVars);

die($getVar);

$curl = curl_init();
// Set some options - we are passing in a useragent too here
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => VOCODER_API_LOC.$getVar,
    CURLOPT_USERAGENT => 'Instatracks'
));

$resp = curl_exec($curl);

curl_close($curl);

die(var_export($resp,true));



// ffmpeg everything.
// $this->updateState('video');
// this->ffmpeg....

// move rendered video to s3
// generate cloudfront url

// done - update database to 'complete'.
// $this->updateState('complete');
// $this->db->executeSql("UPDATE instanceSlides SET status = 'completed' WHERE id = :x1",[$this->instanceID]);

	}






	function analyseImage($result,$image) {
		if($result->logos()){
			$des = $result->logos()[0]->description();
			$this->debug("What logo is it? ".$des."\n");
			if(trim(strtolower($des)) == 'fanta') {
				$this->debug("Fanta");
				return $this->createImageObject('fanta',$image,'fanta');
			} else {
				$this->debug("A different logo is found, so use it's label:");
				$des = $result->labels()[0]->description();
				$ing = substr($des, -3);
				if($ing == "ing") {
					$this->debug("Verb: ".$des);
					return $this->createImageObject('verb',$image,$des);
				} else {
					$this->debug("Noun: ".$des);
					return $this->createImageObject('noun',$image,$des);
				}
			}
		} else if ($result->faces()) {
			$this->debug("Faces:\n");
			$faceCount = sizeof($result->faces());
			if($faceCount > 1){
				$this->debug("\tGroup of friends!\n");
					return $this->createImageObject('group',$image,'group');
			} else {
				foreach ((array) $result->faces() as $face) {
					if($face->isAngry()){
						$this->debug("Angry");
						return $this->createImageObject('angry',$image,'angry');
					} else if($face->isJoyful()){
						$this->debug("\tI'm so happy\n");
						return $this->createImageObject('happy',$image,'happy');
					} else if($face->isSorrowful()){
						$this->debug("\tI'm so sad\n"); // 5) Check sad face
						return $this->createImageObject('sad',$image,'sad');
					}else if($face->isSurprised()){
						$this->debug("\tI'm so surprised\n"); // 6) Check surprised face
						return $this->createImageObject('surprised',$image,'surprised');
					} else{
						$this->debug("\tLooking good there\n"); // 7) Check no emotion detected
						return $this->createImageObject('noEmotion',$image,'noEmotion');
					}
				}
			}
		} else if ($result->landmarks()) {
			$des = $result->landmarks()[0]->description();
			$this->debug("\tLandmark: ".$des."\n");
			return $this->createImageObject('landmark',$image,$des);
		} else {
			$this->debug("Labels:\n");
			$des = $result->labels()[0]->description();
			$ing = substr($des, -3);
			if($ing == "ing") {
				return $this->createImageObject('verb',$image,$des);
			} else {
				return $this->createImageObject('noun',$image,$des);
			}
		}
	}
	
	function debug($var) {
		if($this->isVerbose) {
			echo var_export($var,true);
		}
	}

	function createVocoderRequest($vars) {
		$out = [];
		foreach($vars as $key => $val) {
			if(is_array($val)) {
				foreach($val as $v) {
					$out[] = $key.'[]='.$v;
				}
			} else {
				$out[] = $key.'='.$val;
			}
		
		}
		
		if(count($out)) {
			return '?'.implode('&',$out);
		}
	}
}