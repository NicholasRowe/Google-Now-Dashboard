<?php
require_once('inc/adodb/adodb.inc.php');
require_once('inc/google/Google_Client.php');
require_once('inc/google/contrib/Google_CalendarService.php');
require_once('inc/google/contrib/Google_TasksService.php');

date_default_timezone_set("America/Chicago");

function get_human_time($timestamp,$completed=false)
{
	if($timestamp == '')return 'nope';
	
	$difference = time() - $timestamp;
	$periods = array("second", "minute", "hour", "day", "week", "month", "years", "decade");
	$lengths = array("60","60","24","7","4.35","12","10");
	
	if ($difference < 0) {
		$difference = -$difference;
	}
	else
	{
		return 'Event has passed';
	}
	
	for($j = 0; $difference >= $lengths[$j]; $j++)
		$difference /= $lengths[$j];
		$difference = round($difference);
	if($difference != 1) $periods[$j].= "s";
	
	$text = "In $difference $periods[$j]$ending";
	
	return $text;
}

function get_severity($timestamp)
{
	if($timestamp == '')return 'nope';
	
	$difference = time() - $timestamp;
	
	if ($difference > 0) {
		return 'cc0000';
	}
	else
	{
		$days = round($difference / 60)*-1;
		//Actually hours but meh
		
		if($days > 60)
			return '99cc00';
		elseif($days > 45)
			return 'ccc419';
		elseif($days > 30)
			return 'ffbb33';
		elseif($days > 20)
			return 'ff803b';
		elseif($days > 10)
			return 'ff4444';
		else
			return 'cc0000';
	}
}

//Begin code
$cal = new Google_CalendarService($client);
$tasksService = new Google_TasksService($client);
if (isset($_GET['logout'])) {
  unset($_SESSION['token']);
}

if(isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['token'] = $client->getAccessToken();
  header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if (isset($_SESSION['token'])) {
  $client->setAccessToken($_SESSION['token']);
}

if ($client->getAccessToken()) {
  $calList = $cal->calendarList->listCalendarList();
  
  foreach($calList['items'] as $listed)
  {
	  //	Search for the right calendar name
	  if($listed['summary'] == 'ClassSchedule');
	  $calid = $listed['id'];
  }
  if(!isset($calid))
  	$calid = $calList['items'][1]['id'];
  
  
	$data = $cal->events->listEvents($calList['items'][2]['id'],array('timeMin'=>date(DateTime::ATOM),'maxResults'=>2,'orderBy'=>'startTime','singleEvents'=>true));
  
  	$c = strptime($data['items'][0]['start']['dateTime'], '%Y-%m-%dT%H:%M:%S');
	$c = mktime($c['tm_hour'], $c['tm_min'], $c['tm_sec'], $c['tm_mon']+1, $c['tm_mday'], $c['tm_year']+1900);
	
	if($c < time())
		$data = $data['items'][1];
  	else
		$data = $data['items'][0];
  
  	$a = strptime($data['start']['dateTime'], '%Y-%m-%dT%H:%M:%S');
	$a = mktime($a['tm_hour'], $a['tm_min'], $a['tm_sec'], $a['tm_mon']+1, $a['tm_mday'], $a['tm_year']+1900);
	
	$b = strptime($data['end']['dateTime'], '%Y-%m-%dT%H:%M:%S');
	$b = mktime($b['tm_hour'], $b['tm_min'], $b['tm_sec'], $b['tm_mon']+1, $b['tm_mday'], $b['tm_year']+1900);
  
  	$location = $data['location'];
	if($location == '')
		$location = 'No Location Set';
		
	
	if($data['summary'] == '')
		$json = array(
			'title'=>'No Upcoming Class',
			'time'=>'--:--',
			'countdown'=>'&nbsp;',
			'end'=>'&nbsp;',
			'location'=>'&nbsp;',
			'severity'=>'&nbsp;'
		);
	else
		$json = array(
			'title'=>$data['summary'],
			'time'=>date("h:i",$a),
			'countdown'=>get_human_time($a),
			'end'=>get_human_time($b),
			'location'=>$location,
			'severity'=>get_severity($a)
		);
  
 echo 'nextclass('.json_encode($json).');';


$_SESSION['token'] = $client->getAccessToken();
} else {
  $authUrl = $client->createAuthUrl();
  print "<a class='login' href='$authUrl'>Connect Me!</a>";
}