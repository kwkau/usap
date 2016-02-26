<?php

function loadSSWAP($class){
    $pathControllers = "controller/{$class}.php";
    $pathLibs = "libs/{$class}.php";
    $pathModels = "model/{$class}.php";
    $data_defs = "model/data_object_definitions/{$class}.php";
    $pathInterfaces = "libs/Interfaces/{$class}.php";
    $pathExceptions = "libs/Exceptions/{$class}.php";
    $pathConfig = "config/{$class}.php";
    $websockets = "websockets/{$class}.php";

    if (file_exists($websockets)) {
        require $websockets;
    }elseif (file_exists($pathControllers)) {
        require $pathControllers;
    } elseif (file_exists($pathModels)) {
        require $pathModels;
    } elseif (file_exists($pathLibs)) {
        require $pathLibs;
    } elseif (file_exists($pathConfig)) {
        require $pathConfig;
    }elseif (file_exists($pathInterfaces)) {
        require $pathInterfaces;
    }elseif (file_exists($pathExceptions)) {
        require $pathExceptions;
    }elseif(file_exists($data_defs)){
        require $data_defs;
    }
}

spl_autoload_extensions('.php,.phar');
spl_autoload_register('loadSSWAP');

//initialize the framework by setting environment variables
new initialize();

//let us start the engine
new bootstrap();


