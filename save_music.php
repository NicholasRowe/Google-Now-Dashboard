<?



print_r($_REQUEST);

$data = array(
	'artist'=>$_GET['artist'],
	'album'=>$_GET['album'],
	'track'=>$_GET['track'],
);

$fp = fopen('music.json', 'w');
fwrite($fp,json_encode($data));
fclose($fp);