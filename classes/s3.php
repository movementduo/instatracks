<?php

use Aws\S3\S3Client;

// Instantiate an Amazon S3 client.
$s3 = new S3Client([
    'version' => 'latest',
    'region'  => 'eu-west-1'
]);

try {
    $s3->putObject([
        'Bucket' => 'instatracks-v3',
        'Key'    => 'instagram',
        'Body'   => fopen('/home/ubuntu/basquiat.jpg', 'r'),
        'ACL'    => 'public-read',
    ]);
} catch (Aws\S3\Exception\S3Exception $e) {
    echo "There was an error uploading the file.\n";
}