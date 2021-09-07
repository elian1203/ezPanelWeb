<?php
if (!isset($_COOKIE['123'])) {
  header('Location:../login/');
} else {
  include_once(__DIR__ . '/../protected/daemon.php');
  $response = call('/servers', '');

  if (!is_int($response)) {
    $GLOBALS['servers'] = json_decode($response);
  }
}
?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['serverId'])) {
    call('/servers/delete/' . $_POST['serverId'], array());
    header("Location:../servers");
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php $GLOBALS['page_title'] = 'ezPanel Delete Server';
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
          <h5 class="card-title">Delete Server</h5>
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
                onsubmit="return confirm('Are you sure you want to permanently delete this server? This cannot be undone.')">
            <label for="serverId">
              <h6 class="card-subtitle mb-2 text-muted">Server</h6>
            </label>
            <br/>
            <select class="form-select" name="serverId" id="serverId">
              <?php
              for ($i = 0; $i < count($GLOBALS['servers']); $i++) {
                $server = $GLOBALS['servers'][$i];
                echo '<option value="' . $server->id . '">' . $server->id . ': ' . $server->name . '</option>';
              }
              ?>
            </select>
            <br/>
            <button type="submit" class="btn btn-danger form-control">Delete Server</button>
          </form>
        </div>
      </div>
      <div class="col-md-4"></div>
    </div>
  </div>
</div>
</body>
</html>
