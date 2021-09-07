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
  $requiredFields = ['command', 'time', 'increment', 'incrementType'];
  $data = new stdClass();
  $missingField = false;
  for ($i = 0; $i < count($requiredFields); $i++) {
    $field = $requiredFields[$i];
    if (!isset($_POST[$field]) || $_POST[$field] == '') {
      $missingField = true;
    } else {
      $data->$field = $_POST[$field];
    }
  }

  $data->days = '';
  if (isset($_POST['day1'])) $data->days .= '1';
  if (isset($_POST['day2'])) $data->days .= '2';
  if (isset($_POST['day3'])) $data->days .= '3';
  if (isset($_POST['day4'])) $data->days .= '4';
  if (isset($_POST['day5'])) $data->days .= '5';
  if (isset($_POST['day6'])) $data->days .= '6';
  if (isset($_POST['day7'])) $data->days .= '7';
  if (isset($_POST['days'])) $data->days = '1234567';

  if ($data->days == '')
    $missingField = true;

  $data->serverId = $_GET['serverId'];

  $data->repeat = $data->increment . $data->incrementType;
  if ($data->incrementType == 'n') {
    $data->repeat = '';
  }

  if (!$missingField) {
    call('/settings/tasks/create', json_encode($data));
    header("Location:../settings/tasks.php?id=" . $_GET['serverId']);
  }
} else {
  if (!isset($_GET['serverId'])) {
    header('Location:../settings');
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php $GLOBALS['page_title'] = 'ezPanel Create Task';
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
          <h5 class="card-title">Create Task</h5>
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
            <span
              style="font-size: 16px; font-weight: bold;">Server ID:</span> <?php echo $_GET['serverId'] ?>
            <br>
            <label for="command" class="card-subtitle mb-2 text-muted form-label">Command</label>
            <input class="form-control" type="text" name="command" id="command" required/>
            <br/>
            <span class="card-subtitle mb-2 text-muted">Days</span>
            <br/>
            <fieldset>
              <label class="form-check-label">
                <input class="form-check-input" type="checkbox" name="day1" id="day1" value="1">
                Sunday
              </label>
              <br>
              <label class="form-check-label">
                <input class="form-check-input" type="checkbox" name="day2" id="day2" value="2">
                Monday
              </label>
              <br>
              <label class="form-check-label">
                <input class="form-check-input" type="checkbox" name="day3" id="day3" value="3">
                Tuesday
              </label>
              <br>
              <label class="form-check-label">
                <input class="form-check-input" type="checkbox" name="day4" id="day4" value="4">
                Wednesday
              </label>
              <br>
              <label class="form-check-label">
                <input class="form-check-input" type="checkbox" name="day5" id="day5" value="5">
                Thursday
              </label>
              <br>
              <label class="form-check-label">
                <input class="form-check-input" type="checkbox" name="day6" id="day6" value="6">
                Friday
              </label>
              <br>
              <label class="form-check-label">
                <input class="form-check-input" type="checkbox" name="day7" id="day7" value="7">
                Saturday
              </label>
              <br>
              <label class="form-check-label">
                <input class="form-check-input" type="checkbox" name="days" id="everyDay" value="1234567"
                       onchange="toggled(this);">
                Every Day
              </label>
            </fieldset>
            <br/>

            <label for="time" class="card-subtitle mb-2 text-muted form-label">Time</label>
            <input class="form-control" type="time" name="time" id="time" value="00:00" required/>
            <br/>

            <span class="card-subtitle mb-2 text-muted">Repeat Every</span>
            <fieldset>
              <label for="increment" class="card-subtitle mb-2 text-muted form-label"></label>
              <input name="increment" id="increment" type="number" min="1" value="1" required>

              <label class="card-subtitle mb-2 text-muted form-label">
                <input type="radio" name="incrementType" value="d">
                Days
              </label>
              <label class="card-subtitle mb-2 text-muted form-label">
                <input type="radio" name="incrementType" value="h" checked>
                Hours
              </label>
              <label class="card-subtitle mb-2 text-muted form-label">
                <input type="radio" name="incrementType" value="m">
                Minutes
              </label>
              <label class="card-subtitle mb-2 text-muted form-label">
                <input type="radio" name="incrementType" value="n">
                No Repeat
              </label>
            </fieldset>
            <br>
            <button type="submit" class="btn btn-primary form-control">Create</button>
          </form>
        </div>
      </div>
      <div class="col-md-4"></div>
    </div>
  </div>
</div>
<script>
  function toggled(element) {
    Array.from(document.querySelectorAll('[id^="day"]')).forEach(function (e) {
      if (element.checked) {
        e.checked = false;
        e.disabled = true;
      } else {
        e.disabled = false;
      }
    });
  }
</script>
</body>
</html>

