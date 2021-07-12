<?php
if (!isset($_COOKIE['123'])) {
  header('Location:../login/');
}
?>

<!DOCTYPE html>
<html>
<head>
  <?php $GLOBALS['page_title'] = 'ezPanel Settings';
  include(__DIR__ . '/../sections/head.php'); ?>
</head>
<body>
<?php include(__DIR__ . '/../sections/header.php'); ?>
<p>Settings</p>
</body>
</html>
