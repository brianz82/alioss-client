<?php
require_once __DIR__ . '/Common.php';

use Aliyun\OSS\Exceptions\OSSException;
use Aliyun\Common\Exceptions\ClientException;

// Sample of handle exception
function handleExceptionSample() {
    try {
        $client = createClient('wrong-key-id');
        $client->listBuckets();
    } catch (OSSException $ex) {
        echo "OSSException: " . $ex->getErrorCode() . " Message: " . $ex->getMessage();
    } catch (ClientException $ex) {
        echo "ClientExcetpion, Message: " . $ex->getMessage();
    }
}

handleExceptionSample();
