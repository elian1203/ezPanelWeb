<?php
setcookie('123', '', time() - 60 * 60 * 24 * 101, "/");
setcookie('321', '', time() - 60 * 60 * 24 * 101, "/");
setcookie('456', '', time() - 60 * 60 * 24 * 101, "/");

if (isset($GLOBALS['current_user'])) {
  unset($GLOBALS['current_user']);
}

header('Location:/login/');
