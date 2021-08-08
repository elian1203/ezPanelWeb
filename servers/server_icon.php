<?php
include_once(__DIR__ . '/../protected/daemon.php');
if (isset($_GET['id'])) {
  $response = call('/servers/icon/' . $_GET['id'], array());
  if (!is_int($response)) {
    header('Content-Type: image/gif');
    echo $response;
  }
}
