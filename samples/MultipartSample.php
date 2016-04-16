<?php
require_once __DIR__ . '/Common.php';

use Aliyun\OSS\OSSClient;

// Sample of multipart upload
function multipartUploadSample(OSSClient $client, $bucket, $key = 'your-object-key') {
    $fileName = __FILE__;

    $partSize = 5 * 1024 * 1204; // 5M for each part

    // Init multipart upload
    $uploadId = $client->initiateMultipartUpload(array(
        'Bucket' => $bucket,
        'Key' => $key,
    ))->getUploadId();

    // upload parts
    $fileSize = filesize($fileName);
    $partCount = (int) ($fileSize / $partSize);
    if ($fileSize % $partSize > 0) {
        $partCount += 1;
    }

    $partETags = array();
    for ($i = 0; $i < $partCount ; $i++) {
        $uploadPartSize = ($i + 1) * $partSize > $fileSize ? $fileSize - $i * $partSize : $partSize;
        $file = fopen($fileName, 'r');
        fseek($file, $i * $partSize);
        $partETag = $client->uploadPart(array(
            'Bucket' => $bucket,
            'Key' => $key,
            'UploadId' => $uploadId,
            'PartNumber' => $i + 1,
            'PartSize' => $uploadPartSize,
            'Content' => $file,
        ))->getPartETag();
        $partETags[] = $partETag;
    }

    $result =  $client->completeMultipartUpload(array(
        'Bucket' => $bucket,
        'Key' => $key,
        'UploadId' => $uploadId,
        'PartETags' => $partETags,
    ));

    echo "Completed: " . $result->getETag();
}

$client = createClient();
$bucket = 'jhla-test';
multipartUploadSample($client, $bucket);