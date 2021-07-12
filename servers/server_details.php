<div class="container">
  <div class="row">
    <div class="col-md-9">
      <div class="container">
        <h3><?php echo $GLOBALS['server']->id; ?>: <?php echo $GLOBALS['server']->name; ?></h3>
        <h5>Status: <?php echo $GLOBALS['server']->status; ?></h5>
        <p>
          Online Players: <?php echo $GLOBALS['server']->onlinePlayers ?> <br>
          Maximum Players: <?php echo $GLOBALS['server']->maxPlayers ?>
        </p>
      </div>

      <div class="card">
        <div class="card-header">
          Console
        </div>
        <div class="card-body">
          <ul id="console-logs" class="list-scrollable list-no-bullets">
            <?php
            $logs = $GLOBALS['server']->logs;
            for ($i = 0; $i < count($logs); $i++) {
              echo '<li class="console-log">' . $logs[$i] . '</li>';
            }
            ?>
          </ul>
          <input type="text" placeholder="Enter console command" style="width: 90%;">
          <button type="button">Send</button>
        </div>
      </div>
      <?php echo json_encode($GLOBALS['server']); ?>
    </div>
    <div class="col-md-3">
      <button type="button" class="btn btn-primary" onclick="start()">Start</button>
      <button type="button" class="btn btn-primary" onclick="stop()">Stop</button>
      <button type="button" class="btn btn-primary" onclick="restart()">Restart</button>
      <button type="button" class="btn btn-primary" onclick="kill()">Kill</button>
    </div>
  </div>
</div>
<script>
  function gotoBottom(id){
    var element = document.getElementById(id);
    element.scrollTop = element.scrollHeight - element.clientHeight;
  }
  gotoBottom('console-logs');
</script>
