<?php
if (!isset($_COOKIE['123'])) {
  header('Location:../login/');
} else {
  include_once(__DIR__ . '/../protected/daemon.php');

  if (!isset($GLOBALS['current_user'])) {
    $response = call('/users/self', '');

    if (!is_int($response)) {
      $GLOBALS['current_user'] = json_decode($response);
    }
  }

  if ($GLOBALS['current_user']->permissions != '*') {
    header('Location:../users');
    return;
  }

  include_once(__DIR__ . '/../protected/daemon.php');
  $response = call('/users', '');

  if (!is_int($response)) {
    $users = json_decode($response);

    for ($i = 0; $i < count($users); $i++) {
      $user = $users[$i];
      if ($user->userId == $GLOBALS['current_user']->userId) {
        array_splice($users, $i, 1);
        break;
      }
    }

    $GLOBALS['users'] = $users;
  }
}
?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['userId'])) {
    call('/users/delete/' . $_POST['userId'], '');
    header("Location:../users");
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php $GLOBALS['page_title'] = 'ezPanel Delete User';
  include(__DIR__ . '/../sections/head.php'); ?>
</head>
<body>
<?php include(__DIR__ . '/../sections/header.php'); ?>
<div class="container-fluid main-container">
  <div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4 content col-md-offset-1">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Delete User</h5>
          <div
            class="alert alert-danger <?php
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
              echo 'no-display';
            }
            ?>"
            role="alert">
            Missing required options!
          </div>
          <form method="post"
                onsubmit="return confirm('Are you sure you want to permanently delete this user? This cannot be undone.')">
            <label for="userId">
              <h6 class="card-subtitle mb-2 text-muted">User</h6>
            </label>
            <br/>
            <select class="form-select" name="userId" id="userId">
              <?php
              for ($i = 0; $i < count($GLOBALS['users']); $i++) {
                $user = $GLOBALS['users'][$i];
                echo '<option value="' . $user->userId . '">' . $user->userId . ': ' . $user->username . '</option>';
              }
              ?>
            </select>
            <br/>
            <button type="submit" class="btn btn-danger form-control">Delete User</button>
          </form>
        </div>
      </div>
      <div class="col-md-4"></div>
    </div>
  </div>
</div>
</body>
</html>
