<?php
$arParamsPath = [
    __DIR__,
    'classes',
    'Bootstrap'
];
require_once implode(DIRECTORY_SEPARATOR, $arParamsPath) . '.php';
set_time_limit(0);
Bootstrap::init();
Integration::run();