let queryDict = {};
location.search.substr(1).split("&").forEach(function (item) {
  queryDict[item.split("=")[0]] = item.split("=")[1]
})

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

function commandKeyPressed(event) {
  if (event.keyCode === 13) {
    sendCommand();
  }
}

function sendCommand() {
  let command = document.getElementById('command');
  serverAction('sendCommand', command.value);
  command.value = '';
}

function serverAction(action, postData) {
  let serverId = queryDict.serverId;
  if (!serverId)
    serverId = queryDict.id;
  const url = '/servers/server_action.php';
  let xhr = new XMLHttpRequest();

  let data = 'server=' + serverId + '&action=' + action;
  if (postData)
    data += '&data=' + postData;

  xhr.open('POST', url, true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  xhr.send(data);
}

function updateDetails() {
  const elementIds = [
    'status',
    'onlinePlayers',
    'maxPlayers'
  ];

  const xhr = new XMLHttpRequest();
  let serverId = queryDict.serverId;
  if (!serverId)
    serverId = queryDict.id;
  let url = '/servers/server.php?id=' + serverId + '&detailsOnly=true';
  xhr.open('GET', url);

  xhr.send();

  xhr.onload = function () {
    let details = JSON.parse(xhr.responseText);

    for (i = 0; i < elementIds.length; i++) {
      let elementId = elementIds[i];
      let element = document.getElementById(elementId);
      element.innerText = details[elementId];
    }

    let logs = details['logs'];
    for (i = 0; i < logs.length; i++) {
      let log = document.getElementById('log' + i);
      log.innerText = logs[i];

      if (log.hasAttribute("hidden"))
        log.removeAttribute("hidden")
    }

    scrollLogsToBottom();

    setTimeout(updateDetails, 1000);
  };
}

let scrolled = false;

function onLogsScroll() {
  let element = document.getElementById('console-logs');
  let variance = element.scrollTop - element.scrollHeight + element.clientHeight;

  // take absolute
  if (variance < 0)
    variance *= -1;

  scrolled = variance >= 3;
}

function scrollLogsToBottom() {
  let element = document.getElementById('console-logs');
  if (scrolled === false) {
    element.scrollTop = element.scrollHeight - element.clientHeight;
  }
}
