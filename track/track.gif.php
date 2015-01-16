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

  Tracking::add_gs_record ($cache_hash);

  header ('Content-type:image/gif');
  echo join
  (
    array
    (
      chr(0x47), chr(0x49), chr(0x46), chr(0x38), chr(0x39), chr(0x61),
      chr(0x01), chr(0x00), chr(0x01), chr(0x00), chr(0x80), chr(0xff),
      chr(0x00), chr(0xff), chr(0xff), chr(0xff), chr(0x00), chr(0x00),
      chr(0x00), chr(0x2c), chr(0x00), chr(0x00), chr(0x00), chr(0x00),
      chr(0x01), chr(0x00), chr(0x01), chr(0x00), chr(0x00), chr(0x02),
      chr(0x02), chr(0x44), chr(0x01), chr(0x00), chr(0x3b)
    )
  );
?>
