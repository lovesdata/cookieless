<?php

session_start ();

class Tracking
{
  static function prepare_session ($server, $cache_hash, $uid)
  {
    $page_url = $server["HTTP_REFERER"];
    $ip_address = Tracking::get_client_ip ();

    $ua = Tracking::parse_user_agent ();
    $ua["browser"] = empty ($ua["browser"]) ? "n/a" : $ua["browser"];
    $ua["version"] = empty ($ua["version"]) ? "n/a" : $ua["version"];
    $ua["platform"] = empty ($ua["platform"]) ? "n/a" : $ua["platform"];

    $current_url = Tracking::parse_location ($page_url);
    $utm_source = $current_url["utm_source"];
    $utm_medium = $current_url["utm_medium"];
    $utm_campaign = $current_url["utm_campaign"];
    $utm_content = $current_url["utm_content"];
    $utm_term = $current_url["utm_term"];
    $utm_id = $current_url["utm_id"];
    $gclid = $current_url["gclid"];

    return array
    (
      "tracking_url" => $page_url,
      "tracking_ip" => $ip_address,
      "tracking_uid" => $uid,
      "tracking_ua" => $server["HTTP_USER_AGENT"],
      "tracking_browser" => $ua["browser"],
      "tracking_browser_version" => $ua["version"],
      "tracking_os" => $ua["platform"],
      "tracking_width" => "",
      "tracking_height" => "",
      "tracking_viewport_size" => "",
      "tracking_flash" => "",
      "tracking_java" => "",
      "tracking_title" => "",
      "tracking_description" => "",
      "tracking_encoding" => "",
      "tracking_user_language" => Tracking::get_user_language ($_SERVER["HTTP_ACCEPT_LANGUAGE"]),
      "tracking_utm_source" => $utm_source,
      "tracking_utm_medium" => $utm_medium,
      "tracking_utm_campaign" => $utm_campaign,
      "tracking_utm_content" => $utm_content,
      "tracking_utm_term" => $utm_term,
      "tracking_utm_id" => $utm_id,
      "tracking_gclid" => $gclid,
      "tracking_caching" => $cache_hash,
      "tracking_time" => time ()
    );
  }

  static function update_session ($cache_hash, $get)
  {
    $current_url = Tracking::parse_location ($get["url"]);

    $_SESSION["visits"][$cache_hash]["data"]["tracking_width"] = $get["scr_width"];
    $_SESSION["visits"][$cache_hash]["data"]["tracking_height"] = $get["scr_height"];
    $_SESSION["visits"][$cache_hash]["data"]["tracking_viewport_size"] = $get["viewport_size"];
    $_SESSION["visits"][$cache_hash]["data"]["tracking_flash"] = $get["flash"];
    $_SESSION["visits"][$cache_hash]["data"]["tracking_java"] = $get["java"];
    $_SESSION["visits"][$cache_hash]["data"]["tracking_title"] = $get["title"];
    $_SESSION["visits"][$cache_hash]["data"]["tracking_description"] = $get["description"];
    $_SESSION["visits"][$cache_hash]["data"]["tracking_encoding"] = $get["encoding"];
    $_SESSION["visits"][$cache_hash]["data"]["tracking_user_language"] = $get["user_language"];
    $_SESSION["visits"][$cache_hash]["data"]["tracking_caching"] -= $get["ch"];
    $_SESSION["visits"][$cache_hash]["data"]["tracking_utm_source"] = $current_url["utm_source"];
    $_SESSION["visits"][$cache_hash]["data"]["tracking_utm_medium"] = $current_url["utm_medium"];
    $_SESSION["visits"][$cache_hash]["data"]["tracking_utm_campaign"] = $current_url["utm_campaign"];
    $_SESSION["visits"][$cache_hash]["data"]["tracking_utm_content"] = $current_url["utm_content"];
    $_SESSION["visits"][$cache_hash]["data"]["tracking_utm_term"] = $current_url["utm_term"];
    $_SESSION["visits"][$cache_hash]["data"]["tracking_utm_id"] = $current_url["utm_id"];
    $_SESSION["visits"][$cache_hash]["data"]["tracking_gclid"] = $current_url["gclid"];
    $_SESSION["visits"][$cache_hash]["data"]["tracking_url"] = $get["url"];
  }

