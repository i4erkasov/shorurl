<?php
class Integration
{
    public static function run()
    {
        $yc = new Yclients();
        $arData = $yc->getData();
        $pr = new Prodoctor($arData);
        return $pr->send();
    }
}