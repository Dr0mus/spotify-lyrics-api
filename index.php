<?php
require 'spotify.php';
error_reporting(0);
header("Content-Type: application/json");

$trackid = $_GET['trackid'];
$typed = $_GET['format'];

if (! $trackid) {
	http_response_code(400);
	$reponse = json_encode(["error" => true, "message" => "trackid parameter is required!"]);
	echo $reponse;
	return;
}
$spotify = new Spotify();
$spotify -> check_if_expire();
$reponse = $spotify -> get_lyrics(track_id: $trackid);
echo make_reponse($reponse, $typed);

function make_reponse($response, $format)
{	
	$temp = json_decode($response, true)['lyrics'];
	if ($format == 'lrc') {
		$lines = array();
		foreach ($temp['lines'] as $lists) {
			$lrctime = formatMS($lists['startTimeMs']);
			array_push($lines, ["timeTag" => $lrctime, "words" => $lists['words']]);
		}
		$response = ["error" => false, "syncType" => $temp["syncType"], "lines" => $lines];
	}
	else {
		$response = ["error" => false, "syncType" => $temp["syncType"], "lines" => $temp["lines"]];
	}
	return json_encode($response);
}

function formatMS($milliseconds) {
	$seconds = floor($milliseconds / 1000);
    $minutes = floor($seconds / 60);
    $centi = $milliseconds % 1000;
    $seconds = $seconds % 60;
    $minutes = $minutes % 60;
    $format = '%02u:%02u.%02u';
    $time = sprintf($format, $minutes, $seconds, $centi);
    return rtrim($time, '0');
}
?>