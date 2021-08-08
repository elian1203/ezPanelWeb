<?php
if (isset($_POST['server']) && isset($_POST['action'])) {
  $action = $_POST['action'];
  $server = $_POST['server'];

  $request = '/servers/' . $action . '/' . $server;

  include_once(__DIR__ . '/../protected/daemon.php');
  if (isset($_POST['data'])) {
    call($request, $_POST['data']);
  } else {
    call($request, array());
  }

  http_response_code(200);
} else {
  http_response_code(400);
}
