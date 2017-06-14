<?php
header('Content-type: application/json');

$name = $email = $message = $captcha = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['name'])) {
    $name = test_input($_POST['name']);
  }
  if (isset($_POST['email'])) {
    $email = test_input($_POST['email']);
  }
  if (isset($_POST['message'])) {
    $message = test_input($_POST['message']);
  }
  if (isset($_POST['captcha'])) {
    $captcha = $_POST['captcha'];
  }
} else {
  echo json_encode(array(
    'type' => 'fail',
    'message' => 'method not post'
  ));
  exit;
}

if( !$name || !$email || !$message ) {
  echo json_encode(array(
    'type'=>'fail',
    'message'=>'Please check that all the fields are filled out.'
  ));
  exit;
}
if ( !test_letters($name) ) {
  echo json_encode(array(
    'type'=>'fail',
    'message'=>'Name contains invalid characters'
  ));
  exit;
}
if ( !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
  echo json_encode(array(
    'type'=>'fail',
    'message'=>'Invalid email entered'
  ));
  exit;
}
if (!$captcha) {
  echo json_encode(array(
    'type'=>'fail',
    'message'=>'Please complete the CAPTCHA'
  ));
  exit;
}

$response=json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LdOUyQTAAAAAOLkeRK356ygHTGSAf-NapRSsIBT&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);

if($response['success'] == false) {
  echo json_encode(array(
    'type'=>'fail',
    'message'=>'Please complete the CAPTCHA correctly'
  ));
  exit;
} else {
  $email_from = $email;

  $body = 'Name: ' . $name . "\n\n" . 'Email: ' . $email . "\n\n" . 'Message: ' . $message;
  $success = @mail('support@mativision.com', "VRinOR site", $body, 'From: <'.$email_from.'>');
  $success = @mail('joanna.tzima@mativision.com', "VRinOR site", $body, 'From: <'.$email_from.'>');

  echo json_encode( array(
    'type'=>'success',
    'message'=>'Email sent!'
  ));
  die;
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function test_letters($string) {
  return preg_match("/^[a-zA-Z ]*$/",$string);
}

?>


