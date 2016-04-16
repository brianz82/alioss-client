<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Aliyun\OSS\OSSClient;

// Sample of create client
function createClient($accessKeyId = 'your-access-key-id', 
					  $accessKeySecret = 'your-access-key-secret',
					  $endpoint = 'http://your-end-point') {
	return OSSClient::factory(array(
			'Endpoint' => $endpoint,
			'AccessKeyId' => $accessKeyId,
			'AccessKeySecret' => $accessKeySecret,
	));
}