  static function track ($server, $cache_hash, $ref)
  {
    $db = mysql_connect ( DB_HOST, DB_USER, DB_PASSWORD);
    mysql_select_db ( DB_DATABASE, $db);
    mysql_query ("SET NAMES utf8");

    $page_url = Tracking::get_page_url ();
    $ip_address = Tracking::get_client_ip ();

    $user_agent = Tracking::get_user_agent ();

    $ua = Tracking::parse_user_agent ();

    $ua["browser"] = empty ($ua["browser"]) ? "n/a" : $ua["browser"];
    $ua["version"] = empty ($ua["version"]) ? "n/a" : $ua["version"];
    $ua["platform"] = empty ($ua["platform"]) ? "n/a" : $ua["platform"];

    $current_url = $ref == 1 ? Tracking::parse_location ($server["HTTP_REFERER"]) : Tracking::parse_location ("http://" . $server["HTTP_HOST"] . $server["REQUEST_URI"]);

    $utm_source = $current_url["utm_source"];
    $utm_medium = $current_url["utm_medium"];
    $utm_campaign = $current_url["utm_campaign"];
    $utm_content = $current_url["utm_content"];
    $utm_term = $current_url["utm_term"];
    $utm_id = $current_url["utm_id"];
    $gclid = $current_url["gclid"];

    $qry = '
      INSERT INTO
        tracking_visits
      VALUES
        (
         null,
         "' . mysql_real_escape_string ($page_url) . '",
         "' . mysql_real_escape_string ($ip_address) . '",
         "' . mysql_real_escape_string (Tracking::get_uid ($server)) . '",
         "' . mysql_real_escape_string ($user_agent) . '",
         "' . mysql_real_escape_string ($ua["browser"]) . '",
         "' . mysql_real_escape_string ($ua["version"]) . '",
         "' . mysql_real_escape_string ($ua["platform"]) . '",
         "",
         "",
         "",
         "",
         "",
         "",
         "",
         "",
         "' . Tracking::get_user_language ($_SERVER["HTTP_ACCEPT_LANGUAGE"]) . '",
         "' . mysql_real_escape_string ($utm_source) . '",
         "' . mysql_real_escape_string ($utm_medium) . '",
         "' . mysql_real_escape_string ($utm_campaign) . '",
         "' . mysql_real_escape_string ($utm_content) . '",
         "' . mysql_real_escape_string ($utm_term) . '",
         "' . (int) $utm_id . '",
         "' . mysql_real_escape_string ($gclid) . '",
         "' . (int) $cache_hash . '",
         null
         )
       ';

   mysql_query ($qry);
   $id = mysql_insert_id ();
   return $id;
  }

