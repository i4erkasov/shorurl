<?php
class Sender
{
    private $headers = [];
    private $url = null;
    private $type = 'GET';
    private $data = null;
    private $gzip = false;

    public function setHeaders($headers){
        $this->headers = $headers;
    }

    public function setData($data){
        $this->data = $data;
    }

    public function setGzip($data){
        $this->gzip = $data;
    }

    public function createUrl ($arParams = []){
        if(!empty($arParams)){
            $this->url = implode('/', $arParams);
            return true;
        }
        return false;
    }
    public function setMethod($method = ''){
        if(!empty($method)){
            $this->type = $method;
        }
    }

    public function send(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        if($this->type == 'POST')
            curl_setopt($ch, CURLOPT_POST, TRUE);
        if(!is_null($this->data))
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);
        if(!empty($this->headers))
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        if($this->gzip)
            curl_setopt($ch,CURLOPT_ENCODING , 'gzip');
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

}