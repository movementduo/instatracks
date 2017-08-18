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

		return (object) [
			"type"		=> $type,
			"text"		=> $text,
			"id"		=> $image['id'],
			"url"		=> $image['cdnURL'],
			"likes"		=> $image['likes'],
			"lyrics_id"	=> '',
			"lyrics"	=> '',
			"lyrics2"	=> '',
			"width"		=> $image['width'],
			"height"	=> $image['height'],
		];
	}

	function setImages() {
	
		$mode = $this->db->executeSql("SELECT sessionMode FROM instances WHERE id = :x1 LIMIT 1",[$this->instanceID])->fetchAssoc()[0]['sessionMode'];

		$this->debug('what is the mode: ');
		$this->debug($mode);
		if($mode == 'popular') {
			$imagesQ = $this->db->executeSql("SELECT * FROM instanceSlides WHERE instanceId = :x1 ORDER BY likes DESC, RAND() LIMIT 6",array($this->instanceID));
		}elseif($mode == 'manual') {
			$imagesQ = $this->db->executeSql("SELECT * FROM instanceSlides WHERE instanceId = :x1 AND status = 'selected' ORDER BY RAND() LIMIT 6",array($this->instanceID));
		} else {
			$imagesQ = $this->db->executeSql("SELECT * FROM instanceSlides WHERE instanceId = :x1 ORDER BY RAND() LIMIT 6",array($this->instanceID));
		}
		if($imagesQ->rowCount()) {
			$this->images = $imagesQ->fetchAssoc();
		}
	}

	function execute() {
/*		if(!$this->instanceExists()) {
			$this->debug("No instance");
			$this->destroy();
		}
*/		$this->setImages();
		$this->updateState("analyzing");

		if(count($this->images) < 4) {
			$this->updateState("imagecount");
			$this->destroy();
		}


	
	foreach($this->images as $i) {
		$allLabels = array();
		$imageStream = file_get_contents($i['cdnURL']);
		$this->saveToS3($imageStream,'images',$i['id'].'.jpg');
		$image = $this->vision->image($imageStream, ['LABEL_DETECTION','TEXT_DETECTION', 'LOGO_DETECTION','FACE_DETECTION','LANDMARK_DETECTION']);
		$result = $this->vision->annotate($image);
		$this->db->executeSql("UPDATE instanceSlides SET status = 'accepted' WHERE id = :x1",[$i['id']]);
		$myPics[] = $this->analyseImage($result,$i);
	}


	// SAFE IMAGES

	$this->updateState("lyrics");

$image_count = count($myPics);

$n = array(1, 2, 3, 4, 5, 6);
shuffle($n);

if($image_count == 4 ) {
	$rG1 = array($n[0], $n[1], $n[0], $n[1]);
	$rG2 = array($n[0], $n[0], $n[1], $n[1]);
	$rG = array($rG1, $rG2);
}

if($image_count == 5 ) {
	$rG1 = [$n[0], $n[1], $n[0], $n[1], $n[1]];
	$rG2 = [$n[0], $n[1], $n[2], $n[1], $n[0]];
	$rG3 = [$n[0], $n[1], $n[0], $n[1], $n[0]];
	$rG = array($rG1, $rG2, $rG3);
}

if($image_count == 6) {
	//$rG1 = [$n[0], $n[1], $n[0], $n[1], $n[2], $n[2]];
	//$rG2 = [$n[0], $n[1], $n[2], $n[0], $n[1], $n[2]];
	//$rG3 = [$n[0], $n[1], $n[2], $n[2], $n[1], $n[0]];
	$rG4 = [$n[0], $n[0], $n[1], $n[1], $n[2], $n[2]];
	$rG = array($rG4);
}

$scheme = $rG[array_rand($rG)];

$sequenceMap = [
	"happy" => "A",
	"angry" => "B",
	"sad" => "C",
	"surprised" => "D",
	"landmark" => "E",
	"group" => "F",
	"noun" => "G",
	"verb" => "H",
	"logo" => "I",
	"fanta" => "J",
	"noEmotion" => "K",
];

$seq = [];
$l_seq = [];

$this->debug($scheme);

foreach($scheme as $key=>$s){

	$image = $myPics[$key];
	$a = ($s*3)-2;
	$b = ($s*3)-1;
	$f = ($s*3);

	if($key == count($scheme)-1){
		$image->lyrics_id = $f-1;
		if($f < 10) {
			$seq[] = $sequenceMap[$image->type].'0'.$f;
		} else{
			$seq[] = $sequenceMap[$image->type].$f;
		}
	} else if($key % 2 == 0) {
		$image->lyrics_id = $a-1;
		if($a < 10) {
			$seq[] = $sequenceMap[$image->type].'0'.$a;
		} else{
			$seq[] = $sequenceMap[$image->type].$a;
		}
	} else {
		$image->lyrics_id = $b-1;
		if($b < 10) {
			$seq[] = $sequenceMap[$image->type].'0'.$b;
		} else{
			$seq[] = $sequenceMap[$image->type].$b;
		}
	}

}

$this->debug($seq);
$this->debug($myPics);

$audio = [];
$total = count($myPics);
$c = 0;
 	
foreach($myPics as $key => $s){	
	$type = $s->type;
	$line = $s->lyrics_id;
  	$t = $this->lyrics->$type;
  	if($type == 'landmark' || $type == 'noun' || $type == 'verb' || $type == 'logo') {
    		$replaced = str_replace('%replace%', $s->text, $t[$line]);
    		$l = explode('| ', $replaced);
  	} else {
    		$l = explode('| ', $t[$line]);
  	}
  	$lyrics = implode('',$l);
  	$s->lyrics = $l[0];
  	$s->lyrics2 = $l[1];

	$this->db->executeSql("UPDATE instanceSlides SET lyrics = :x1 WHERE id = :x2",[$lyrics, $s->id]);
	
	$pollySpeech = $this->polly->synthesizeSpeech([
	  'OutputFormat' => 'mp3', // REQUIRED
	  'Text' => '<speak><prosody rate="slow">'.$lyrics.'</prosody></speak>', // REQUIRED
	  'TextType' => 'ssml',
	  'VoiceId' => 'Joanna', // REQUIRED
	]);

	$audioStream = $pollySpeech->get('AudioStream')->getContents();

	$this->saveToS3($audioStream,'audio',$s->id.'.mp3');

	if(in_array(substr($seq[$c],0,1),array("E","G","H","I"))) {
		$audio[] = S3_WEB_ROOT.'instances/'.$this->instanceID.'/audio/'.$s->id.'.mp3';
	}
	$c++;
}

$this->debug($myPics);	


	$this->db->executeSql("UPDATE instances SET sequence = :x1 WHERE id = :x2",[implode('',$seq), $this->instanceID]);
	
	

	// next step - get lyrics sorted (api call)
	$this->updateState('audio');

	$getVars = [
		'salt'		=> $this->instanceID,
		'pepper'	=> VOCODER_API_SECRET,
		'sequence'	=> implode('',$seq),
		'file'		=> $audio,
	];

	$getVar = $this->createVocoderRequest($getVars);

	$this->debug($getVar);

	$return = trim(file_get_contents(VOCODER_API_LOC.$getVar));

	if(!$return) {
		die('no url');
	}

	$audio = file_get_contents($return);
	$this->saveToS3($audio,'audio/rendered',$this->instanceID.'.wav');

	$this->updateState('video');



$all_commands = [];

foreach($myPics as $i) {
	// $this->debug($i);
	$w = $i->width;
	$h = $i->height;
	$url = $i->url;
	$id = $i->id;
	$l1 = $i->lyrics;
	$l2 = $i->lyrics2;

	if($w == $h) {
		$object = new stdClass();
		$object->background = FFMPEG_ASSETS.'video-bg_003.mp4';
		$object->textbox = FFMPEG_ASSETS.'orange_textbox.png';
		$object->image = $url;
		$object->video = new stdClass();
		$object->video->id = $id;
		$object->video->text_top_line= $l1;
		$object->video->text_bottom_line= $l2;
		$all_commands[] = square_top($object);
	}
	if($w < $h) {
		$object = new stdClass();
		$object->background = FFMPEG_ASSETS.'video-bg_005.mp4';
		$object->textbox = FFMPEG_ASSETS.'blue_textbox.png';
		$object->image = $url;
		$object->video = new stdClass();
		$object->video->id = $id;
		$object->video->text_top_line= $l1;
		$object->video->text_bottom_line= $l2;
		$all_commands[] = portrait_top($object);
	}
	if($w > $h) {
		$object = new stdClass();
		$object->background = FFMPEG_ASSETS.'video-bg_009.mp4';
		$object->textbox = FFMPEG_ASSETS.'blue_textbox.png';
		$object->image = $url;
		$object->video = new stdClass();
		$object->video->id = $id;
		$object->video->text_top_line= $l1;
		$object->video->text_bottom_line= $l2;
		$all_commands[] = landscape_center($object);
	}
}

$cmd = trim(join(' & ', $all_commands));
shell_exec($cmd);

join_videos($myPics,$this->instanceID);

do {
	usleep(500);
} while(!file_exists(TMP_DIR."addmusic-{$this->instanceID}.mp4"));

add_music(S3_WEB_ROOT.'instances/'.$this->instanceID.'/audio/rendered/'.$this->instanceID.'.wav',$this->instanceID);

do {
	usleep(500);
} while(!file_exists(TMP_DIR."finished-{$this->instanceID}.mp4"));


// move rendered video to s3

		$videoStream = file_get_contents(TMP_DIR."finished-{$this->instanceID}.mp4");
		$filename = $this->generateFilename();
		$this->saveToS3($videoStream,'complete',$filename.'.mp4');

		try {
			$this->s3->putObject([
				'Bucket' => S3_BUCKET,  
				'Key'    => 'complete/'.$filename.'.mp4', 
				'Body'   => $videoStream, // image
				'ACL'    => 'public-read',
			]);
		} catch (Aws\S3\Exception\S3Exception $e) {
			echo "There was an error uploading the file.\n";
		}
		$this->updateState('complete');
		$this->db->executeSql("UPDATE instanceSlides SET status = 'accepted' WHERE id = :x1",[$this->instanceID]);
		$this->db->executeSql("UPDATE instances SET status = 'complete', videoFile = :x1, shareUrl = :x2, instanceId = :x3 WHERE id = :x4",[$filename.'.mp4','/v/'.$filename,$filename,$this->instanceID]);
		
//		GARBAGE COLLECTION
/*	delete temporary files after successful creation */
		
		
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
	
	function generateFilename() {
		$code = substr(sha1(microtime()),0,8);
		
		$codeQ = $this->db->executeSql("SELECT * FROM instances WHERE videoFile = :x1",array($code.'.mp4'));
		if($codeQ->rowCount()) {
			return $this->generateFilename();
		}
		
		return $code;

		
	}
	
	function saveToS3($stream,$folder,$filename) {
		try {
			$this->s3->putObject([
				'Bucket' => S3_BUCKET,  // bucket to store in
				'Key'    => 'instances/'.$this->instanceID.'/'.$folder.'/'.$filename, // filename of object stored
				'Body'   => $stream, // image
				'ACL'    => 'public-read',
			]);
		} catch (Aws\S3\Exception\S3Exception $e) {
			echo "There was an error uploading the file.\n";
		}
	}
}
