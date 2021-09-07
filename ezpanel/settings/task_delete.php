<?php
if (isset($_GET['id'])) {
  $id = $_GET['id'];
  require_once(__DIR__ . '/../protected/daemon.php');
  call('/settings/tasks/delete/' . $id, '');
}

header('Location:../settings');
