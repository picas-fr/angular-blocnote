<?php

// config & init
require_once 'src/bootstrap.php';

// response
try {
    $response = RESTapi::getInstance()
        ->dispatch()
        ->getResponse()
        ;
} catch (Exception $e) {
    try {
        $response = RESTapi::getInstance()
            ->error($e)
            ->getResponse()
            ;
    } catch (Exception $f) {
        die($f->getMessage());
    }
}
header('Content-Type: application/json; charset=utf8');
echo json_encode($response);
?>
