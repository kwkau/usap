<?php
require_once "/path/to/lib/Twig/Autoloader.php";

Twig_Autoloader::register();

/*
 * php template files are present
 * */
$loader = new Twig_Loader_Filesystem("/path/to/templates");
$twig = new Twig_Environment($loader,array('cache'=>'/path/to/compilation_cache'));

echo $twig->render('index.html',array("name"=>'kwaku appiah-kubby osei'));
/*
 * template via code
 */
$loader = new Twig_Loader_Array(array('index'=> "hello{{name}}"));
$twig = new Twig_Environment($loader);

echo $twig->render('index',array('name'=>"kwaku appiah-kubby osei"));
