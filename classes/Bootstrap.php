<?php
class Bootstrap
{
    public static function init()
    {
        $arFile = [
            'Sender',
            'Yclients',
            'Prodoctor',
            'Integration',
        ];

        foreach ($arFile as $file){
            $path = __DIR__ . DIRECTORY_SEPARATOR . $file . '.php';
            if(file_exists($path)){
                require_once $path;
            }
        }
    }
}