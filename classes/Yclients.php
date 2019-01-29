<?php
class Yclients
{
    private $url = "https://api.yclients.com/api/v1";
    private  $login = "";
    private  $password = "";
    private  $partnerToken = '';
    private $companyId =  151592;
    private static $userToken = null;

    private function auth()
    {
        $sender = new Sender();
        $sender->createUrl([$this->url, 'auth']);
        $sender->setHeaders([
            'Content-Type: application/json',
            'Authorization: Bearer '. $this->partnerToken,
            ]);
        $sender->setData(json_encode(
            [
                'login' => $this->login,
                'password' => $this->password,
            ]
        ));
        $sender->setMethod('POST');
        $response = $sender->send();
        $res = json_decode($response, true);
        if(empty($res['errors'])){
            self::$userToken = $res['user_token'];
            return true;
        }
        return false;
    }

    private function getDataByParams($params = [])
    {
        if(!empty($params)){
            if(is_null(self::$userToken))
                $this->auth();

            $sender = new Sender();
            $sender->createUrl($params);
            $sender->setHeaders([
                'Content-Type: application/json',
                'Authorization: Bearer '. $this->partnerToken . ', '. self::$userToken,
            ]);
            $sender->setMethod('GET');
            $response = $sender->send();
            return json_decode($response, true);
        }
    }

    private function getStaff()
    {
        $arResult = $this->getDataByParams([$this->url, 'staff', $this->companyId]);
        $arRes = [];
        foreach ($arResult as $items){
            if($items['hidden'] === 0){
                $arRes[intval($items['id'])] = [
                    'efio' => $items['name'],
                    'espec' => $items['specialization'],
                ];
            }
        }
        return $arRes;
    }

    private function getScheduleById($staffTd)
    {
        $dateTime = new DateTime();
        $dateStart = $dateTime->format('Y-m-d');
        $dateTime->add(new DateInterval('P3D'));
        $dateEnd = $dateTime->format('Y-m-d');
        $arSchedule = $this->getDataByParams([$this->url, 'schedule', $this->companyId, $staffTd, $dateStart, $dateEnd]);
        $arResult = [];
        foreach ($arSchedule as $key => $items) {
            if($items['is_working']){
                $arSeances = $this->getDataByParams([$this->url, 'timetable', 'seances', $this->companyId, $staffTd, $items['date']]);
                $i = 0; $j = 2;
                while ($j <= count($arSeances)+1){
                    $time = strtotime($arSeances[$i]['time']);
                    $endTime = date("H:i", strtotime('+30 minutes', $time));
                    $arResult[] = [
                        'dt' => $items['date'],
                        'time_start' => $arSeances[$i]['time'],
                        'time_end' => (empty($arSeances[$j]['time'])) ? $endTime : $arSeances[$j]['time'],
                        'free' => ($arSeances[$i]['is_free']) ? true : false,
                    ];
                    $i = $j; $j = $j +2;
                }
            }
        }
        return $arResult;
    }

    public function getData()
    {
        $arStaff = $this->getStaff();
        $arResult = [];
        if(!empty($arStaff)){
            $arResult = $arStaff;
            foreach ($arStaff as $key => $item) {
                $arSchedule = $this->getScheduleById($key);
                if(!empty($arSchedule)){
                    $arResult[$key]['cells'] = $this->getScheduleById($key);
                } else {
                    unset($arResult[$key]);
                }
            }
        }
        return $arResult;
    }

}