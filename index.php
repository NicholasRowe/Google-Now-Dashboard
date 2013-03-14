<?php
session_start();
require_once('inc/adodb/adodb.inc.php');
require_once('inc/google/Google_Client.php');
require_once('inc/google/contrib/Google_CalendarService.php');
require_once('inc/google/contrib/Google_TasksService.php');

$client = new Google_Client();
$notacal = new Google_CalendarService($client);
$notatasksService = new Google_TasksService($client);

if (isset($_GET['logout'])) {
  session_unset();
}

if (isset($_SESSION['access_token'])) {
	$client->setAccessToken($_SESSION['access_token']);
} else {
	$client->setAccessToken($client->authenticate($_GET['code']));
	$_SESSION['access_token'] = $client->getAccessToken();
}
//	Use index.php as a base so they can share a single access token
if(@$_GET['m'] == 'cal')
{
	include('calendar_json.php');
}
elseif(@$_GET['m'] == 'task')
{
	include('tasks_json.php');
}
else
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href='css/global.css' rel='stylesheet' type='text/css'>
<link href='css/weather.css' rel='stylesheet' type='text/css'>
<link href='css/tasks.css' rel='stylesheet' type='text/css'>
<link href='css/nextclass.css' rel='stylesheet' type='text/css'>
<link href='css/loader.css' rel='stylesheet' type='text/css'>
<link href='css/music.css' rel='stylesheet' type='text/css'>
<link href='css/reddit.css' rel='stylesheet' type='text/css'>
<meta content="minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no" name="viewport" />
<link href='http://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900' rel='stylesheet' type='text/css'>
<title>Untitled Document</title>

<script type="text/javascript" src="js/scripts.js"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-30263964-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

<script type="text/javascript">
gettasks();
getnextclass();
getmusic();
getreddit();
getlocation();	//Calls weather
</script>
</head>

<body>

<div id="alerts">Loading...</div>
<?php
if(!$client->getAccessToken()) {
  $authUrl = $client->createAuthUrl();
  print "<a class='login' href='$authUrl'>Connect Me to access your Google Calendar and Google Tasks!</a>";
}
?>

<div id="col1">

    <div class="card" id="next_class">
    </div>
    <div class="card" id="weather">
        <div id="weather_main">
            <div id="weather_main_img_stats">
                <img id="weather_main_img" />
                <span id="weather_img_current_cond"></span>
            </div>
            	<div id="weather_main_stats">
                    <span id="weather_main_name_place"> </span>
                    <span id="weather_main_current_temp"></span>
                    <span id="weather_main_windy"> </span>
                    <span id="weather_main_rainy"> </span>
                </div>
                <br class="clr" />
            </div>
            <div id="weather_forecast">
                <span class="forecast_day" id="day_1"></span>
                <span class="forecast_day" id="day_2"></span>
                <span class="forecast_day" id="day_3"></span>
                <span class="forecast_day" id="day_4"></span>
            <br class="clr" />
        </div>
    </div>
</div>

<div id="col2" class="">

    <div class="card" id="tasks">
        <span class="header_tasks">Assignments</span>
        <ul id="assignments">
        </ul>
    </div>
    
    <div class="card" id="music_player">
      
    </div>
    
    <div class="card" id="reddit">
    	<a class="mail_none" href="http://www.reddit.com/message/inbox/" target="_blank"></a>
        <span class="username">fodawim</span>
        <span class="karma">999,999 &bull; 999,999</span>
        <div class="clr"></div>
    </div>
    
</div>

</body>
</html>
<?php
}