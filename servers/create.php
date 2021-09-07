<?php
if (!isset($_COOKIE['123'])) {
  header('Location:../login/');
} else {
  include_once(__DIR__ . '/../protected/daemon.php');
  $response = call('/servers/create/config', '');

  if (!is_int($response)) {
    $GLOBALS['config'] = json_decode($response);
  }
}
?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $requiredFields = ['name', 'javaPath', 'serverJar', 'jarPathRelativeTo', 'maximumMemory', 'autoStart', 'ftp', 'owner'];
  $data = new stdClass();
  $missing = false;
  for ($i = 0; $i < count($requiredFields); $i++) {
    $field = $requiredFields[$i];
    if (!isset($_POST[$field]) || $_POST[$field] == '') {
      $missing = true;
      break;
    } else {
      $data->$field = $_POST[$field];
    }
  }

  $serverJar = $data->serverJar;
  if ($serverJar == 'custom') {
    if (isset($_POST['custom-jar']) && $_POST['custom-jar'] != '') {
      $serverJar = $_POST['custom-jar'];
    } else {
      $missing = true;
    }
  } else if (preg_match('/paper-[0-9a-zA-Z-.]+/', $serverJar)) {
    $split = explode('-', $serverJar);
    $url = 'https://papermc.io/api/v2/projects/' . $split[0] . '/versions/' . $split[1];
    $response = json_decode(file_get_contents($url));

    $latestBuild = $response->builds[count($response->builds) - 1];

    $serverJar .= '-' . $latestBuild . '.jar';
  } else if ($serverJar == 'waterfall-latest') {
    $versions = json_decode(file_get_contents('https://papermc.io/api/v2/projects/waterfall'))->versions;
    $latestVersion = $versions[count($versions) - 1];
    $builds = json_decode(file_get_contents('https://papermc.io/api/v2/projects/waterfall/versions/' . $latestVersion))->builds;
    $latestBuild = $builds[count($builds) - 1];

    $serverJar = 'waterfall-' . $latestVersion . '-' . $latestBuild . '.jar';
  }

  $data->serverJar = $serverJar;

  if ($missing == false) {
    call('/servers/create', json_encode($data));
    header("Location:../servers");
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php $GLOBALS['page_title'] = 'ezPanel Create Server';
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
          <h5 class="card-title">Create Server</h5>
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
            <label for="name">
              <h6 class="card-subtitle mb-2 text-muted">Server Name</h6>
            </label>
            <br/>
            <input class="form-control" type="text" name="name" id="name" required/>
            <br/>
            <label for="javaPath">
              <h6 class="card-subtitle mb-2 text-muted">Java Version</h6>
            </label>
            <br/>
            <select class="form-select" name="javaPath" id="javaPath">
              <option value="/bin/java">Server Default</option>
              <?php
              foreach ($GLOBALS['config']->javaVersions as $versionName => $javaPath) {
                echo '<option value="' . $javaPath . '">' . $versionName . '</option>';
              }
              ?>
            </select>
            <br/>
            <label for="serverJar">
              <h6 class="card-subtitle mb-2 text-muted">Server Jar</h6>
            </label>
            <br/>
            <select class="form-select" name="serverJar" id="serverJar" onchange="jarChanged(this)">
              <optgroup label="Paper">
                <?php
                $response = json_decode(file_get_contents('https://papermc.io/api/v2/projects/paper'));
                $versionGroups = $response->version_groups;

                $versions = [];
                for ($i = 0; $i < count($response->versions); $i++) {
                  array_push($versions, $response->versions[$i]);
                }

                for ($i = 0; $i < count($versionGroups); $i++) {
                  $versionGroup = $versionGroups[$i];
                  echo '<optgroup label="&nbsp;&nbsp;&nbsp;&nbsp;' . $versionGroup . '">';
                  for ($j = 0; $j < count($versions); $j++) {
                    $version = $versions[$j];
                    if (preg_match('/' . $versionGroup . '/', $version)) {
                      $versionSelected = ($i == count($versionGroups) - 1) && ($j == count($versions) - 1) ? 'selected="selected"' : '';
                      echo '<option value="paper-' . $version . '" ' . $versionSelected . ' >Paper - '
                        . $version . '</option>';
                    }
                  }
                  echo '</optgroup>';
                }
                ?>
              </optgroup>
              <optgroup label="Waterfall (BungeeCord Alternative)">
                <option value="waterfall-latest">Waterfall - Latest</option>
              </optgroup>
              <optgroup label="Custom">
                <option value="custom">Custom Jar</option>
              </optgroup>
            </select>
            <br/>
            <label for="jarPathRelativeTo">
              <h6 class="card-subtitle mb-2 text-muted">Server Jar Relative To</h6>
            </label>
            <br/>
            <select class="form-select" name="jarPathRelativeTo" id="jarPathRelativeTo">
              <option selected="selected">Server Jar Folder</option>
              <option>Server Base Directory</option>
              <option>Absolute</option>
            </select>
            <br/>
            <label for="maximumMemory">
              <h6 class="card-subtitle mb-2 text-muted">Maximum Memory (MB)</h6>
            </label>
            <br/>
            <input class="form-control" type="number" name="maximumMemory" id="maximumMemory"
                   value="2048" min="512" required/>
            <br/>
            <label for="autoStart">
              <h6 class="card-subtitle mb-2 text-muted">Auto Start</h6>
            </label>
            <br/>
            <select class="form-select" name="autoStart" id="autoStart">
              <option>True</option>
              <option>False</option>
            </select>
            <br/>
            <label for="ftp">
              <h6 class="card-subtitle mb-2 text-muted">FTP Enabled</h6>
            </label>
            <select class="form-select" name="ftp" id="ftp">
              <option>True</option>
              <option>False</option>
            </select>
            <br/>
            <label for="owner">
              <h6 class="card-subtitle mb-2 text-muted">Owner</h6>
            </label>
            <br/>
            <select class="form-select" name="owner" id="owner">
              <option value="-1">None</option>
              <?php
              for ($i = 0; $i < count($GLOBALS['config']->users); $i++) {
                $user = $GLOBALS['config']->users[$i];
                $selected = $user->userId == $GLOBALS['current_user']->userId ? 'selected="selected"' : '';
                echo '<option value="' . $user->userId . '" ' . $selected . '>' . $user->username . '</option>';
              }
              ?>
            </select>
            <br/>
            <button type="submit" class="btn btn-primary form-control">Create</button>
          </form>
        </div>
      </div>
      <div class="col-md-4"></div>
    </div>
  </div>
</div>
<script>
  function insertAfter(newNode, referenceNode) {
    referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
  }

  const input = document.createElement('input');
  input.type = 'text';
  input.id = 'custom-jar';
  input.name = 'custom-jar';
  input.classList.add('form-control');
  input.value = 'custom.jar';
  input.required = true;

  jarChanged(document.getElementById('serverJar'));

  function jarChanged(select) {
    if (select.value === 'custom') {
      insertAfter(input, select);
    } else {
      if (document.getElementById('custom-jar')) {
        input.parentNode.removeChild(input);
      }
    }
  }
</script>
</body>
</html>
