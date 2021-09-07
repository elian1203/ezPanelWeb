<?php
include_once(__DIR__ . '/../protected/daemon.php');
if (isset($_COOKIE['123']) && !isset($GLOBALS['current_user'])) {
  $response = call('/users/self', '');

  if (!is_int($response)) {
    $GLOBALS['current_user'] = json_decode($response);
  }
}
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light header-margin">
  <div class="container-fluid">
    <a class="navbar-brand unselectable" disabled="true">ezPanel</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?php
          $request = $_SERVER['REQUEST_URI'];
          if ($request == '/' || substr($request, 0, 5) == '/home')
            echo 'active';
          ?>" aria-current="page" href="../">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php
          $request = $_SERVER['REQUEST_URI'];
          if (substr($request, 0, 8) == '/servers')
            echo 'active';
          ?>" href="../servers">Servers</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php
          $request = $_SERVER['REQUEST_URI'];
          if (substr($request, 0, 6) == '/users')
            echo 'active';
          ?>" href="../users">Users</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php
          $request = $_SERVER['REQUEST_URI'];
          if (substr($request, 0, 9) == '/settings')
            echo 'active';
          ?>" href="../settings">Settings</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php
          $request = $_SERVER['REQUEST_URI'];
          if (substr($request, 0, 6) == '/about')
            echo 'active';
          ?>" href="../about">About</a>
        </li>
      </ul>
    </div>
    <div class="collapse navbar-collapse justify-content-end">
      <ul class="navbar-nav">
        <li class="nav-item active">
          <?php
          if (isset($_COOKIE['123'])) {
            echo '<a class="nav-link" href="../login/logout.php">Logout</a>';
          } else {
            echo '<a class="nav-link" href="../login/">Login</a>';
          }
          ?>
        </li>
      </ul>
    </div>
  </div>
</nav>
