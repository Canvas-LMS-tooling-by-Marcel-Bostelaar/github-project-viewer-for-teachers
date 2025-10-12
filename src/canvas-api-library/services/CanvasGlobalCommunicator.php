<?php

namespace CanvasApiLibrary\Services;

class CanvasGlobalCommunicator extends BaseCanvasCommunicator{
    public function get(string $route){
        return self::curlGet($this->baseURL . $route, $this->apiKey);
    }

    public function put(string $route, $data){
        return self::curlPut($this->baseURL . $route, $this->apiKey, $data);
    }
}