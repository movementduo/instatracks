<?php
require('config.php');
require('classes/database.php');
require('aws/vendor/autoload.php');
require('vision/vendor/autoload.php');
require('ffmpeg/vendor/autoload.php');

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
# 8 - time lyrics 

# 9 - merge audio in ffmpeg,
# 10 - merge images to audio track
# 11 - store video in s3
# 12 - update db + kill this session