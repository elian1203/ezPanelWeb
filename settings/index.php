<?php
if (!isset($_COOKIE['123'])) {
  header('Location:../login/');
} else {
  include_once(__DIR__ . '/../protected/daemon.php');
  $response = call('/servers/editable', '');

  if (!is_int($response)) {
    $GLOBALS['servers'] = json_decode($response);
  }
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
<div class="container-fluid">
  <div class="row">
    <div class="col-2"></div>
    <div class="col-4">
      <div class="card">
        <div class="card-header">
          <div class="card-title">Global Settings</div>
        </div>
        <div class="card-body">

        </div>
      </div>
    </div>
    <div class="col-4">
      <div class="card">
        <div class="card-header">
          <div class="card-title">Server Settings</div>
        </div>
        <div class="card-body">
          <table>
            <tbody>
            <?php
            foreach ($GLOBALS['servers'] as $server) {
              echo '<tr>';
              echo '<td>' . $server->id . ': ' . $server->name . '</td>';
              echo '<td><a style="margin: 20px;" href="/settings/settings.php?id=' . $server->id . '"><button class="btn btn-primary" type="button">Settings</button></a></td>';
              echo '<td><a style="margin-right: 20px;" href="/settings/tasks.php?id=' . $server->id . '"><button class="btn btn-primary" type="button">Tasks</button></a></td>';
              echo '<td><a onclick="updateJar(' . $server->id . ')"><button class="btn btn-primary" type="button">Update Jar</button></a></td>';
              echo '</tr>';
              echo '<tr><td></td></tr>';
            }
            ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-4"></div>
  </div>
</div>
<script>
  function updateJar(serverId) {
    const http = new XMLHttpRequest();
    http.open('GET', '/update_jar.php?id=' + serverId);
    http.send(null);
  }
</script>
</body>
</html>
