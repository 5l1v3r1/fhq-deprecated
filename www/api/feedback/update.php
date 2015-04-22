<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

/*
 * API_NAME: Feedback Update
 * API_DESCRIPTION: Method for change feedback text
 * API_ACCESS: admin only
 * API_INPUT: id - integer, feedback id
 * API_INPUT: type - string, tyep of feedback
 * API_INPUT: text - string, text message
 * API_INPUT: token - string, token
 */

$curdir_events_insert = dirname(__FILE__);
include_once ($curdir_events_insert."/../api.lib/api.helpers.php");
include_once ($curdir_events_insert."/../../config/config.php");
include_once ($curdir_events_insert."/../api.lib/api.base.php");

include_once ($curdir_events_insert."/../api.lib/loadtoken.php");
APIHelpers::checkAuth();

$result = array(
	'result' => 'fail',
	'data' => array(),
);

if(!APISecurity::isAdmin())
  APIHelpers::showerror(1268, 'access denie. you must be admin.');

if (!APIHelpers::issetParam('id'))
  APIHelpers::showerror(1259, 'not found parameter id');

if (!APIHelpers::issetParam('type'))
  APIHelpers::showerror(1260, 'not found parameter type');
  
if (!APIHelpers::issetParam('text'))
  APIHelpers::showerror(1262, 'not found parameter text');

$id = APIHelpers::getParam('id', 0);
$type = APIHelpers::getParam('type', '');
$text = APIHelpers::getParam('text', '');

if (!is_numeric($id))
  APIHelpers::showerror(1261, 'Parameter id must be integer');

$id = intval($id);

$conn = APIHelpers::createConnection($config);

try {
 	$stmt = $conn->prepare('UPDATE feedback SET type = ?, text = ? WHERE id = ?');
 	$stmt->execute(array($type, $text, intval($id)));
 	$result['result'] = 'ok';
} catch(PDOException $e) {
 	APIHelpers::showerror(1263, $e->getMessage());
}

echo json_encode($result);
