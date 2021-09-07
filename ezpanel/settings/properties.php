<?php
if (!isset($_COOKIE['123'])) {
  header('Location:../login/');
} else {
  include_once(__DIR__ . '/../protected/daemon.php');
  if (isset($_GET['id'])) {
    $response = call('/settings/properties/' . $_GET['id'], '');

    if (!is_int($response)) {
      $GLOBALS['properties'] = json_decode($response);
    }
  } else {
    header('Location:../settings');
  }
}
?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $data = new stdClass();

  foreach ($_POST as $key => $value) {
    $data->$key = $value;
  }

  $data->serverId = $_GET['id'];

  call('/settings/properties/update', json_encode($data));
  header("Location:../settings");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php $GLOBALS['page_title'] = 'Server Properties';
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
          <h5 class="card-title">Server Properties</h5>
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
            <?php
            $found = false;
            foreach ($GLOBALS['properties'] as $key => $value) {
              $found = true;
              echo '<label for="' . $key . '" class="card-subtitle mb-2 text-muted">' . $key . '</label>';
              echo '<input class="form-control" type="text" name="' . $key . '" id="' . $key . '" value="' . $value . '">';
            }
            if ($found == false) {
              echo '<div
              class="alert alert-danger"
              role="alert">
              Properties file not found. Start the server to generate one.
            </div>';
            }
            ?>
            <br>
            <button type="submit" class="btn btn-primary form-control">Update</button>
          </form>
        </div>
      </div>
      <div class="col-md-4"></div>
    </div>
  </div>
</div>
</body>
</html>
