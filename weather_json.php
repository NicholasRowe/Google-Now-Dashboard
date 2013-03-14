<?
error_reporting(0);
include('functions.php');
require_once('config.php');
global $set;

$lat = $_REQUEST['lat'];
$lon = $_REQUEST['lon'];

$timezone = get_time_zone($_REQUEST['country'],$_REQUEST['state']);
date_default_timezone_set($timezone);
$zenith = 96;
$tzoffset = date("Z")/60 / 60;

$set = day_or_night($lat,$lon,$tzoffset,$zenith);

$data = get_cached_file('http://api.wunderground.com/api/b2a9cff9e9e6dbcf/conditions/q/'.$lat.','.$lon.'.json');
$data2 = get_cached_file('http://api.wunderground.com/api/b2a9cff9e9e6dbcf/forecast/q/'.$lat.','.$lon.'.json');
$data = json_decode($data);
$data2 = json_decode($data2);


$current = $data->current_observation;

$current_json = array(
				'update_time'=>date('g:i a',$current->observation_epoch),
				'temperature'=>((int)$current->temp_f).'&deg;',
				'wind'=>((int)$current->wind_mph).'mph',
				'weather'=>$current->weather,
				'icon'=>get_icon($current->icon),
				'precip'=>$data2->forecast->simpleforecast->forecastday[0]->pop.'%'
			);
			
			
$forecast_array = array();
foreach($data2->forecast->simpleforecast->forecastday as $forecast)
{
	$data = array(
		'day'=>$forecast->date->weekday_short,
		'high'=>$forecast->high->fahrenheit.'&deg;',
		'low'=>$forecast->low->fahrenheit.'&deg;',
		'icon'=>get_icon($forecast->icon,true),
		'precip'=>$forecast->pop
	);
	
	array_push($forecast_array,$data);
}

$json = 
json_encode(
	array(
		'place'=>$current->display_location->full,
		'current_conditions'=>$current_json,
		'forecast'=>$forecast_array
	)
);


echo 'weather('.$json.');';