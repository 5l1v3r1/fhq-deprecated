<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

/*
 * API_NAME: Update LastEventID
 * API_DESCRIPTION: Method for update user profile
 * API_ACCESS: authorized users
 * API_INPUT: id - integer, Identificator of last event
 * API_OKRESPONSE: { "result":"ok" }
 */


$curdir = dirname(__FILE__);
include_once ($curdir."/../api.lib/api.base.php");
include_once ($curdir."/../../config/config.php");

APIHelpers::checkAuth();

// TODO only for admins
// really ???

$result = array(
	'result' => 'fail',
	'data' => array(),
);

$result['result'] = 'ok';

$conn = APIHelpers::createConnection($config);

$country = '';
$city = '';

if (!APIHelpers::issetParam('id'))
  APIHelpers::showerror(1202, 'Not found parameter "id"');

$id = APIHelpers::getParam('id', 0);

if (!is_numeric($id))
  APIHelpers::showerror(1203, 'id must be integer');

try {
	$_SESSION['user']['profile']['lasteventid'] = $id; // todo must be renamed to lasteventid!

	$query = 'UPDATE users_profile SET value = ?, date_change = NOW() WHERE name = ? AND userid = ?';
	$stmt = $conn->prepare($query);
	$stmt->execute(array("" + $id, 'lasteventid', APISecurity::userid()));
	$result['data']['lasteventid'] = $id;
	$result['data']['userid'] = APISecurity::userid();
	$result['result'] = 'ok';
} catch(PDOException $e) {
	APIHelpers::showerror(1204, $e->getMessage());
}

echo json_encode($result);