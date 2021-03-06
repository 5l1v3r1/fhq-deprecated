<?php
/*
 * API_NAME: Get Game Info
 * API_DESCRIPTION: Mthod returned information about game
 * API_ACCESS: all
 * API_INPUT: token - guid, token
 * API_INPUT: gameid - integer, Identificator of the game (defualt current id)
 */

$curdir_games_get = dirname(__FILE__);
include_once ($curdir_games_get."/../api.lib/api.base.php");
include_once ($curdir_games_get."/../../config/config.php");

$response = APIHelpers::startpage($config);

$conn = APIHelpers::createConnection($config);

$gameid = APIHelpers::getParam('gameid', 0);
$response['access']['edit'] = APISecurity::isAdmin();

if (!is_numeric($gameid))
	APIHelpers::error(400, '"gameid" must be numeric');

$gameid = intval($gameid);

try {

	$query = '
		SELECT *
		FROM
			games
		WHERE id = ?';

	$columns = array(
		'id',
		'type_game',
		'state',
		'form',
		'title',
		'date_start',
		'date_stop',
		'date_restart',
		'description',
		'logo',
		'owner',
		'organizators',
		'rules',
		'maxscore'
	);

	$stmt = $conn->prepare($query);
	$stmt->execute(array(intval($gameid)));
	if($row = $stmt->fetch())
	{
		$response['data'] = array();
		foreach ( $columns as $k) {
			$response['data'][$k] = $row[$k];
		}
		$response['result'] = 'ok';
	} else {
		APIHelpers::error(404, 'Does not found game with this id');
	}
} catch(PDOException $e) {
	APIHelpers::error(500, $e->getMessage());
}

APIHelpers::endpage($response);
