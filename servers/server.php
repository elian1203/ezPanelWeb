<?php
include_once(__DIR__ . '/../protected/daemon.php');
if (isset($_GET['id'])) {
  $response = call('/servers/details/' . $_GET['id'], array());
  if (!is_int($response)) {
    $GLOBALS['server'] = json_decode($response);
  }

  $response = call('/servers/ftpport', '');
  $GLOBALS['ftpPort'] = intval($response);
}

if (isset($GLOBALS['server']) && isset($_GET['detailsOnly']) && $_GET['detailsOnly'] == "true") {
  echo json_encode($GLOBALS['server']);
  return;
}
?>
<!DOCTYPE html>
<html>
<head>
  <?php
  if (isset($GLOBALS['server'])) {
    $GLOBALS['page_title'] = 'Server: ' . $GLOBALS['server']->name;
  } else {
    $GLOBALS['page_title'] = "Server not found!";
  }

  include(__DIR__ . '/../sections/head.php');
  echo '<script src="/js/server.js"></script>';
  ?>
</head>
<body>
<?php include(__DIR__ . '/../sections/header.php'); ?>

<div class="container">
  <?php
  if (!isset($GLOBALS['server'])) {
    echo '<div
              class="alert alert-danger"
              role="alert">
              Server Not Found!
            </div>';
    return;
  }
  ?>
</div>
<div class="container">
  <div class="row">
    <div class="col-md-9">
      <div class="container">
        <h3><?php echo $GLOBALS['server']->id; ?>: <?php echo $GLOBALS['server']->name; ?></h3>
        <h5>Status: <span id="status"><?php echo $GLOBALS['server']->status; ?></span></h5>
        <p>
          Online Players: <span id="onlinePlayers"><?php echo $GLOBALS['server']->onlinePlayers ?></span> <br>
          Maximum Players: <span id="maxPlayers"><?php echo $GLOBALS['server']->maxPlayers ?></span>
        </p>
      </div>
    </div>
    <div class="col-md-3">
      <div<?php
      $perm = $GLOBALS['current_user']->permissions;
      $serverId = $GLOBALS['server']->id;

      if ($perm != '*' && !preg_match('/server.' . $serverId . '.[*]/', $perm)
        && !preg_match('/server.' . $serverId . '.commands/', $perm)) {
        echo ' style="display: none;"';
      }
      ?>>
        <button type="button" class="btn btn-primary" onclick="start()" id="button-start">Start</button>
        <button type="button" class="btn btn-primary" onclick="stop()" id="button-stop">Stop</button>
        <button type="button" class="btn btn-primary" onclick="restart()" id="button-restart">Restart</button>
        <button type="button" class="btn btn-primary" onclick="kill()" id="button-kill">Kill</button>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-9">
      <div class="card"<?php
      $perm = $GLOBALS['current_user']->permissions;
      $serverId = $GLOBALS['server']->id;

      if ($perm != '*' && !preg_match('/server.' . $serverId . '.[*]/', $perm)
        && !preg_match('/server.' . $serverId . '.console/', $perm)) {
        echo ' style="display: none;"';
      }
      ?>>
        <div class="card-header">
          Console
        </div>
        <div class="card-body">
          <ul id="console-logs" class="list-scrollable list-no-bullets">
            <?php
            $logs = $GLOBALS['server']->logs;
            $count = count($logs);
            for ($i = 0; $i < 500; $i++) {
              if ($i < $count)
                echo '<li class="console-log" id="log' . $i . '">' . $logs[$i] . '</li>';
              else
                echo '<li class="console-log" id="log' . $i . '" hidden="true"></li>';
            }
            ?>
          </ul>
          <input id="command" type="text" placeholder="Enter console command" style="width: 90%;">
          <button class="btn btn-primary" type="button" onclick="sendCommand()">Send</button>
        </div>
      </div>
    </div>
    <div class="col-3">
      <div class="card"<?php
      $userPerm = $GLOBALS['current_user']->permissions;
      $perm = '/server.' . $GLOBALS['server']->id . '.ftp/';
      $perm2 = '/server.' . $GLOBALS['server']->id . '.[*]]/';
      if ($GLOBALS['ftpPort'] == -1 || !$GLOBALS['server']->ftp ||
        (!preg_match($perm, $userPerm) && !preg_match($perm2, $userPerm) && $userPerm !== '*'))
        echo ' style="display: none;"';
      ?>>
        <div class="card-header">FTP</div>
        <div class="card-body">
          <p class="card-text fw-bold">You can access the server's files using FileZilla with these connection
            properties</p>
          <p class="card-text">
            Host: <?php echo file_get_contents('http://ipecho.net/plain/'); ?> <br>
            Port: <?php echo $GLOBALS['ftpPort']; ?> <br>
            User: <?php echo $GLOBALS['current_user']->username . '.' . $GLOBALS['server']->id ?> <br>
            Pass: <span class="fst-italic">Your password</span>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  document.getElementById('console-logs').addEventListener('scroll', onLogsScroll);
  scrollLogsToBottom();

  document.getElementById('command').addEventListener('keypress', commandKeyPressed);

  updateDetails();
</script>
</body>
</html>
