<?php
require_once __DIR__ . '/Common.php';

use Aliyun\OSS\OSSClient;

// Sample of list buckets
function listBuckets(OSSClient $client) {
    $buckets = $client->listBuckets();

    foreach ($buckets as $bucket) {
        echo 'Bucket: ' . $bucket->getName() . "\n";
    }
}

// Sample of create Bucket
function createBucket(OSSClient $client, $bucket) {
    $client->createBucket(array(
        'Bucket' => $bucket,
    ));
}

// Sample of get Bucket Acl
function getBucketAcl(OSSClient $client, $bucket) {
    $acl = $client->getBucketAcl(array(
        'Bucket' => $bucket,
    ));

    $grants = $acl->getGrants();
    echo $grants[0];
}

// Sample of delete Bucket
function deleteBucket(OSSClient $client, $bucket) {
    $client->deleteBucket(array(
        'Bucket' => $bucket,
    ));
}


$client = createClient();
$bucket = 'jhla-test';

listBuckets($client);
createBucket($client, $bucket);
getBucketAcl($client, $bucket);
deleteBucket($client, $bucket);


