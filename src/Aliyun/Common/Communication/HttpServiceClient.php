<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved.
 *
 * 版权所有 （C）阿里云计算有限公司
 */
namespace Aliyun\Common\Communication;

use Aliyun\Common\Exceptions\ClientException;

use Aliyun\Common\Utilities\AssertUtils;
use Aliyun\Common\Utilities\HttpHeaders;
use Aliyun\Common\Models\ServiceOptions;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/**
 * Updated the original code by using GuzzleHttp
 */
class HttpServiceClient implements ServiceClientInterface {
	/**
	 * @var \GuzzleHttp\Client
	 */
	protected $client;

	public function __construct($config = array()) {
        // Create internal client.
		$this->client = new Client(array(
		    'curl' => $config[ServiceOptions::CURL_OPTIONS],
			'allow_redirects' => array(
			    'strict' => true    // Strict redirect
			)
        ));
	}
	
	public function sendRequest(HttpRequest $request, ExecutionContext $context) {
        $response = new HttpResponse($request);
        try {

            $coreRequest = $this->buildCoreRequest($request);
//             $coreResponse = $coreRequest->send();
			/**
			 * @var $coreResponse Psr\Http\Message\ResponseInterface
			 */
            $options = [
            	'http_errors' => false,	
            ];
            if ($request->getResponseBody()) {
            	$options['sink'] = $request->getResponseBody();
            }
			$coreResponse = $this->client->send($coreRequest, $options);
			
            $coreResponse->getBody()->rewind();
            $response->setStatusCode($coreResponse->getStatusCode());
            $response->setUri($coreRequest->getUri());
            $response->setContent($coreResponse->getBody()->detach());

            // Replace resource of Guzzle Stream to forbidden resource close when Stream is released.
//             $fakedResource = fopen('php://memory', 'r+');
//             if ($coreResponse->getBody() !== null) {
//                 $coreResponse
//                     ->getBody()
//                     ->setStream($fakedResource);
//             }

//             // If request has entity, replace resource of Guzzle Stream to forbidden resource close when Stream is released.
//             if ($coreRequest instanceof EntityEnclosingRequest && $coreRequest->getBody() !== null) {
//                 $coreRequest
//                     ->getBody()
//                     ->setStream($fakedResource);
//             }

//             fclose($fakedResource);
            foreach ($coreResponse->getHeaders() as $name => $values) {
            	foreach ($values as $value) {
            		$response->addHeader($name, $value);
            	}
            }
            
            $request->setResponse($response);
            return $response;
        } catch (\Exception $e) {
            $response->close();
            throw new ClientException($e->getMessage(), $e);
        }
	}
	
	protected function buildCoreRequest(HttpRequest $request) {

        $headers = $request->getHeaders();
        $contentLength = 0;
        if (!$request->isParameterInUrl()) {
            $body = $request->getParameterString();
            $contentLength = strlen($body);
        } else {
            $body = $request->getContent();
            if ($body !== null) {
                AssertUtils::assertSet(HttpHeaders::CONTENT_LENGTH, $headers);
                $contentLength = (int) $headers[HttpHeaders::CONTENT_LENGTH];
            }
        }

        $entity = null;
        $headers[HttpHeaders::CONTENT_LENGTH] = (string) $contentLength;
        if ($body !== null) {
        	$body = \GuzzleHttp\Psr7\stream_for($body, [
        		'size' => $contentLength
        	]);
        	if ($request->getOffset() !== false) {
        		$body->seek($request->getOffset());
        	}
        }
        
        $coreRequest = new Request($request->getMethod(), $request->getFullUrl(), $headers, $body);
//         $coreRequest = $this->client->createRequest($request->getMethod(), $request->getFullUrl(), $headers, $entity);

//         if ($request->getResponseBody() != null) {
//             $coreRequest->setResponseBody($request->getResponseBody());
//         }

        return $coreRequest;
	}
}
