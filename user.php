<?php 

/**
 * @var PDO $dbh
 */
$dbh = require 'include.php';

/**
 * @var string $action
 */
$action = strtolower(filter_input (INPUT_SERVER, "REQUEST_METHOD"));

if ($action === "get") {
	get_action();
} else if ($action === "put") {
	put_action();
} else {
	bad_method_call();
}

/**
 * Get http action response
 */
function get_action() 
{
	$token = filter_input(INPUT_GET, "token");
//	$type= filter_input(INPUT_GET, "type");
	if (!$token) {
        header("HTTP/1.1 412 Precondition Failed");
        exit;
	}
	try {
	    if (!$type) {
	        $result = get_single_user($token);
	    } else if ("aller" === $type) {
	        $result = get_aller($token);
	    } else if ("retour" === $type) {
	        $result = get_retour($token);
	    } else if ("all" === $type){
	        $result = get_all($token);
	    }
	    
	} catch (Throwable $e) {
	    header("HTTP/1.1 500 Internal Server Error");
	    var_dump( (string) $e);
	    exit;
	}
	if(!$result) {
		$result = new stdClass();
		$result->error = "No Found";
		header("HTTP/1.1 404 No Found");
 	}
    header("Content-Type: application/json");
	echo json_encode($result, JSON_PRETTY_PRINT);
}
/**
 * 
 * @global type $dbh
 * @param type $token
 * @return type
 */
function get_single_user ($token) {
    global $dbh;
    $sql = "SELECT * FROM `users` WHERE `token` = :token";
    $sth = $dbh->prepare($sql);
    $sth->bindValue(":token", $token);
    $sth->execute();
    return $sth->fetch(pdo::FETCH_OBJ);
}
/**
 * 
 * @global type $dbh
 * @param type $token
 * @return type
 */
function get_aller ($token) {
     global $dbh;
     $sql = "SELECT * 
            FROM `users` 
            JOIN `trip_aller` 
            ON `users`.`trip_aller`=`trip`.`a_trip` 
            WHERE `token` =:token";
     $sth = $dbh->prepare($sql);
     $sth->bindValue(":token", $token);
     $sth->execute();
     return $sth->fetch(pdo::FETCH_OBJ);
}
/**
 * 
 * @global type $dbh
 * @param type $token
 * @return type
 */
function get_retour ($token) {
    global $dbh;
    $sql = "SELECT * 
            FROM `users` 
            JOIN `trip_retour` 
            ON `users`.`trip_id2`=`trip`.`r_trip` 
            WHERE `token` =:token";
    $sth = $dbh->prepare($sql);
    $sth->bindValue("type", $token);
    $sth->execute();
    return $sth->fetch(pdo::FETCH_OBJ);
}
/**
 * 
 * @global type $dbh
 * @param type $token
 * @return type
 */
function get_all($token) {
    global $dbh;
    $sql = "SELECT * "
         . "FROM `users` "
         . "JOIN `trip_aller` "
         . "ON `users`.`trip_id1`=`trip_aller`.`a_trip` "
         . "JOIN `trip_retour` "
         . "ON `users`.`trip_id2`=`trip_retour`.`r_trip` "
         . "WHERE `token` = :token";
    $sth = $dbh->prepare($sql);
    $sth->bindValue(":token", $token);
    $sth->execute();
    return $sth->fetch(pdo::FETCH_OBJ);    
}

/**
 * Put http action response
 */
function put_action() 
{
	global $dbh;
	try {
		$input = [];
		$flux = file_get_contents("php://input");
		parse_str($flux, $input);
		var_dump($input);
		if(!array_key_exists("token", $input)) {
				header("HTTP/1.1 412 Precondition Failed");
				exit;
	    }
    	$sql = "INSERT INTO `users`(`token`) VALUES (:token)";
    	$sth = $dbh->prepare($sql);
    	$sth->bindValue(":token", $input["token"]);
    	$sth->execute();
    	header("HTTP/1.1 200 OK");
	} catch (Throwable $e) {
	    header("HTTP/1.1 500 Internal Server Error");
		exit;
	}
}




/**
 * Bad http action response
 */
function bad_method_call()
{
	header("HTTP/1.1 405 Bad Method Call");
	exit;
}
