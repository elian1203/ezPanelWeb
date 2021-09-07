<?php
if (!isset($_COOKIE['123'])) {
  header('Location:../login/');
} else {
  include_once(__DIR__ . '/../protected/daemon.php');
  $response = call('/users', '');

  if (!is_int($response)) {
    $GLOBALS['users'] = json_decode($response);
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php $GLOBALS['page_title'] = 'ezPanel Users';
  include(__DIR__ . '/../sections/head.php'); ?>
</head>
<body>
<?php include(__DIR__ . '/../sections/header.php'); ?>
<div class="container-fluid main-container">
  <div class="container-fluid">
    <div class="row">
      <div class="text-end">
        <a href="create.php">
          <button type="button" class="btn btn-primary">Create User</button>
        </a>
        <a href="delete.php">
          <button type="button" class="btn btn-primary">Delete User</button>
        </a>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6 content col-md-offset-1">

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Users</h5>
          <table class="table wrap-columns">
            <thead>
            <tr>
              <th scope="col">ID</th>
              <th scope="col">Username</th>
              <th scope="col">Email</th>
              <th scope="col">Permissions</th>
            </tr>
            </thead>
            <tbody>
            <?php
            for ($i = 0; $i < count($GLOBALS['users']); $i++) {
              $user = $GLOBALS['users'][$i];
              echo '<tr onclick="window.location = \'/users/user.php?id=' . $user->userId . '\'"><td>' . $user->userId . '</td><td>' . $user->username . '</td>' .
                '<td>' . $user->email . '</td><td>' . $user->permissions . '</td></tron>';
            }
            ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-md-3"></div>
    </div>
  </div>
</div>
</body>
</html>
