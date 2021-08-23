<?php
//if (!isset($_COOKIE['123'])) {
//  header('Location:../login/');
//}
//?>

<!DOCTYPE html>
<html>
<head>
  <?php $GLOBALS['page_title'] = 'ezPanel';
  include(__DIR__ . '/sections/head.php'); ?>
</head>
<body>
<?php include(__DIR__ . '/sections/header.php'); ?>
<div class="container-fluid main-container">
  <div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4 content col-md-offset-1">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">ezPanel</h5>
          <p class="card-text">
            Welcome to ezPanel, the open source Minecraft server control panel
          </p>
        </div>
      </div>
      <div class="col-md-4"></div>
    </div>
  </div>
</div>
</body>
</html>
