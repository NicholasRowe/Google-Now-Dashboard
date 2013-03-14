<?
include('config.php');
$data = json_decode(file_get_contents('http://www.reddit.com/user/'.$reddit_username.'.json'));

echo '<br /><pre>'.print_r($data,true).'</pre>';