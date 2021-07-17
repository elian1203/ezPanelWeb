<?php
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
  http_response_code(401);
  return;
}

function call($request, $data)
{
  $user = base64_decode($_COOKIE['321']);
  $pass = base64_decode($_COOKIE['456']);

  return call_user_pass($request, $data, $user, $pass);
}

function call_user_pass($request, $data, $user, $pass)
{
  $host = '127.0.0.1';
  $port = '12521';

  $auth = 'Basic ' . base64_encode($user . ":" . $pass);

  $crl = curl_init($host . ':' . $port . $request);
  curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($crl, CURLINFO_HEADER_OUT, true);
  curl_setopt($crl, CURLOPT_POST, true);

  curl_setopt($crl, CURLOPT_HTTPHEADER, array(
    'Authorization: ' . $auth
  ));

  if (isset($data)) {
    curl_setopt($crl, CURLOPT_POSTFIELDS, $data);
  }

  $response = curl_exec($crl);
  $httpCode = curl_getinfo($crl, CURLINFO_HTTP_CODE);

  if ($httpCode == 200)
    return $response;
  else
    return $httpCode;
}
