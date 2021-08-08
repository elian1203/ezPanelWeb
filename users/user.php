<?php
include_once(__DIR__ . '/../protected/daemon.php');

if (!isset($GLOBALS['current_user'])) {
  $response = call('/users/self', '');

  if (!is_int($response)) {
    $GLOBALS['current_user'] = json_decode($response);
  }
}

unset($GLOBALS['user']);
if (isset($_GET['id'])) {
  $response = call('/users', '');
  if (!is_int($response)) {
    $id = $_GET['id'];
    $users = json_decode($response);

    $userFound = false;
    for ($i = 0; $i < count($users); $i++) {
      $user = $users[$i];
      if ($user->userId == $id) {
        $GLOBALS['user'] = $user;
        break;
      }
    }
  }
}

$user = $GLOBALS['user'];
$current = $GLOBALS['current_user'];

if ($current->permissions != '*' && $user->userId != $current->userId) {
  header('Location:/users');
  return;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php
  if (isset($GLOBALS['user'])) {
    $user = $GLOBALS['user'];
    $GLOBALS['page_title'] = $user->userId . ': ' . $user->username;
  } else {
    $GLOBALS['page_title'] = "User not found!";
  }

  include(__DIR__ . '/../sections/head.php');
  echo '<script src="/js/server.js"></script>';
  ?>
</head>
<body>
<?php include(__DIR__ . '/../sections/header.php'); ?>
<div class="container">
  <?php
  if (!isset($GLOBALS['user'])) {
    echo '<div
              class="alert alert-danger"
              role="alert">
              User Not Found!
            </div>';
    return;
  } else {
    include('user_details.php');
  }
  ?>
</div>
<script src="/js/user.js"></script>
</body>
</html>
