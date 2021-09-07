<?php
if (isset($_POST['user']) || isset($_POST['pass'])) {
  processForm();
}
?>

  <!DOCTYPE html>
  <html>
  <head>
    <?php $GLOBALS['page_title'] = 'ezPanel Login';
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
            <h5 class="card-title">Login</h5>
            <div
              class="alert alert-danger <?php if (!(isset($_POST['user']) || isset($_POST['pass']))) echo 'no-display'; ?>"
              role="alert">
              Login failed!
            </div>
            <form method="post">
              <label for="user">
                <h6 class="card-subtitle mb-2 text-muted">Username</h6>
              </label>
              <br/>
              <input class="form-control" type="text" name="user" id="user"/>
              <br/>
              <label for="pass">
                <h6 class="card-subtitle mb-2 text-muted">Password</h6>
              </label>
              <br/>
              <input class="form-control" type="password" name="pass" id="pass"/>
              <br/>
              <button type="submit" class="btn btn-primary form-control">Submit</button>
            </form>
          </div>
        </div>
        <div class="col-md-4"></div>
      </div>
    </div>
  </div>
  </body>
  </html>
<?php

function processForm()
{
  $user = $_POST['user'];
  $pass = $_POST['pass'];

  if ($user == '' || $pass == '')
    return;

  setcookie('321', base64_encode($user), time() + 60 * 60 * 24 * 100, "/");
  setcookie('456', base64_encode($pass), time() + 60 * 60 * 24 * 100, "/");

  include_once(__DIR__ . '/../protected/daemon.php');
  $response = call_user_pass('/users/login', array(), $user, $pass);

  if (!is_int($response)) {
    setcookie('123', '123', time() + 60 * 60 * 24 * 100, "/");
    setcookie('321', base64_encode($user), time() + 60 * 60 * 24 * 100, "/");
    setcookie('456', base64_encode($pass), time() + 60 * 60 * 24 * 100, "/");
    header('Location:../');
  }
}
