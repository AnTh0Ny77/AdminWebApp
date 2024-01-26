<?php
require "vendor/autoload.php";
use Src\Controllers\HomeController as home;
session_start();

$request = $_SERVER['REQUEST_URI'];
$getRequest = explode('?' ,$request, 2);
$getData = null;

if (isset($get_request[1])) 
	$getData = '?' . $getRequest[1];

if (!isset($getRequest[1])) 
	$getData = '';

$globalRequest = $getRequest[0] . $getData; 

switch ($globalRequest){

    case '/AdminWebApp/':
        echo home::index();
        break;

    case '/AdminWebApp/game'.$getData:
        echo home::index();
        break;

    case '/AdminWebApp/quetes'.$getData:
        echo home::quest();
        break;

    case '/AdminWebApp/poi'.$getData:
        echo home::poi();
        break;

    case '/AdminWebApp/slide'.$getData:
        echo home::slides();
        break;

    case '/AdminWebApp/clientgame'.$getData:
        echo home::clientgames();
        break;

    case '/AdminWebApp/typepoi'.$getData:
        echo home::typepoi();
        break;
    
    default:
        header('HTTP/1.0 404 not found');
        echo  home::error404();
        break;
}

