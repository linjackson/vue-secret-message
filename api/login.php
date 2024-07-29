<?php
session_start();
header('Content-type: application/json');

// Check that it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Check that there's a username, password, and token
  if (!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['token'])) {
    // Set variables
    $username = $_POST['username'];
    $password = $_POST['password'];
    $token = $_POST['token'];

    // Check that there's a session token and set a variable for it
    if (!empty($_SESSION['token'])) {
      $session_token = $_SESSION['token'];

      // If the next allowed login attempt is later than now
      if (!empty($_SESSION['login_after']) && $_SESSION['login_after'] > time()) {
        // Login is currently locked
        http_response_code(403);
        // Calculate how long until login is allowed
        $_SESSION['login_after'] = strtotime('+30 seconds');
        $_SESSION['login_attempts'] = 0;
        $wait = $_SESSION['login_after'] - time();
        $server__response__error = array(
          "code"=>http_response_code(403),
          "status"=>false,
          "message"=>"access: PERMISSION DENIED...too many login attempts. Wait $wait seconds.",
          "countdown"=>"$wait"
        );
        echo json_encode($server__response__error);
      } else {
        // A login attempt is allowed, so try checking the database for this user
        try {
          require 'dbconnect.php';
          $SELECT__USER__DATA = "SELECT * FROM `users` WHERE users.username=:username";
          $select__user__statement = $con->prepare($SELECT__USER__DATA);
          $select__user__statement->bindParam(':username', $username, PDO::PARAM_STR);
          $select__user__statement->execute();
          $user__flag = $select__user__statement->rowCount();
          if ($user__flag > 0) {
            // If the user is found in the database
            $user__data = $select__user__statement->fetch(PDO::FETCH_ASSOC);
            // Check the password
            if (password_verify($password, $user__data['password']) && $token === $session_token) {
              // If correct password
              $user__object = array(
                "username"=>$user__data['username']
              );
              http_response_code(200);
              $server__response__success = array(
                "code" => http_response_code(200),
                "status" => true,
                "message" => "Thanks for logging in to Vue this secret message.",
                "userData" => $user__object
              );
              echo json_encode($server__response__success);
            } else {
              // If wrong password, record a failed attempt
              $_SESSION['login_attempts']++;
              // If there have already been two wrong login attempts and this is the third, set the next allowed login time for 30 seconds out
              if (!empty($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= 3 && !empty($_SESSION['login_after'] && $_SESSION['login_after'] < time())) {
                $_SESSION['login_after'] = strtotime('+30 seconds');
                $_SESSION['login_attempts'] = 0;
                // Calculate the seconds until the next login is allowed
                $wait = $_SESSION['login_after'] - time();
                http_response_code(403);
                $server__response__error = array(
                  "code"=>http_response_code(403),
                  "status"=>false,
                  "message"=>"access: PERMISSION DENIED...too many login attempts. Wait $wait seconds.",
                  "countdown" => $wait,
                  "login_attempts" => $_SESSION['login_attempts'],
                  "login_after" => date($_SESSION['login_after']),
                  "now" => date(time())
                );
                echo json_encode($server__response__error);
              } else {
                // Otherwise the regular wrong login message
                http_response_code(403);
                $server__response__error = array(
                  "code" => http_response_code(403),
                  "status" => false,
                  "message" => "access: PERMISSION DENIED....and...YOU DIDN'T SAY THE MAGIC WORD."
                );
                echo json_encode($server__response__error);
              }
            }
          } else {
            // If the user was not found in the database
            // If wrong password, record a failed attempt
            $_SESSION['login_attempts']++;
            // If there have already been two wrong login attempts and this is the third, set the next allowed login time for 30 seconds out
            if (!empty($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= 3 && !empty($_SESSION['login_after'] && $_SESSION['login_after'] < time())) {
              $_SESSION['login_after'] = strtotime('+30 seconds');
              $_SESSION['login_attempts'] = 0;
              // Calculate the seconds until the next login is allowed
              $wait = $_SESSION['login_after'] - time();
              http_response_code(403);
              $server__response__error = array(
                "code" => http_response_code(403),
                "status" => false,
                "message" => "access: PERMISSION DENIED....and...I don't even know who you are.",
                "countdown" => $wait,
                "login_attempts" => $_SESSION['login_attempts'],
                "login_after" => $_SESSION['login_after'],
                "now" => time()
              );
              echo json_encode($server__response__error);
            } else {
              // If login is not timed out
              http_response_code(403);
              $server__response__error = array(
                "code" => http_response_code(403),
                "status" => false,
                "message" => "access: PERMISSION DENIED....and...I don't even know who you are.",
                "login_attempts" => $_SESSION['login_attempts']
              );
              echo json_encode($server__response__error);
            }
          }
        } catch (Exception $ex) {
          // Catch exceptions
          http_response_code(404);
            $server__response__error = array(
              "code" => http_response_code(404),
              "status" => false,
              "message" => "Oh no! Something bad happened. " . $ex->getMessage()
            );
            echo json_encode($server__response__error);
          }
        }
    } else {
      // If the session token is missing
      $_SESSION['login_attempts']++;
      http_response_code(403);
      $server__response__error = array(
        "code" => http_response_code(403),
        "status" => false,
        "message" => "access: PERMISSION DENIED....and...There's no token."
      );
      echo json_encode($server__response__error);
    }
  } else {
    // If username, password, or token are missing
    $_SESSION['login_attempts']++;
    if (!empty($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= 3 && !empty($_SESSION['login_after'] && $_SESSION['login_after'] < time())) {
      // If login should be timed out
      $_SESSION['login_after'] = strtotime('+30 seconds');
      $_SESSION['login_attempts'] = 0;
      // Calculate the seconds until the next login is allowed
      $wait = $_SESSION['login_after'] - time();
      http_response_code(403);
      $server__response__error = array(
        "code" => http_response_code(403),
        "status" => false,
        "message" => "access: PERMISSION DENIED....and...Missing something?",
        "countdown" => $wait,
        "login_attempts" => $_SESSION['login_attempts'],
        "login_after" => $_SESSION['login_after'],
        "now" => time()
      );
      echo json_encode($server__response__error);
    } else {
      // If login is not timed out
      http_response_code(403);
      $server__response__error = array(
        "code" => http_response_code(403),
        "status" => false,
        "message" => "access: PERMISSION DENIED....and...Missing something?",
        "login_attempts" => $_SESSION['login_attempts']
      );
      echo json_encode($server__response__error);
    }
  }
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
