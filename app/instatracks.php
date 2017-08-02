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
			"lyrics2"	=> 'dynamic',
			"width"		=> $metadata[1],
			"height"	=> $metadata[2],
		];
	}

	function setImages() {
		$imagesQ = $this->db->executeSql("SELECT * FROM instanceSlides WHERE instanceId = :x1 ORDER BY RAND() LIMIT 6",array($this->instanceID));
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

$safe = count($myPics);

if($safe == 4 ) {


	$n = array(0, 1, 2, 3);
	shuffle($n);
	$rGroup_1 = array($n[0], $n[1], $n[0], $n[1]);
	$rGroup_2 = array($n[0], $n[0], $n[1], $n[1]);
	$rGroup_3 = array($n[0], $n[1], $n[1], $n[0]);
	$whichGroup = array($rGroup_1, $rGroup_2, $rGroup_3);
	$anotherRandom = rand(1,3);
}
if($safe == 5 ) {

	$n = array(0, 1, 2, 3, 4);
	shuffle($n);
	$rGroup_1 = [$n[0], $n[1], $n[0], $n[1], $n[1]];
	$rGroup_2 = [$n[0], $n[1], $n[2], $n[1], $n[0]];
	$rGroup_3 = [$n[0], $n[1], $n[0], $n[1], $n[0]];
	$rGroup_4 = [$n[0], $n[1], $n[1], $n[0], $n[0]];
	$whichGroup = array($rGroup_1, $rGroup_2, $rGroup_3, $rGroup_4);
	$anotherRandom = rand(1,4);
}
if($safe == 6) {

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

$audio = [];
$total = count($myPics);

foreach($myPics as $key => $s){
	$rhyme = $scheme[$key];
	$type = $s->type;
	$t = $this->lyrics->$type;
	$r = $t[$rhyme];


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
	
	
	$pollySpeech = $this->polly->synthesizeSpeech([
	  'OutputFormat' => 'mp3', // REQUIRED
	  'Text' => '<speak><prosody rate="slow">'.$lyrics.'</prosody></speak>', // REQUIRED
	  'TextType' => 'ssml',
	  'VoiceId' => 'Joanna', // REQUIRED
	]);

	$audioStream = $pollySpeech->get('AudioStream')->getContents();

	$this->saveToS3($audioStream,'audio',$s->id.'.mp3');
	$audio[] = S3_WEB_ROOT.$this->instanceID.'/audio/'.$s->id.'.mp3';


}

$this->debug($myPics);	




	// next step - get lyrics sorted (api call)
	$this->updateState('audio');
	// store audio in tmp w/ instance id

	$getVars = [
		'salt'		=> $this->instanceID,
		'pepper'	=> VOCODER_API_SECRET,
		'sequence'	=> 'A01B02C01D02D03E09',
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
	// print_r($i);
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

exec(join(' & ', $all_commands));

join_videos();
add_music();





// move rendered video to s3
// generate cloudfront url

// done - update database to 'complete'.
		$this->updateState('complete');
		$this->db->executeSql("UPDATE instanceSlides SET status = 'completed' WHERE id = :x1",[$this->instanceID]);
		$this->db->executeSql("UPDATE instances SET status = 'complete' WHERE id = :x1",[$this->instanceID]);
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
	
	function saveToS3($stream,$folder,$filename) {
		try {
			$this->s3->putObject([
				'Bucket' => S3_BUCKET,  // bucket to store in
				'Key'    => $this->instanceID.'/'.$folder.'/'.$filename, // filename of object stored
				'Body'   => $stream, // image
				'ACL'    => 'public-read',
			]);
		} catch (Aws\S3\Exception\S3Exception $e) {
			echo "There was an error uploading the file.\n";
		}
	}
}