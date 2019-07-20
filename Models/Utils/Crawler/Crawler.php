<?php
/**
 * Created by PhpStorm
 * Author: Lê Minh Hổ
 * Date: 4/12/2019
 * Time: 10:31 AM
 */

namespace util\crawler;

require_once "Models/Utils/Guzzle/vendor/autoload.php";
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

abstract class Crawler
{
    public static $GET = "GET";
    public static $POST = "POST";
    private $client; //Class GuzzleHttp/Client
    private $request; //Class GuzzleHttp/Psr7/Request
    private $response; //Class GuzzleHttp/Psr7/Response
    private $timeout = 60; //Default timeout value

    public function __construct()
    {
        $this->client = new Client([
            "timeout" => $this->timeout
        ]);
    }


    /**
     * @param $uri
     * @param $method
     * @param array $header
     * @param array $query
     * @return string|void
     */
    protected final function getSourceFromURL($uri, $method, $header = array(), $query = array())
    {
        try{
            //Create new request with url, method and parameter
            $this->request = $this->createRequest($uri, $method, $header, $query);
            //Send synchronous request
            $this->response = $this->sendRequest();
            //Check status code available
            if ($code = $this->response->getStatusCode() != 200) {
                $reason = $this->response->getReasonPhrase();
                throw new Exception("Static code invalid: {$code}({$reason})");
            }

            //Get content of body response
            $body = (string)$this->response->getBody();
        } catch(Exception $exp){
            echo $exp->getMessage();
            return;
        }

        return $body;
    }


    /**
     * @param $uri
     * @param $method
     * @param $params
     * @return Request
     */
    private function createRequest($uri, $method, $header = array(), $query = array())
    {
        return new Request($method, $uri, [
            "headers" => $header,
            "query" => $query
        ]);
    }


    private function sendRequest()
    {
        return $this->client->send($this->request);
    }


    private function sendAsyncRequest()
    {
        return $this->client->sendAsync($this->request);
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

}