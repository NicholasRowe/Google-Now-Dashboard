<?php

require_once('config.php');

$album = $_REQUEST['album'];
$artist = $_REQUEST['artist'];
$track = $_REQUEST['track'];

$music_data = json_decode(file_get_contents('music.json'));

$album = $music_data->album;
$artist = $music_data->artist;
$track = $music_data->track;


function ordinal_suffix($num){
    if($num < 11 || $num > 13){
         switch($num % 10){
            case 1: return $num.'st';
            case 2: return $num.'nd';
            case 3: return $num.'rd';
        }
    }
    return $num.'th';
}

$url = 'http://ws.audioscrobbler.com/2.0/?method=album.getinfo&api_key='.$last_fm_api_key.'&artist='.urlencode($artist).'&album='.urlencode($album).'&format=json';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_1) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.89 Safari/537.1');
$data = curl_exec($ch);
//	Fuck you Last.fm for putting a # in json
$data = json_decode(str_replace('#text','text',$data));

if($data->album != null)
{
	$album_release = preg_replace('/\s+/', ' ',$data->album->releasedate);

	$c = date_parse_from_format('%d-%M-%Y, %g:%i',$album_release);
	$c = mktime(0,0,0,$c['month'],$c['day'],$c['year']);
	
	$jsonData = array(
		'artwork'=>$data->album->image[3]->text,
		'title'=>$data->album->name,
		'author'=>$data->album->artist,
		'track'=>$track,
		'release'=>'Released '.date('F ',$c).ordinal_suffix(date('j',$c)).date(' Y',$c)
	);
}
else
{
	$jsonData = array(
		'artwork'=>'img/no_art.png',
		'title'=>$album,
		'author'=>$artist,
		'track'=>$track,
		'release'=>''
	);
}


echo 'musicdata('.json_encode($jsonData).');';