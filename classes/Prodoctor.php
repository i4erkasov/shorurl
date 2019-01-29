<?php
class Prodoctor
{
    private $login = "";
    private $password = "";
    private $clinicId = 34155;
    private $clinicName = "";
    private $url = "https://api.prodoctorov.ru";
    private $data = null;


    public function __construct($data = null)
    {
        if(!is_null($data)){
            $this->setData($data);
        } else {
            throw new InvalidArgumentException('Error! Data is null');
        }
    }

    private function sendData()
    {
        if(!is_null($this->data)){
            $sender = new Sender();
            $sender->createUrl([$this->url, 'mis', 'send_cells/']);
            $sender->setHeaders([
                'content-encoding: gzip',
                'content-type: application/x-www-form-urlencoded',
            ]);
            $sender->setMethod('POST');
            $sender->setGzip(true);
            $sender->setData(gzencode($this->data, 5));
            $response = $sender->send();
            return $response;
        } else {
            throw new InvalidArgumentException('Error! Data is null');
        }
    }

    private function prepareData()
    {
        $arResult = [
            'login' => $this->login,
            'password' => $this->password,
            'cells' =>json_encode([
                intval($this->clinicId) => $this->clinicName,
                'data' => [
                    $this->clinicId => $this->data,
                ],
            ], JSON_UNESCAPED_UNICODE),
        ];
        $resultStr = '';
        foreach ($arResult as $key => $val){
            $resultStr .= $key .'='.$val;
            if($key != 'cells')
                $resultStr .='&';
        }

        $this->setData($resultStr);
    }

    private function setData($data = null){
        $this->data = $data;
    }

    public function send()
    {
        if(!is_null($this->data))
        {
            $this->prepareData();
            return $this->sendData();
        }
    }

}