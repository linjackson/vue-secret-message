<?php
session_start();
header('Content-type: application/json');

// This path isn't the login path, so there's no reason for someone to visit it. Send a message to snoopers.

// Check that it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  http_response_code(403);
  $server__response__error = array(
    "code"=>http_response_code(403),
    "status"=>false,
    "message"=>"access: PERMISSION DENIED....and...Stop trying to make fetch happen."
  );
  echo json_encode($server__response__error);
} else {
  // If it's not a POST request.
  http_response_code(403);
  $server__response__error = array(
    "code"=>http_response_code(403),
    "status"=>false,
    "message"=>"access: PERMISSION DENIED....and...Stop trying to make fetch happen."
  );
  echo json_encode($server__response__error);
}
