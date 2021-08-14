<?php
include_once(__DIR__ . '/../protected/daemon.php');
if (isset($_GET['id'])) {
  $response = call('/servers/details/' . $_GET['id'], array());
  if (!is_int($response)) {
    $GLOBALS['server'] = json_decode($response);
  }

  $response = call('/servers/ftpport', '');
  $GLOBALS['ftpPort'] = intval($response);
}

if (isset($GLOBALS['server']) && isset($_GET['detailsOnly']) && $_GET['detailsOnly'] == "true") {
  echo json_encode($GLOBALS['server']);
  return;
}
?>
<!DOCTYPE html>
<html>
<head>
  <?php
  if (isset($GLOBALS['server'])) {
    $GLOBALS['page_title'] = 'Server: ' . $GLOBALS['server']->name;
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
    include('server_details.php');
  }
  ?>
</div>
</body>
</html>
