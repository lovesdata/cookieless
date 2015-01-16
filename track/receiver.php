<?php
  include_once (dirname (__FILE__) . "/tracking.class.php");
  include_once (dirname (__FILE__) . "/config.php");

  Tracking::update_session ($_GET["ch"], $_GET);
  Tracking::add_gs_record ($_GET["ch"]);
?>
