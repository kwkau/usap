<?php

// Replace the custom autoloader with Composer's autoloader
require 'vendor/autoload.php';

// If you need to maintain backward compatibility, you can keep the existing loader
// but wrap it in a function that only runs if Composer couldn't find the class
spl_autoload_register(function($class) {
    $pathControllers = "controller/{$class}.php";
    $pathLibs = "libs/{$class}.php";
    $pathModels = "model/{$class}.php";
    $data_defs = "model/data_object_definitions/{$class}.php";
    $pathInterfaces = "libs/Interfaces/{$class}.php";
    $pathExceptions = "libs/Exceptions/{$class}.php";
    $pathConfig = "config/{$class}.php";
    $websockets = "websockets/{$class}.php";

    // Use a cleaner approach with an array of paths to check
    $paths = [
        $websockets, $pathControllers, $pathModels, $pathLibs,
        $pathConfig, $pathInterfaces, $pathExceptions, $data_defs
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require $path;
            return true;
        }
    }
    
    return false;
});

//initialize the framework by setting environment variables
new initialize();

//let us start the engine
new bootstrap();


