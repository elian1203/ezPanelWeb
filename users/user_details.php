<?php
if (!isset($_COOKIE['123'])) {
  header('Location:../login/');
} else {
  $response = call('/servers', '');

  if (!is_int($response)) {
    $GLOBALS['servers'] = json_decode($response);
  }
}
?>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $user = $GLOBALS['user'];
  $headerLocation = '';

  if (isset($_POST['email']) && $_POST['email'] != '' && $_POST['email'] != $user->email) {
    $object = new stdClass();
    $object->userId = $GLOBALS['user']->userId;
    $object->email = $_POST['email'];

    call('/users/set/email', json_encode($object));
  }

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
    }
  }

  if ($permissions != $user->permissions) {
    if (substr($permissions, 0, 1) == ',') {
      $permissions = substr($permissions, 1);
    }

    $object = new stdClass();
    $object->userId = $GLOBALS['user']->userId;
    $object->permissions = $permissions;

    call('/users/set/permissions', json_encode($object));
  }

  if (isset($_POST['password']) && $_POST['password'] != '') {
    $object = new stdClass();
    $object->userId = $user->userId;
    $object->password = $_POST['password'];

    call('/users/set/password', json_encode($object));

    if ($user->userId == $GLOBALS['current_user']->userId) {
      $headerLocation = 'Location:/login/logout.php';
    }
  }

  if ($headerLocation == '')
    $headerLocation = 'Location:/users';

  header($headerLocation);
}

?>
<div class="container-fluid">
  <div class="row">
    <div class="col-3"></div>
    <div class="col-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">User Details</h5>
          <div class="alert alert-danger no-display" id="required-fields-error">Missing required fields!</div>
          <div class="alert alert-danger no-display" id="password-match-error">Passwords do not match!</div>
          <span style="font-size: 16px; font-weight: bold;">ID:</span> <?php echo $GLOBALS['user']->userId ?>
          <br>
          <span style="font-size: 16px; font-weight: bold;">Username:</span> <?php echo $GLOBALS['user']->username ?>
          <form method="post">
            <label for="email" class="form-label">Email:</label>
            <input class="form-control" type="text" name="email" id="email"
                   value="<?php echo $GLOBALS['user']->email ?>"/>
            <label for="password" class="form-label">Password:</label>
            <input class="form-control" type="password" name="password" id="password">
            <label for="password-confirm" class="form-label">Confirm Password:</label>
            <input class="form-control" type="password" name="password-confirm" id="password-confirm">
            <br>
            <label class="form-label fw-bold">Permissions</label>
            <br>
            <div class="row">
              <div class="col-6">
                <label for="admin-permission" class="form-label">Admin User</label>
                <input type="checkbox" name="admin-permission" id="admin-permission" onclick="adminClicked(this)"
                  <?php
                  if ($GLOBALS['user']->permissions == '*') echo ' checked';
                  if ($GLOBALS['user']->userId == $GLOBALS['current_user']->userId) echo ' disabled';
                  ?>
                >
                <br>
                <!--<label for="user-permission" class="form-label">Modify Users</label>
                <input type="checkbox" name="modify-users" id="modify-users">-->
              </div>
              <div class="col-6">
                <span class="fw-bold">Servers</span>
                <br>
                <?php
                $user = $GLOBALS['user'];
                for ($i = 0; $i < count($GLOBALS['servers']); $i++) {
                  $server = $GLOBALS['servers'][$i];

                  $allChecked = preg_match('/server.' . $server->id . '.[*]/', $user->permissions) ? 'checked' : '';
                  $viewChecked = preg_match('/server.' . $server->id . '.view/', $user->permissions) ? 'checked' : '';
                  $consoleChecked = preg_match('/server.' . $server->id . '.console/', $user->permissions) ? 'checked' : '';
                  $commandsChecked = preg_match('/server.' . $server->id . '.commands/', $user->permissions) ? 'checked' : '';
                  $editChecked = preg_match('/server.' . $server->id . '.edit/', $user->permissions) ? 'checked' : '';

                  if ($user->userId == $GLOBALS['current_user']->userId) {
                    $allChecked .= ' disabled';
                    $viewChecked .= ' disabled';
                    $consoleChecked .= ' disabled';
                    $commandsChecked .= ' disabled';
                    $editChecked .= ' disabled';
                  }

                  echo '<span class="fst-italic">' . $server->id . ': ' . $server->name . '</span>';
                  echo '<br>';
                  echo '<label for="server-all-' . $server->id . '" class="form-label">All Permissions&nbsp;&nbsp;</label>';
                  echo '<input type="checkbox" name="server-all-' . $server->id . '" id="server-all-' . $server->id . '" '
                    . $allChecked . ' onclick="serverAllPermissionsClicked(this)">';
                  echo '<br>';
                  echo '<label for="server-' . $server->id . '-view" class="form-label">View Server&nbsp;&nbsp;</label>';
                  echo '<input type="checkbox" name="server-' . $server->id . '-view" id="server-' . $server->id . '-view" '
                    . $viewChecked . '>';
                  echo '<br>';
                  echo '<label for="server-' . $server->id . '-console" class="form-label">View Console&nbsp;&nbsp;</label>';
                  echo '<input type="checkbox" name="server-' . $server->id . '-console" id="server-' . $server->id . '-console" '
                    . $consoleChecked . '>';
                  echo '<br>';
                  echo '<label for="server-' . $server->id . '-commands" class="form-label">Send Commands&nbsp;&nbsp;</label>';
                  echo '<input type="checkbox" name="server-' . $server->id . '-commands" id="server-' . $server->id . '-commands" '
                    . $commandsChecked . '>';
                  echo '<br>';
                  echo '<label for="server-' . $server->id . '-edit" class="form-label">Edit&nbsp;&nbsp;</label>';
                  echo '<input type="checkbox" name="server-' . $server->id . '-edit" id="server-' . $server->id . '-edit" '
                    . $editChecked . '>';
                  echo '<br>';
                }
                ?>
              </div>
            </div>
            <button type="submit" class="btn btn-primary form-control" onclick="return validateUserForm();">Save
            </button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-3"></div>
  </div>
</div>
