function start() {
  serverAction('start');
}

function stop() {
  serverAction('stop');
}

function restart() {
  serverAction('restart');
}

function kill() {
  serverAction('kill');
}

function serverAction(action) {
  let serverId = localStorage.serverId;
  const url = '/servers/server_action.php';
  let xhr = new XMLHttpRequest();
  let data = 'server=' + serverId + '&action=' + action;

  xhr.open('POST', url, true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  xhr.send(data);
}
