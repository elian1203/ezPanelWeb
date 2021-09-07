<?php
include_once(__DIR__ . '/../protected/daemon.php');
if (isset($_GET['id'])) {
  $response = call('/servers/editable/' . $_GET['id'], '');
  if (!is_int($response)) {
    $GLOBALS['server'] = json_decode($response);
  }
  $response = call('/users', '');
  if (!is_int($response)) {
    $GLOBALS['users'] = json_decode($response);
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
    call('/servers/update/' . $GLOBALS['server']->id, json_encode($data));
    header("Location:../settings");
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <?php
  if (isset($GLOBALS['server'])) {
    $GLOBALS['page_title'] = 'Settings: ' . $GLOBALS['server']->name;
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
<div class="container-fluid main-container">
  <div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6 content col-md-offset-1">

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Edit Server - <?php echo $GLOBALS['server']->id ?></h5>
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
            <input class="form-control" type="text" name="name" id="name"
                   value="<?php echo $GLOBALS['server']->name ?>" required/>
            <br/>
            <label for="javaPath">
              <h6 class="card-subtitle mb-2 text-muted">Java Version</h6>
            </label>
            <br/>
            <select class="form-select" name="javaPath" id="javaPath">
              <option value="/bin/java">Server Default</option>
              <?php
              $response = json_decode(call('/servers/javaVersions', ''));
              foreach ($response as $versionName => $javaPath) {
                $selected = $GLOBALS['server']->javaPath == $javaPath ? 'selected' : '';
                echo '<option value="' . $javaPath . '" ' . $selected . '>' . $versionName . '</option>';
              }
              ?>
            </select
            <br/>
            <label for="serverJar">
              <h6 class="card-subtitle mb-2 text-muted">Server Jar</h6>
            </label>
            <br/>
            <select class="form-select" name="serverJar" id="serverJar" onchange="jarChanged(this)"
                    jar="<?php echo $GLOBALS['server']->serverJar ?>">
              <optgroup label="Paper">
                <?php
                $response = json_decode(file_get_contents('https://papermc.io/api/v2/projects/paper'));
                $versionGroups = $response->version_groups;

                $versions = [];
                for ($i = 0; $i < count($response->versions); $i++) {
                  array_push($versions, $response->versions[$i]);
                }

                $jarExploded = explode('-', $GLOBALS['server']->serverJar);

                for ($i = 0; $i < count($versionGroups); $i++) {
                  $versionGroup = $versionGroups[$i];
                  echo '<optgroup label="&nbsp;&nbsp;&nbsp;&nbsp;' . $versionGroup . '">';
                  for ($j = 0; $j < count($versions); $j++) {
                    $version = $versions[$j];
                    if (preg_match('/' . $versionGroup . '/', $version)) {
                      $versionSelected = ($jarExploded[0] == 'paper' && $jarExploded[1] == $version) ? 'selected' : '';
                      echo '<option value="paper-' . $version . '" ' . $versionSelected . ' >Paper - '
                        . $version . '</option>';
                    }
                  }
                  echo '</optgroup>';
                }
                ?>
              </optgroup>
              <optgroup label="Waterfall (BungeeCord Alternative)">
                <option value="waterfall-latest"<?php
                if (preg_match('/waterfall-[0-9a-zA-Z.]+-[0-9]+/', $GLOBALS['server']->serverJar))
                  echo 'selected';
                ?>>Waterfall - Latest
                </option>
              </optgroup>
              <optgroup label="Custom">
                <option value="custom"<?php
                if (!preg_match('/(paper|waterfall)-[0-9a-zA-Z.]+-[0-9]+/', $GLOBALS['server']->serverJar))
                  echo 'selected';
                ?>>Custom Jar
                </option>
              </optgroup>
            </select>
            <br/>
            <label for="jarPathRelativeTo">
              <h6 class="card-subtitle mb-2 text-muted">Server Jar Relative To</h6>
            </label>
            <br/>
            <select class="form-select" name="jarPathRelativeTo" id="jarPathRelativeTo">
              <option<?php if ($GLOBALS['server']->jarPathRelativeTo == 'Server Jar Folder') echo ' selected'; ?>>Server
                Jar Folder
              </option>
              <option<?php if ($GLOBALS['server']->jarPathRelativeTo == 'Server Base Directory') echo ' selected'; ?>>
                Server Base Directory
              </option>
              <option<?php if ($GLOBALS['server']->jarPathRelativeTo == 'Absolute') echo ' selected'; ?>>Absolute
              </option>
            </select>
            <br/>
            <label for="maximumMemory">
              <h6 class="card-subtitle mb-2 text-muted">Allotted Memory (MB)</h6>
            </label>
            <br/>
            <input class="form-control" type="number" name="maximumMemory" id="maximumMemory"
                   value="<?php echo $GLOBALS['server']->maximumMemory; ?>" min="512" required/>
            <br/>
            <label for="autoStart">
              <h6 class="card-subtitle mb-2 text-muted">Auto Start</h6>
            </label>
            <br/>
            <select class="form-select" name="autoStart" id="autoStart">
              <option<?php if ($GLOBALS['server']->autoStart == true) echo ' selected'; ?>>True</option>
              <option<?php if ($GLOBALS['server']->autoStart == false) echo ' selected'; ?>>False</option>
            </select>
            <br/>
            <label for="ftp">
              <h6 class="card-subtitle mb-2 text-muted">FTP Enabled</h6>
            </label>
            <select class="form-select" name="ftp" id="ftp">
              <option<?php if ($GLOBALS['server']->ftp == true) echo ' selected'; ?>>True</option>
              <option<?php if ($GLOBALS['server']->ftp == false) echo ' selected'; ?>>False</option>
            </select>
            <br/>
            <label for="owner">
              <h6 class="card-subtitle mb-2 text-muted">Owner</h6>
            </label>
            <br/>
            <select class="form-select" name="owner" id="owner">
              <option value="-1">None</option>
              <?php
              for ($i = 0; $i < count($GLOBALS['users']); $i++) {
                $user = $GLOBALS['users'][$i];
                $selected = $user->userId == $GLOBALS['server']->ownerId ? 'selected="selected"' : '';
                echo '<option value="' . $user->userId . '" ' . $selected . '>' . $user->username . '</option>';
              }
              ?>
            </select>
            <br/>
            <button type="submit" class="btn btn-primary form-control">Update</button>
          </form>
        </div>
      </div>
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
  input.value = document.getElementById('serverJar').getAttribute('jar');

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

