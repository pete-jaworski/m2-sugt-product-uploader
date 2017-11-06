<?php
require_once 'vendor/autoload.php';
 
    

$logger = new \Appe\Logger();
$dotnev = new \Dotenv\Dotenv(__DIR__);

$controller = new \Appe\Controller(
        new \Appe\Magento(new \Curl\Curl(), $dotnev, $logger),
        new \Appe\SubiektGT(new \COM("InsERT.gt"), $logger, $dotnev),
        $logger
        );
 

$controller->uploadProducts();
 
 