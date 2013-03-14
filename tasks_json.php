<?
require_once('inc/adodb/adodb.inc.php');
require_once('inc/google/Google_Client.php');
require_once('inc/google/contrib/Google_TasksService.php');
require_once('inc/google/contrib/Google_CalendarService.php');
require_once('config.php');

function get_human_time($timestamp,$completed=false)
{
	if($timestamp == '')return;
	
	$difference = time() - $timestamp;
	$periods = array("second", "minute", "hour", "day", "week", "month", "years", "decade");
	$lengths = array("60","60","24","7","4.35","12","10");
	
	if ($difference > 0) { // this was in the past
		$ending = " ago";
		$pre_ending = "";
	} else { // this was in the future
		$difference = -$difference;
		$pre_ending = "in ";
	}
	for($j = 0; $difference >= $lengths[$j]; $j++)
		$difference /= $lengths[$j];
		$difference = round($difference);
	if($difference != 1) $periods[$j].= "s";
	
	if($completed)
		$text = "Completed $pre_ending$difference $periods[$j]$ending";
	else
		$text = "Due $pre_ending$difference $periods[$j]$ending";
	
	return $text;
}

function get_severity($timestamp)
{
	if($timestamp == '')return;
	
	$difference = time() - $timestamp;
	
	if ($difference > 0) {
		return 'cc0000';
	}
	else
	{
		$days = round($difference / 86400)*-1;
		
		if($days > 30)
			return '99cc00';
		elseif($days > 25)
			return 'ccc419';
		elseif($days > 20)
			return 'ffbb33';
		elseif($days > 15)
			return 'ff803b';
		elseif($days > 5)
			return 'ff4444';
		elseif($days < 5)
			return 'cc0000';
		else
			return 'cc0000';
	}
}

//Begin code
$cal = new Google_CalendarService($client);
$tasksService = new Google_TasksService($client);

if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['token'] = $client->getAccessToken();
  header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if (isset($_SESSION['token'])) {
  $client->setAccessToken($_SESSION['token']);
}

if($_GET['change'])
{
	
}
else
{
	$lists = $tasksService->tasklists->listTasklists();
	foreach($lists['items'] as $list)
	{
		//	Find proper list by name
		if($list['title'] == 'Assignments')
			$assignments = $list;
	}
	
	if($assignments == null)
	{
		echo 'tasks('.json_encode(array('empty'=>true)).')';
		die();
	}
	
	
	$tasks = $tasksService->tasks->listTasks($assignments['id']);
	$taskArray = array();
	
	if(count($tasks['items'])>0)
	{
		foreach($tasks['items'] as $task)
		{
			$notes = ''.$task['notes'];
			$date = $task['due'];
			$completed = false;
			if(isset($task['completed']))
			{
				$completed = true;
				$date = $task['completed'];
			}
			$a = strptime($date, '%Y-%m-%dT%H:%M:%S');
			$a = mktime($a['tm_hour'], $a['tm_minute'], $a['tm_sec'], $a['tm_mon']+1, $a['tm_mday'], $a['tm_year']+1900);
			$data = array(
				'title'=>$task['title'],
				'notes'=>$notes,
				'duedate'=>get_human_time($a,$completed),
				'status'=>$task['status'],
				'background'=>get_severity($a)
			);
			array_push($taskArray,$data);
		}
	}
	else
	{
		echo 'tasks('.json_encode(array('empty'=>true)).')';
		die();
	}
	
	echo 'tasks('.json_encode($taskArray).');';
}