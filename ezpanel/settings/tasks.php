<?php
include_once(__DIR__ . '/../protected/daemon.php');
if (isset($_GET['id'])) {
  $response = call('/servers/editable/' . $_GET['id'], array());
  if (!is_int($response)) {
    $GLOBALS['server'] = json_decode($response);
  }
  $response = call('/settings/tasks/' . $_GET['id'], '');
  if (!is_int($response)) {
    $GLOBALS['tasks'] = json_decode($response);
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <?php
  if (isset($GLOBALS['server'])) {
    $GLOBALS['page_title'] = 'Tasks: ' . $GLOBALS['server']->name;
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
  <div class="container-fluid">
    <div class="row">
      <div class="text-end">
        <a href="../settings/task_create.php?serverId=<?php echo $GLOBALS['server']->id; ?>">
          <button type="button" class="btn btn-primary">Create Task</button>
        </a>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8 content col-md-offset-1">

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Tasks for <?php echo $GLOBALS['server']->id . ': ' . $GLOBALS['server']->name ?></h5>
          <table class="table wrap-columns">
            <thead>
            <tr>
              <th scope="col">Task ID</th>
              <th scope="col">Command</th>
              <th scope="col">Days</th>
              <th scope="col">Time</th>
              <th scope="col">Repeat</th>
              <th scope="col">Last Run</th>
              <th scope="col">Next Run</th>
              <th scope="col">Delete</th>
            </tr>
            </thead>
            <tbody>
            <?php
            for ($i = 0; $i < count($GLOBALS['tasks']); $i++) {
              $task = $GLOBALS['tasks'][$i];
              echo '<tr><td>' . $task->taskId . '</td><td>' . $task->command . '</td>' .
                '<td>' . $task->days . '</td><td>' . $task->time . '</td><td>' . $task->repeat . '</td><td>' .
                $task->lastRun . '</td><td>' . $task->nextRun . '</td><td><a href="task_delete.php?id=' . $task->taskId . '">
                    <button class="btn btn-primary" onclick="return confirm(\'Are you sure you want to delete this task?\')">
                    Delete Task</button></a></td></tr>';
            }
            ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
