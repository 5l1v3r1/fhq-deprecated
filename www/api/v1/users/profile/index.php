<?php
/*
 * API_NAME: Get user info
 * API_DESCRIPTION: Method returned user info
 * API_ACCESS: admin, tester and authorized user
 * API_INPUT: userid - integer, user id
 * API_OKRESPONSE: { "result":"ok" }
 */

$curdir = dirname(__FILE__);
include_once ($curdir."/../../../api.lib/api.base.php");

$response = APIHelpers::startpage();

if(!APIHelpers::is_json_input()){
	APIHelpers::showerror2(2000, 400, "Expected application/json");
}
$conn = APIHelpers::createConnection();
$request = APIHelpers::read_json_input();

$response['profile'] = array();
$response['access'] = array();

/*if (!APIHelpers::issetParam('userid'))
	APIHelpers::showerror(1177, 'Not found parameter userid');*/

$userid = APISecurity::userid();
if(isset($request['userid'])){
	$userid = $request['userid'];
}

if (!is_numeric($userid))
	APIHelpers::showerror2(1181, 400, 'Parameter userid must be integer');

if($userid == 0 && APISecurity::userid() == 0){
	APIHelpers::showerror2(1181, 401, 'Not authorized');
}

$userid = intval($userid);

$bAllow = APISecurity::isAdmin() || APISecurity::isTester() || APISecurity::userid() == $userid;

$response['access']['edit'] = $bAllow;
$response['currentUser'] = APISecurity::userid() == $userid;

$columns = array('id', 'email', 'dt_last_login', 'uuid', 'status', 'role', 'nick', 'logo', 'country', 'region', 'city');

$query = '
		SELECT '.implode(', ', $columns).' FROM
			users
		WHERE id = ?
';

$result['userid'] = $userid;
// $result['query'] = $query;

try {
	$stmt = $conn->prepare($query);
	$stmt->execute(array($userid));
	if ($row = $stmt->fetch()){
		$response['result'] = 'ok';
		$response['data']['userid'] = $row['id'];
		$response['data']['nick'] = $row['nick'];
		$response['data']['logo'] = $row['logo'];
		$response['data']['uuid'] = $row['uuid'];
		$response['data']['dt_last_login'] = $row['dt_last_login'];

		if ($bAllow) {
			 $response['data']['email'] = $row['email'];
			 $response['data']['role'] = $row['role'];
			 $response['data']['status'] = $row['status'];
			 $response['data']['country'] = $row['country'];
			 $response['data']['region'] = $row['region'];
			 $response['data']['city'] = $row['city'];
		}
	}
	
} catch(PDOException $e) {
	APIHelpers::showerror(1184, $e->getMessage());
}

// users_profile
try {
	$stmt = $conn->prepare('SELECT * FROM users_profile WHERE userid = ?');
	$stmt->execute(array($userid));
	while ($row = $stmt->fetch()){
		$response['profile'][$row['name']] = $row['value'];
	}
} catch(PDOException $e) {
	APIHelpers::showerror(1183, $e->getMessage());
}

if(isset($_SESSION['game'])){
	$response['profile']['game'] = $_SESSION['game'];
}else{
	unset($response['profile']['game']);
}

// users_games
try {
	$stmt = $conn->prepare('
		SELECT
			g.id as gameid,
			g.maxscore as game_maxscore,
			g.logo,
			g.title,
			g.type_game,
			ug.score as user_score
		FROM users_games ug
		INNER JOIN games g ON ug.gameid = g.id
		WHERE ug.userid = ?
	');
	$stmt->execute(array($userid));
	while ($row = $stmt->fetch())
	{
		$response['games'][] = array(
			'gameid' => $row['gameid'],
			'title' => $row['title'],
			'type_game' => $row['type_game'],
			'maxscore' => $row['game_maxscore'],
			'logo' => $row['logo'],
			'score' => $row['user_score'],
		);
	}
} catch(PDOException $e) {
	APIHelpers::showerror(1182, $e->getMessage());
}

APIHelpers::endpage($response);