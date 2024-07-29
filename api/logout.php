<?php
session_start();
header('Content-type: application/json');

// Clear the session variables
$_SESSION = array();

header('Content-type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // If it is a POST request, process the logout
  http_response_code(200);
  $server__response__success = array(
    "code" => http_response_code(200),
    "status" => true,
    "message" => "logout success"
  );
  echo json_encode($server__response__success);
} else {
  // Don't allow GET requests
  http_response_code(403);
  $server__response__error = array(
    "code" => http_response_code(403),
    "status" => false,
    "message" => "access: PERMISSION DENIED....and...STOP TRYING TO GET INTO THIS."
  );
  echo json_encode($server__response__error);
}
