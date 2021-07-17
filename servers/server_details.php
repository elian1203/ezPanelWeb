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

      <div class="card">
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
          <button type="button" onclick="sendCommand()">Send</button>
        </div>
      </div>
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
  document.getElementById('console-logs').addEventListener('scroll', onLogsScroll);
  scrollLogsToBottom();

  document.getElementById('command').addEventListener('keypress', commandKeyPressed);

  updateDetails();
</script>
