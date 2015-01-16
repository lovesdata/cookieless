<?php
  include_once (dirname (__FILE__) . "/tracking.class.php");
  include_once (dirname (__FILE__) . "/config.php");

  Tracking::clear_old ();

  $cache_hash = Tracking::caching_hash ();
  $uid = Tracking::get_uid ($_SERVER);

  $_SESSION["visits"][$cache_hash] = array
  (
    "time" => time (),
    "data" => Tracking::prepare_session ($_SERVER, $cache_hash, $uid)
  );

  header ('Content-type:text/javascript');
  echo "var uid='$uid';";
  echo "var ch='$cache_hash';";
  echo "var postUrl = '" . TRACK_URL . "';";
  readfile (dirname (__FILE__) . "/post.js");
?>
