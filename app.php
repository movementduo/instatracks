<?php
require('config.php');
require('classes/database.php');
require('aws/vendor/autoload.php');
require('vision/vendor/autoload.php');

# 1 - get the token name
# 2 - create a database session
# 3 - create s3 project folder inside bucket
# 3 - get images from db {

// SELECT s.* FROM instanceSlides s JOIN instances I ON s.instanceID = i.id ORDER BY RAND()

	# 4 - get images from instagram and store in s3
	# 5 - send images to cloud vision OR aws rekognition - store output in db
	# 6 - generate lyrics from tags via polly, store in s3


# }
# 7 - time lyrics

# 8 - merge audio in ffmpeg,
# 9 - merge images to audio track
# 10 - store video in s3
# 11 - update db + kill this session