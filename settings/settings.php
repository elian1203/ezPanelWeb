<?php
include_once(__DIR__ . '/../protected/daemon.php');
if (isset($_GET['id'])) {
  $response = call('/servers/editable/' . $_GET['id'], array());
  if (!is_int($response)) {
    $GLOBALS['server'] = json_decode($response);
  }
  $response = call('/users', array());
  if (!is_int($response)) {
    $GLOBALS['users'] = json_decode($response);
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <?php
  if (isset($GLOBALS['server'])) {
    $GLOBALS['page_title'] = 'Settings: ' . $GLOBALS['server']->name;
  } else {
    $GLOBALS['page_title'] = "Server not found!";
  }

  include(__DIR__ . '/../sections/head.php');
  echo '<script src="/js/server.js"></script>';
  ?>
</head>
<body>
<?php include(__DIR__ . '/../sections/header.php'); ?>

<div class="container">
  <?php
  if (!isset($GLOBALS['server'])) {
    echo '<div
              class="alert alert-danger"
              role="alert">
              Server Not Found!
            </div>';
    return;
  } else {
    include('settings_server.php');
  }
  ?>
</div>
</body>
</html>

