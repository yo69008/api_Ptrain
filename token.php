<?php

$dbh = require 'include.php';

$token = base64_encode(uniqId());

$obj = new stdClass();
$obj->token =$token;
$sql ='INSERT INTO `users`(`token`, `trajet`, `retard`, `train`, `destination`, `horaires`) VALUES (:token, :trajet, :retard,  :train, :destination, :horaires)';
try{
    $sth = $dbh->prepare($sql);
    $sth->bindValue(":token", $token);
    $sth->bindValue(":trajet", "");
    $sth->bindValue(":retard", 0);
    $sth->bindValue(":train", "");
    $sth->bindValue(":destination", "");
    $sth->bindValue(":horaires", "");
 //   $sth->bindValue(":mail", "");
    $sth->execute();
} catch (Throwable $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo (string) $e;
    exit;
}

header("HTTP/1.1 200 OK");
header("Content-Type: application/json");
echo json_encode($obj);