  static function add_gs_record ($key)
  {
    $url_obj = parse_url ($_SESSION["visits"][$key]["data"]["tracking_url"]);

    $track_params = array
    (
      "v" => 1,
      "tid" => ANALYTICS_PROPERTY_ID,
      "cid" => $_SESSION["visits"][$key]["data"]["tracking_uid"],
      "t" => "pageview",
      "dh" => $url_obj["host"],
      "dp" => $url_obj["path"],
      "dt" => $_SESSION["visits"][$key]["data"]["tracking_title"],
      "dl" => $_SESSION["visits"][$key]["data"]["tracking_url"],
      "sr" => $_SESSION["visits"][$key]["data"]["tracking_width"] > 0 ? $_SESSION["visits"][$key]["data"]["tracking_width"] . "x" . $_SESSION["visits"][$key]["data"]["tracking_height"] : "",
      "vp" => str_replace (" ", "", $_SESSION["visits"][$key]["data"]["tracking_viewport_size"]),
      "de" => $_SESSION["visits"][$key]["data"]["tracking_encoding"],
      "ul" => $_SESSION["visits"][$key]["data"]["tracking_user_language"],
      "je" => $_SESSION["visits"][$key]["data"]["tracking_java"] == "" ? 0 :1,
      "fl" => $_SESSION["visits"][$key]["data"]["tracking_flash"],
      "cd" => $_SESSION["visits"][$key]["data"]["tracking_description"],

      "cs" => $_SESSION["visits"][$key]["data"]["tracking_utm_source"],
      "cm" => $_SESSION["visits"][$key]["data"]["tracking_utm_medium"],
      "ck" => $_SESSION["visits"][$key]["data"]["tracking_utm_campaign"],
      "cc" => $_SESSION["visits"][$key]["data"]["tracking_utm_content"],
      "ct" => $_SESSION["visits"][$key]["data"]["tracking_utm_term"],
      "ci" => $_SESSION["visits"][$key]["data"]["tracking_utm_id"],
      "gclid" => $_SESSION["visits"][$key]["data"]["tracking_gclid"],

      "cm1" => $_SESSION["visits"][$key]["data"]["tracking_ip"], // The IP address of the visitor
      "cm2" => $_SESSION["visits"][$key]["data"]["tracking_browser"], // The browser of the visitor
      "cm3" => $_SESSION["visits"][$key]["data"]["tracking_browser_version"], // The browser version of the visitor
      "cm4" => $_SESSION["visits"][$key]["data"]["tracking_os"], // The OS of the visitor
    );
//file_put_contents (dirname (__FILE__) . '/' . time (), print_r ($track_params, true));
    file_get_contents ("http://www.google-analytics.com/collect?" . http_build_query ($track_params));
    unset ($_SESSION["visits"][$key]);
  }

  static function clear_old ()
  {
    foreach ($_SESSION["visits"] as $key => $value)
    {
      if (time () - $value["time"] > TIMEOUT_SECONDS)
      {
        //Tracking::add_db_record ($key);
        Tracking::add_gs_record ($key);
      }
    }
  }

  static function get_uid ($server)
  {
    $keys = array
    (
      "HTTP_USER_AGENT",
      "SERVER_PROTOCOL",
      "HTTP_ACCEPT_CHARTSET",
      "HTTP_ACCEPT_ENCODING",
      "HTTP_ACCEPT_LANGUAGE"
    );

    $tmp = "";
    foreach ($keys as $key)
    {
      if (isset ($server[$key]))
      {
        $tmp .= $server[$key];
      }
    }
    return md5 ($tmp);
  }

  static function parse_location ($url)
  {
    $current_url = parse_url ($url);
    $get_params = explode ("&", $current_url["query"]);
    for ($i = 0, $j = count ($get_params);$i < $j;$i++)
    {
      $params_part = explode ("=", $get_params[$i]);
      $url_params[$params_part[0]] = $params_part[1];
    }

    return $url_params;
  }

  static function caching_hash ()
  {
    $hash = crc32 (microtime () . rand (1, 10000));

    return $hash;
  }

  static function js_update_records ($get)
  {
    $db = mysql_connect( DB_HOST, DB_USER, DB_PASSWORD);
    mysql_select_db( DB_DATABASE, $db);
    mysql_query('SET NAMES utf8');

    $page_params = Tracking::parse_location ($get["url"]);

    $jqry = '
      UPDATE
        tracking_visits
      SET
        tracking_width="' . (int) $get['scr_width']  . '",
        tracking_height="' . (int) $get['scr_height']  . '",
        tracking_viewport_size="' . mysql_real_escape_string ($get['viewport_size']) . '",
        tracking_flash="' . mysql_real_escape_string ($get['flash'])  . '",
        tracking_java="' . mysql_real_escape_string ($get['java'])  . '",
        tracking_title="' . mysql_real_escape_string ($get['title'])  . '",
        tracking_description="' . mysql_real_escape_string ($get['description']) . '",
        tracking_encoding="' . mysql_real_escape_string ($get['encoding'])  . '",
        tracking_user_language="' . mysql_real_escape_string ($get['user_language'])  . '",
        tracking_caching=tracking_caching-' . (int) $get['ch'] . ',
        tracking_utm_source="' . mysql_real_escape_string ($page_params['utm_source']) . '",
        tracking_utm_medium="' . mysql_real_escape_string ($page_params['utm_medium']) . '",
        tracking_utm_campaign="' . mysql_real_escape_string ($page_params['utm_campaign']) . '",
        tracking_utm_content="' . mysql_real_escape_string ($page_params['utm_content']) . '",
        tracking_utm_term="' . mysql_real_escape_string ($page_params['utm_term']) . '",
        tracking_utm_id="' . mysql_real_escape_string ($page_params['utm_id']) . '",
        tracking_gclid="' . mysql_real_escape_string ($page_params['gclid']) . '",
        tracking_url="' . mysql_real_escape_string ($get['url']) . '"
      WHERE
        tracking_id="' . (int) $get['uid']  . '"
        ';
    mysql_query ($jqry);
    file_put_contents ("out", $jqry);
  }

