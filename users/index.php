<?php
if (!isset($_COOKIE['123'])) {
  header('Location:../login/');
}
?>

<!DOCTYPE html>
<html>
<head>
  <?php $GLOBALS['page_title'] = 'ezPanel Users';
  include(__DIR__ . '/../sections/head.php'); ?>
</head>
<body>
<?php include(__DIR__ . '/../sections/header.php'); ?>
<p>Users</p>
</body>
</html>
