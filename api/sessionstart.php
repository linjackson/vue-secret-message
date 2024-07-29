<?php
session_start();
header('Content-type: application/json');

// If there is no session token, set one and zero out the other values
if (empty($_SESSION['token'])) {
  $_SESSION['token'] = bin2hex(random_bytes(32));
  $_SESSION['login_attempts'] = 0;
  $_SESSION['login_after'] = time();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // If it is a POST request
  if (!empty($_SESSION['login_after']) && $_SESSION['login_after'] > time()) {
    // If there is a login_after value and it's later than now
    // Login is currently locked
    http_response_code(403);
    // Calculate how long until login is allowed
    $wait = $_SESSION['login_after'] - time();
    http_response_code(200);
    $server__response__success = array(
      "code" => http_response_code(200),
      "status" => true,
      "token" => $_SESSION['token'],
      "countdown" => $wait,
      "login_attempts" => $_SESSION['login_attempts']
    );
    echo json_encode($server__response__success);
  } else {
    // Success
    http_response_code(200);
    $server__response__success = array(
      "code" => http_response_code(200),
      "status" => true,
      "token" => $_SESSION['token']
    );
    echo json_encode($server__response__success);
  }
} else {
  // If it is not a POST request
  http_response_code(403);
  $server__response__error = array(
    "code" => http_response_code(403),
    "status" => false,
    "message" => "access: PERMISSION DENIED....STOP TRYING TO GET INTO THIS."
  );
  echo json_encode($server__response__error);
}
