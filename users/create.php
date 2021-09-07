<?php
if (!isset($_COOKIE['123'])) {
  header('Location:../login/');
} else {
  include_once(__DIR__ . '/../protected/daemon.php');
  $response = call('/servers/create/config', '');

  if (!is_int($response)) {
    $GLOBALS['config'] = json_decode($response);
  }
}
?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $requiredFields = ['username', 'email', 'password'];
  $data = new stdClass();
  $missingField = false;
  for ($i = 0; $i < count($requiredFields); $i++) {
    $field = $requiredFields[$i];
    if (!isset($_POST[$field]) || $_POST[$field] == '') {
      $missingField = true;
    } else {
      $data->$field = $_POST[$field];
    }
  }

  $data->username = str_replace(' ', '', $data->username);

  $permissions = '';

  foreach ($_POST as $key => $value) {
    if ($key == 'admin-permission') {
      $permissions = '*';
      break;
    } else if (preg_match('/server-all/', $key)) {
      $serverId = explode('-', $key)[2];
      $permissions .= ',server.' . $serverId . '.*';
    } else if (preg_match('/server-[0-9]+-view/', $key)) {
      $serverId = explode('-', $key)[1];
      $permissions .= ',server.' . $serverId . '.view';
    } else if (preg_match('/server-[0-9]+-console/', $key)) {
      $serverId = explode('-', $key)[1];
      $permissions .= ',server.' . $serverId . '.console';
    } else if (preg_match('/server-[0-9]+-commands/', $key)) {
      $serverId = explode('-', $key)[1];
      $permissions .= ',server.' . $serverId . '.commands';
    } else if (preg_match('/server-[0-9]+-edit/', $key)) {
      $serverId = explode('-', $key)[1];
      $permissions .= ',server.' . $serverId . '.edit';
    } else if (preg_match('/server-[0-9]+-ftp/', $key)) {
      $serverId = explode('-', $key)[1];
      $permissions .= ',server.' . $serverId . '.ftp';
    }
  }

  if (substr($permissions, 0, 1) == ',') {
    $permissions = substr($permissions, 1);
  }

  $data->permissions = $permissions;

  if ($permissions == '') {
    $missingField = true;
  }

  if (!$missingField) {
    call('/users/create', json_encode($data));
    header("Location:../users");
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php $GLOBALS['page_title'] = 'ezPanel Create User';
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
          <h5 class="card-title">Create User</h5>
          <div
            class="alert alert-danger <?php
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
              echo 'no-display';
            }
            ?>"
            role="alert">
            Missing required options!
          </div>
          <form method="post">
            <label for="username">
              <h6 class="card-subtitle mb-2 text-muted">Username</h6>
            </label>
            <br/>
            <input class="form-control" type="text" name="username" id="username" required/>
            <br/>
            <label for="email">
              <h6 class="card-subtitle mb-2 text-muted">Email</h6>
            </label>
            <br/>
            <input class="form-control" type="email" name="email" id="email" required/>
            <br/>
            <label for="password">
              <h6 class="card-subtitle mb-2 text-muted">Password</h6>
            </label>
            <br/>
            <input class="form-control" type="password" name="password" id="password" required/>
            <br/>
            <div class="row">
              <div class="col-6">
                <label for="admin-permission" class="form-label">Admin User</label>
                <input type="checkbox" name="admin-permission" id="admin-permission" onclick="adminClicked(this)">
                <br>
                <!--<label for="user-permission" class="form-label">Modify Users</label>
                <input type="checkbox" name="modify-users" id="modify-users">-->
              </div>
              <div class="col-6">
                <span class="fw-bold">Servers</span>
                <br>
                <?php
                $servers = $GLOBALS['config']->servers;
                for ($i = 0; $i < count($servers); $i++) {
                  $server = $servers[$i];

                  echo '<span class="fst-italic">' . $server->id . ': ' . $server->name . '</span>';
                  echo '<br>';
                  echo '<label for="server-all-' . $server->id . '" class="form-label">All Permissions&nbsp;&nbsp;</label>';
                  echo '<input type="checkbox" name="server-all-' . $server->id . '" id="server-all-' . $server->id . '" '
                    . ' onclick="serverAllPermissionsClicked(this)">';
                  echo '<br>';
                  echo '<label for="server-' . $server->id . '-view" class="form-label">View Server&nbsp;&nbsp;</label>';
                  echo '<input type="checkbox" name="server-' . $server->id . '-view" id="server-' . $server->id . '-view" '
                    . '>';
                  echo '<br>';
                  echo '<label for="server-' . $server->id . '-console" class="form-label">View Console&nbsp;&nbsp;</label>';
                  echo '<input type="checkbox" name="server-' . $server->id . '-console" id="server-' . $server->id . '-console" '
                    . '>';
                  echo '<br>';
                  echo '<label for="server-' . $server->id . '-commands" class="form-label">Send Commands&nbsp;&nbsp;</label>';
                  echo '<input type="checkbox" name="server-' . $server->id . '-commands" id="server-' . $server->id . '-commands" '
                    . '>';
                  echo '<br>';
                  echo '<label for="server-' . $server->id . '-edit" class="form-label">Edit&nbsp;&nbsp;</label>';
                  echo '<input type="checkbox" name="server-' . $server->id . '-edit" id="server-' . $server->id . '-edit" '
                    . '>';
                  echo '<br>';
                  echo '<label for="server-' . $server->id . '-ftp" class="form-label">FTP&nbsp;&nbsp;</label>';
                  echo '<input type="checkbox" name="server-' . $server->id . '-ftp" id="server-' . $server->id . '-ftp" '
                    . '>';
                  echo '<br>';
                }
                ?>
              </div>
            </div>
            <br>
            <button type="submit" class="btn btn-primary form-control">Create</button>
          </form>
        </div>
      </div>
      <div class="col-md-4"></div>
    </div>
  </div>
</div>
<script src="/js/user.js"></script>
</body>
</html>
