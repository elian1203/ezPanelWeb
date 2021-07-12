<?php
if (!isset($_COOKIE['123'])) {
  header('Location:../login/');
}
?>

<!DOCTYPE html>
<html>
<head>
  <?php $GLOBALS['page_title'] = 'ezPanel Servers';
  include(__DIR__ . '/../sections/head.php'); ?>
</head>
<body>
<?php include(__DIR__ . '/../sections/header.php'); ?>
<div class="container-fluid">
  <div class="row">
    <div class="text-end">
      <button type="button" class="btn btn-primary">Create Server</button>
      <button type="button" class="btn btn-primary">Delete Server</button>
    </div>
  </div>
  <div class="container-fluid">
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <?php
      include(__DIR__ . '/../protected/daemon.php');
      $response = call('/servers', array());
      $json = json_decode($response);

      if (!is_int($response)) {
        for ($i = 0; $i < count($json); $i++) {
          $server = $json[$i];
          $html = '
<div class="col">
    <div class="card">
      <a href="/servers/server.php?id=$id"><img src="/servers/server_icon.php?id=$id" class="card-img-top" height="200" alt="Server Icon"></a>
      <div class="card-body">
        <a href="/servers/server.php?id=$id"><h4 class="card-title">$id: $name</h4></a>
        <div class="container-fluid">
            <div class="row">
              <div class="col-md-6">
                  <h8 class="card-title">Status: $status</h8> <br>
                  <h8 class="card-subtitle">Date Created: $dateCreated</h8> <br>
                  <h8 class="card-subtitle">Current Memory: $memory MB</h8> <br>
                  <h8 class="card-subtitle">Maximum Memory: $maximumMemory MB</h8> <br>
                  <h8 class="card-subtitle">Auto Start: $autoStart</h8> <br>
              </div>
              <div class="col-md-6">
                  <h8 class="card-subtitle">Online Players: $onlinePlayers</h8> <br>
                  <h8 class="card-subtitle">Max Players: $maxPlayers</h8> <br>
                  <h8 class="card-subtitle">Version: $version</h8> <br>
                  <h8 class="card-subtitle">MOTD: $motd</h8> <br>
                  <h8 class="card-subtitle">Players: $playerNames</h8> <br>
              </div>
            </div>
        </div>

      </div>
    </div>
  </div>';

          $html = str_replace('$id', $server->id, $html);
          $html = str_replace('$name', $server->name, $html);
          $html = str_replace('$status', $server->status, $html);
          $html = str_replace('$dateCreated', $server->dateCreated, $html);
          $html = str_replace('$memory', $server->memory, $html);
          $html = str_replace('$maximumMemory', $server->maximumMemory, $html);
          $html = str_replace('$autoStart', $server->autoStart == 1 ? "True" : "False", $html);

          $html = str_replace('$onlinePlayers', $server->onlinePlayers == null ? "0" : $server->onlinePlayers, $html);
          $html = str_replace('$maxPlayers', $server->maxPlayers == null ? "0" : $server->maxPlayers, $html);
          $html = str_replace('$version', $server->version == null ? "" : $server->version, $html);
          $html = str_replace('$motd', $server->motd == null ? "" : $server->motd, $html);
          $html = str_replace('$playerNames', $server->playerNames == null ? "" : $server->playerNames, $html);

          $html = str_replace('$offline$', '<span style="color: red;"">Offline</span>', $html);
          echo $html;
        }
      }
      ?>
    </div>
  </div>
</div>
</body>
</html>
