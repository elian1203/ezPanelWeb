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
  updateButtons();
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

      if (element.innerText !== details[elementId])
        element.innerText = details[elementId];
    }

    let logs = details['logs'];
    for (i = 0; i < logs.length; i++) {
      let log = document.getElementById('log' + i);
      if (log.innerHTML !== logs[i])
        log.innerHTML = logs[i];

      if (log.hasAttribute("hidden"))
        log.removeAttribute("hidden")
    }

    scrollLogsToBottom();

    setTimeout(updateDetails, 1000);
  };
}

function updateButtons() {
  let start = document.getElementById('button-start');
  let stop = document.getElementById('button-stop');
  let restart = document.getElementById('button-restart');
  let kill = document.getElementById('button-kill');

  let status = document.getElementById('status').innerText;

  if (status === 'Online') {
    disableButton(start);
    enableButton(stop);
    enableButton(restart);
    enableButton(kill);
  } else if (status === 'Offline') {
    enableButton(start);
    disableButton(stop);
    disableButton(restart);
    disableButton(kill);
  } else if (status === 'Starting' || status === 'Stopping') {
    disableButton(start);
    disableButton(stop);
    disableButton(restart);
    enableButton(kill);
  }
}

function disableButton(button) {
  if (!button.hasAttribute('disabled')) {
    button.setAttribute('disabled', 'disabled');
  }
}

function enableButton(button) {
  if (button.hasAttribute('disabled')) {
    button.removeAttribute('disabled');
  }
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