  static function parse_user_agent( $u_agent = null )
  {
    if(is_null($u_agent) && isset($_SERVER['HTTP_USER_AGENT'])) $u_agent = $_SERVER['HTTP_USER_AGENT'];

    $empty = array(
        'platform' => null,
        'browser'  => null,
        'version'  => null,
    );

    $data = $empty;

    if(!$u_agent) return $data;

    if( preg_match('/\((.*?)\)/im', $u_agent, $parent_matches) )
    {
      preg_match_all('/(?P<platform>Android|CrOS|iPhone|iPad|Linux|Macintosh|Windows(\ Phone\ OS)?|Silk|linux-gnu|BlackBerry|PlayBook|Nintendo\ (WiiU?|3DS)|Xbox)
          (?:\ [^;]*)?
          (?:;|$)/imx', $parent_matches[1], $result, PREG_PATTERN_ORDER);

      $priority = array('Android', 'Xbox');
      $result['platform'] = array_unique($result['platform']);
      if( count($result['platform']) > 1 ) {
          if( $keys = array_intersect($priority, $result['platform']) ) {
              $data['platform'] = reset($keys);
          }else{
              $data['platform'] = $result['platform'][0];
          }
      }elseif(isset($result['platform'][0])){
          $data['platform'] = $result['platform'][0];
      }
    }

    if( $data['platform'] == 'linux-gnu' ) { $data['platform'] = 'Linux'; }
    if( $data['platform'] == 'CrOS' ) { $data['platform'] = 'Chrome OS'; }

    preg_match_all('%(?P<browser>Camino|Kindle(\ Fire\ Build)?|Firefox|Safari|MSIE|AppleWebKit|Chrome|IEMobile|Opera|OPR|Silk|Lynx|Version|Wget|curl|NintendoBrowser|PLAYSTATION\ (\d|Vita)+)
            (?:;?)
            (?:(?:[/ ])(?P<version>[0-9A-Z.]+)|/(?:[A-Z]*))%ix',
    $u_agent, $result, PREG_PATTERN_ORDER);

    $key = 0;

    if (!isset($result['browser'][0]) || !isset($result['version'][0])) {
        return $empty;
    }

    $data['browser'] = $result['browser'][0];
    $data['version'] = $result['version'][0];

    if( $key = array_search( 'Playstation Vita', $result['browser'] ) !== false ) {
        $data['platform'] = 'PlayStation Vita';
        $data['browser'] = 'Browser';
    }elseif( ($key = array_search( 'Kindle Fire Build', $result['browser'] )) !== false || ($key = array_search( 'Silk', $result['browser'] )) !== false ) {
        $data['browser']  = $result['browser'][$key] == 'Silk' ? 'Silk' : 'Kindle';
        $data['platform'] = 'Kindle Fire';
        if( !($data['version'] = $result['version'][$key]) || !is_numeric($data['version'][0]) ) {
            $data['version'] = $result['version'][array_search( 'Version', $result['browser'] )];
        }
    }elseif( ($key = array_search( 'NintendoBrowser', $result['browser'] )) !== false || $data['platform'] == 'Nintendo 3DS' ) {
        $data['browser']  = 'NintendoBrowser';
        $data['version']  = $result['version'][$key];
    }elseif( ($key = array_search( 'Kindle', $result['browser'] )) !== false ) {
        $data['browser']  = $result['browser'][$key];
        $data['platform'] = 'Kindle';
        $data['version']  = $result['version'][$key];
    }elseif( ($key = array_search( 'OPR', $result['browser'] )) !== false || ($key = array_search( 'Opera', $result['browser'] )) !== false ) {
        $data['browser'] = 'Opera';
        $data['version'] = $result['version'][$key];
        if( ($key = array_search( 'Version', $result['browser'] )) !== false ) { $data['version'] = $result['version'][$key]; }
    }elseif( $result['browser'][0] == 'AppleWebKit' ) {
        if( ( $data['platform'] == 'Android' && !($key = 0) ) || $key = array_search( 'Chrome', $result['browser'] ) ) {
            $data['browser'] = 'Chrome';
            if( ($vkey = array_search( 'Version', $result['browser'] )) !== false ) { $key = $vkey; }
        }elseif( $data['platform'] == 'BlackBerry' || $data['platform'] == 'PlayBook' ) {
            $data['browser'] = 'BlackBerry Browser';
            if( ($vkey = array_search( 'Version', $result['browser'] )) !== false ) { $key = $vkey; }
        }elseif( $key = array_search( 'Safari', $result['browser'] ) ) {
            $data['browser'] = 'Safari';
            if( ($vkey = array_search( 'Version', $result['browser'] )) !== false ) { $key = $vkey; }
        }

        $data['version'] = $result['version'][$key];
    }elseif( $result['browser'][0] == 'MSIE' ){
        if( $key = array_search( 'IEMobile', $result['browser'] ) ) {
            $data['browser'] = 'IEMobile';
        }else{
            $data['browser'] = 'MSIE';
            $key = 0;
        }
        $data['version'] = $result['version'][$key];
    }elseif( $key = array_search( 'PLAYSTATION 3', $result['browser'] ) !== false ) {
        $data['platform'] = 'PlayStation 3';
        $data['browser']  = 'NetFront';
    }
    return $data;
  }

  static function get_user_language ($accept_language, $deflang = "en")
  {
    if(isset ($http_accept) && strlen ($http_accept) > 1)
    {
      $x = explode (",", $http_accept);
      foreach ($x as $val)
      {
        if(preg_match("/(.*);q=([0-1]{0,1}\.\d{0,4})/i",$val,$matches))
        {
          $lang[$matches[1]] = (float)$matches[2];
        }
        else
        {
          $lang[$val] = 1.0;
        }
      }
      $qval = 0.0;
      foreach ($lang as $key => $value)
      {
        if ($value > $qval)
        {
          $qval = (float) $value;
          $deflang = $key;
        }
      }
    }
    return strtolower($deflang);
  }

  static function get_client_ip ()
  {
    $ipaddress = "";
    if ($_SERVER["HTTP_CLIENT_IP"])
    {
      $ipaddress = $_SERVER["HTTP_CLIENT_IP"];
    }
    else if ($_SERVER["HTTP_X_FORWARDED_FOR"])
    {
      $ipaddress = $_SERVER["HTTP_X_FORWARDED_FOR"];
    }
    else if ($_SERVER["HTTP_X_FORWARDED"])
    {
      $ipaddress = $_SERVER["HTTP_X_FORWARDED"];
    }
    else if ($_SERVER["HTTP_FORWARDED_FOR"])
    {
      $ipaddress = $_SERVER["HTTP_FORWARDED_FOR"];
    }
    else if ($_SERVER["HTTP_FORWARDED"])
    {
      $ipaddress = $_SERVER["HTTP_FORWARDED"];
    }
    else if ($_SERVER["REMOTE_ADDR"])
    {
      $ipaddress = $_SERVER["REMOTE_ADDR"];
    }
    else
    {
      $ipaddress = "UNKNOWN";
    }

    return $ipaddress;
	}

  static function get_protocol ($server)
  {
    if (!empty ($server["HTTPS"]) && $server["HTTPS"] !== "off" || $server["SERVER_PORT"] == 443)
    {
      $protocol = "https://";
    }
    else
    {
      $protocol = "http://";
    }

    return $protocol;
  }

  static function get_page_url ()
  {
    return $_SERVER["HTTP_REFERER"];
  }

  static function get_user_agent ()
  {
    return $_SERVER["HTTP_USER_AGENT"];
  }
}
?>
