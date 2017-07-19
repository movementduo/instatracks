<?php

class Instatracks {

	var $images;
	var $lyrics;
	var $db;
	
	var $polly;
	
	function __construct() {
	}
	
	
	function setImages($images) {
		$this->images = $images;
	}
	function setLyrics($lyrics) {
		$this->lyrics = $lyrics;
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

	function _createDatabase() {
		$this->db = new Database($this->cfg);
	}

	function _createImageObject($type,$i,$text) {
	
		if(is_array($text)) {
			$text = $text[0];
		}

		return (object) ["type"=>$type, "text"=>[$text], "id"=>$i->id, "url"=>$i->url, "likes"=>$i->likes, "tagged"=>$i->taggedUsers, "lyrics"=>$lyric];
	}


	function execute() {


$type_fanta = $this->lyrics->fanta;
$type_noun = $this->lyrics->noun;
$type_verb = $this->lyrics->verb;
$type_happy = $this->lyrics->happy;
$type_angry = $this->lyrics->angry;
$type_sad = $this->lyrics->sad;
$type_surprised = $this->lyrics->surprised;
$type_noEmotion = $this->lyrics->noEmotion;
$type_group = $this->lyrics->group;
$type_landmark = $this->lyrics->landmark;

	
	$c = count($this->images->images);
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
shuffle($this->images->images);
$image_batch = array_slice($this->images->images, 0, $numberOfImages);

	
			foreach($image_batch as $i) {

				$allLabels = array();
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

			$image = $this->vision->image(file_get_contents($i->url), ['LABEL_DETECTION','TEXT_DETECTION', 'LOGO_DETECTION','FACE_DETECTION','LANDMARK_DETECTION','SAFE_SEARCH_DETECTION']);
			$result = $this->vision->annotate($image);

			$safe = $result->safeSearch();

			if($safe->isAdult() || $safe->isSpoof() || $safe->isMedical() || $safe->isViolent()) {
				echo "This image is not safe.\n";
			} else {
				echo "This image is safe to use.\n";
		
//				$imgType = $it->getImagetype($result);
		
		
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
		//				$myPics[] = (object) ["type"=>'fanta', "text"=>'', "id"=>$i->id, "url"=>$i->url, "likes"=>$i->likes, "tagged"=>$i->taggedUsers, "lyrics"=>$lyric];
						$myPics[] = $this->_createImageObject('fanta',$i,$allLabels);

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

							$myPics[] = $this->_createImageObject('verb',$i,$allLabels);

						} else {
							print("\tNoun: ".$des."\n");

							$rhymeA = $type_noun[0];
							$lyric = "\t".$rhymeA[0]." '".$des."' ".$rhymeA[1]."\n";
							print_r($lyric);

							$myPics[] = $this->_createImageObject('noun',$i,$allLabels);
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

		//					$myPics[] = (object) ["type"=>'group', "text"=>'', "id"=>$i->id, "url"=>$i->url, "likes"=>$i->likes, "tagged"=>$i->taggedUsers, "lyrics"=>$lyric];
							$myPics[] = $this->_createImageObject('group',$i,'');

						} else {

							foreach ((array) $result->faces() as $face) {
								if($face->isAngry()){
									printf("\tI'm so angry\n");
									$rhymeA = $type_angry[0];
									$lyric = "\t".$rhymeA[0]."\n";
									print_r($lyric);

		//							$myPics[] = (object) ["type"=>'angry', "text"=>'', "id"=>$i->id, "url"=>$i->url, "likes"=>$i->likes, "tagged"=>$i->taggedUsers, "lyrics"=>$lyric];
									$myPics[] = $this->_createImageObject('angry',$i,'');

								}
								else if($face->isJoyful()){
									printf("\tI'm so happy\n");

									$rhymeA = $type_happy[0];
									$lyric = "\t".$rhymeA[0]."\n";
									print_r($lyric);

		//							$myPics[] = (object) ["type"=>'happy', "text"=>'', "id"=>$i->id, "url"=>$i->url, "likes"=>$i->likes, "tagged"=>$i->taggedUsers, "lyrics"=>$lyric];
									$myPics[] = $this->_createImageObject('happy',$i,'');

								}
								else if($face->isSorrowful()){
									printf("\tI'm so sad\n"); // 5) Check sad face
							
									$rhymeA = $type_sad[0];
									$lyric = "\t".$rhymeA[0]."\n";
									print_r($lyric);

		//							$myPics[] = (object) ["type"=>'happy', "text"=>'', "id"=>$i->id, "url"=>$i->url, "likes"=>$i->likes, "tagged"=>$i->taggedUsers, "lyrics"=>$lyric];
									$myPics[] = $this->_createImageObject('sad',$i,'');

								}
								else if($face->isSurprised()){
									printf("\tI'm so surprised\n"); // 6) Check surprised face
							
									$rhymeA = $type_surprised[0];
									$lyric = "\t".$rhymeA[0]."\n";
									print_r($lyric);

		//							$myPics[] = (object) ["type"=>'', "text"=>'', "id"=>$i->id, "url"=>$i->url, "likes"=>$i->likes, "tagged"=>$i->taggedUsers, "lyrics"=>$lyric];
									$myPics[] = $this->_createImageObject('surprised',$i,'');

								}
								else{
									printf("\tLooking good there\n"); // 7) Check no emotion detected

									$rhymeA = $type_noEmotion[0];
									$lyric = "\t".$rhymeA[0]."\n";
									print_r($lyric);

		//							$myPics[] = (object) ["type"=>'noEmotion', "text"=>'', "id"=>$i->id, "url"=>$i->url, "likes"=>$i->likes, "tagged"=>$i->taggedUsers, "lyrics"=>$lyric];
									$myPics[] = $this->_createImageObject('noEmotion',$i,'');

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

					$myPics[] = $this->_createImageObject('landmark',$i,$des);

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

		//				$myPics[] = (object) ["type"=>'verb', "text"=>$allLabels, "id"=>$i->id, "url"=>$i->url, "likes"=>$i->likes, "tagged"=>$i->taggedUsers, "lyrics"=>$lyric];
						$myPics[] = $this->_createImageObject('verb',$i,$allLabels);
 

					} else {
						print("\tNoun: ".$des."\n"); // 10) Check noun

						$rhymeA = $type_noun[0];
						$lyric = "\t".$rhymeA[0]." '".$des."' ".$rhymeA[1]."\n";
						print_r($lyric);

						$myPics[] = $this->_createImageObject('noun',$i,$allLabels);

					}

				}

			}

			print "\n";
			print "------------\n";
			print "\n";

		}

	// SAFE IMAGES
$safe = count($myPics);

if($safe == 4 ) {
	$selected = array_slice($myPics, 0, 4);
	$n = array(0, 1, 2, 3);
	shuffle($n);
	$rGroup_1 = array($n[0], $n[1], $n[0], $n[1]);
	$rGroup_2 = array($n[0], $n[0], $n[1], $n[1]);
	$rGroup_3 = array($n[0], $n[1], $n[1], $n[0]);
	$whichGroup = array($rGroup_1, $rGroup_2, $rGroup_3);
	$anotherRandom = rand(1,3);
	print_r("\n lol what rhyme group then:\n");
	print_r($whichGroup[$anotherRandom]);
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
	print_r("\n lol what rhyme group then:\n");
	print_r($whichGroup[$anotherRandom]);
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
	print_r($rGroup_1);
	print_r($rGroup_2);
	print_r($rGroup_3);
	print_r($rGroup_4);
	print_r($rGroup_5);
	print_r($rGroup_6);
	$whichGroup = array($rGroup_1, $rGroup_2, $rGroup_3, $rGroup_4, $rGroup_5);
	print_r($whichGroup."\n");
	$anotherRandom = rand(1,5);
	print_r("\n lol what rhyme group then:\n");
	print_r($whichGroup[$anotherRandom]);
}
// $scheme = $whichGroup[$anotherRandom];
$scheme = array(0,0,0,0,0,0);
print_r($scheme);
$l = $this->lyrics;

foreach($selected as $key => $s){
	$rhyme = $scheme[$key];
	$type = $s->type;
	$t = $l->$type;
	$r = $t[$rhyme];

	if($key == $total-1){
		$line = $r[2];
		if($type == 'noun' || $type == 'verb' || $type == 'landmark') {
			$lyrics = $line[0].' '.$s->text[0].' '.$line[1];
			print_r("\n lyrics: ".$lyrics."\n");
			$s->lyrics = $lyrics;
		} else {
			$lyrics = $line[0];
			print_r("\n lyrics: ".$lyrics."\n");
			$s->lyrics = $lyrics;
		}
	} else {
		$line = $r[0];
		if($type == 'noun' || $type == 'verb' || $type == 'landmark') {
			$lyrics = $line[0].' '.$s->text[0].' '.$line[1];
			print_r("\n lyrics: ".$lyrics."\n");
			$s->lyrics = $lyrics;
		} else {
			$lyrics = $line[0];
			print_r("\n lyrics: ".$lyrics."\n");
			$s->lyrics = $lyrics;
		}
	}

}

print_r($selected);	

	}

}