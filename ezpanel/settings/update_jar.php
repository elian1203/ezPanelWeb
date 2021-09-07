<?php
include_once(__DIR__ . '/../protected/daemon.php');
if (isset($_GET['id'])) {
  call('/servers/updateJar/' . $_GET['id'], '');
}
return;
