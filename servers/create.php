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
  $requiredFields = ['name', 'javaPath', 'serverJar', 'jarPathRelativeTo', 'maximumMemory', 'autoStart',
    'owner'];
  $data = new stdClass();
  for ($i = 0; $i < count($requiredFields); $i++) {
    $field = $requiredFields[$i];
    if (!isset($_POST[$field])) {
      return;
    } else {
      $data->$field = $_POST[$field];
    }
  }

  call('/servers/create', json_encode($data));
  header("Location:/servers");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php $GLOBALS['page_title'] = 'ezPanel Create Server';
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
          <h5 class="card-title">Create Server</h5>
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
            <label for="name">
              <h6 class="card-subtitle mb-2 text-muted">Server Name</h6>
            </label>
            <br/>
            <input class="form-control" type="text" name="name" id="name"/>
            <br/>
            <label for="javaPath">
              <h6 class="card-subtitle mb-2 text-muted">Java Path</h6>
            </label>
            <br/>
            <input class="form-control" type="text" name="javaPath" id="javaPath"
                   value="<?php echo $GLOBALS['config']->javaPath; ?>"/>
            <br/>
            <label for="serverJar">
              <h6 class="card-subtitle mb-2 text-muted">Server Jar</h6>
            </label>
            <br/>
            <input class="form-control" type="text" name="serverJar" id="serverJar"
                   value="<?php echo $GLOBALS['config']->defaultJar; ?>"/>
            <br/>
            <label for="jarPathRelativeTo">
              <h6 class="card-subtitle mb-2 text-muted">Server Jar Relative To</h6>
            </label>
            <br/>
            <select class="form-select" name="jarPathRelativeTo" id="jarPathRelativeTo">
              <option>Server Base Directory</option>
              <option>Absolute</option>
            </select>
            <br/>
            <label for="maximumMemory">
              <h6 class="card-subtitle mb-2 text-muted">Maximum Memory</h6>
            </label>
            <br/>
            <input class="form-control" type="text" name="maximumMemory" id="maximumMemory"
                   value="<?php echo $GLOBALS['config']->defaultMaximumMemory; ?>"/>
            <br/>
            <label for="autoStart">
              <h6 class="card-subtitle mb-2 text-muted">Auto Start</h6>
            </label>
            <br/>
            <select class="form-select" name="autoStart" id="autoStart">
              <option>True</option>
              <option>False</option>
            </select>
            <br/>
            <label for="owner">
              <h6 class="card-subtitle mb-2 text-muted">Owner</h6>
            </label>
            <br/>
            <select class="form-select" name="owner" id="owner">
              <option value="-1">None</option>
              <?php
              for ($i = 0; $i < count($GLOBALS['config']->users); $i++) {
                $user = $GLOBALS['config']->users[$i];
                echo '<option value="' . $user->userId . '">' . $user->username . '</option>';
              }
              ?>
            </select>
            <br/>
            <button type="submit" class="btn btn-primary form-control">Create</button>
          </form>
        </div>
      </div>
      <div class="col-md-4"></div>
    </div>
  </div>
</div>
</body>
</html>
