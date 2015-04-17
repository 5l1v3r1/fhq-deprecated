<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

/*
 * API_NAME: Enums
 * API_DESCRIPTION: list of some types, for example:
 * API_DESCRIPTION: questTypes, questStates, userStatuses and etc.
 * API_ACCESS: all
 */


$result = array(
	'result' => 'ok',
	'data' => array(
		'questTypes' => array(
			array( 'value' => 'hashes', 'caption' => 'Hashes' ),
			array( 'value' => 'stego', 'caption' => 'Stego' ),
			array( 'value' => 'reverse', 'caption' => 'Reverse' ),
			array( 'value' => 'recon', 'caption' => 'Recon' ),
			array( 'value' => 'trivia', 'caption' => 'Trivia' ),
			array( 'value' => 'crypto', 'caption' => 'Crypto' ),
			array( 'value' => 'forensics', 'caption' => 'Forensics' ),
			array( 'value' => 'network', 'caption' => 'Network' ),
			array( 'value' => 'web', 'caption' => 'Web' ),
			array( 'value' => 'ppc', 'caption' => 'PPC' ),
			array( 'value' => 'admin', 'caption' => 'Admin' ),
			array( 'value' => 'enjoy', 'caption' => 'Enjoy' ),
			array( 'value' => 'unknown', 'caption' => 'Unknown' ),
		),
		'questTypesFilter' => array(
			array( 'value' => '', 'caption' => '*' ),
			array( 'value' => 'hashes', 'caption' => 'Hashes' ),
			array( 'value' => 'stego', 'caption' => 'Stego' ),
			array( 'value' => 'reverse', 'caption' => 'Reverse' ),
			array( 'value' => 'recon', 'caption' => 'Recon' ),
			array( 'value' => 'trivia', 'caption' => 'Trivia' ),
			array( 'value' => 'crypto', 'caption' => 'Crypto' ),
			array( 'value' => 'forensics', 'caption' => 'Forensics' ),
			array( 'value' => 'network', 'caption' => 'Network' ),
			array( 'value' => 'web', 'caption' => 'Web' ),
			array( 'value' => 'ppc', 'caption' => 'PPC' ),
			array( 'value' => 'admin', 'caption' => 'Admin' ),
			array( 'value' => 'enjoy', 'caption' => 'Enjoy' ),
			array( 'value' => 'unknown', 'caption' => 'Unknown' ),
		),		
		'questStates' => array(
			array( 'value' => 'open', 'caption' => 'Open' ),
			array( 'value' => 'closed', 'caption' => 'Closed' ),
			array( 'value' => 'broken', 'caption' => 'Broken' ),
		),
		'gameTypes' => array(
			array( 'value' => 'jeopardy', 'caption' => 'Jeopardy' ),
			//array( 'value' => 'attack-defence', 'caption' => 'Attack-Defence' ),
		),
		'gameForms' => array(
			array( 'value' => 'online', 'caption' => 'Online' ),
			array( 'value' => 'offline', 'caption' => 'Offline' ),
		),
		'gameStates' => array(
			array( 'value' => 'original', 'caption' => 'Original' ),
			array( 'value' => 'copy', 'caption' => 'Copy' ),
			array( 'value' => 'unlicensed-copy', 'caption' => 'Unlicensed copy' ),
		),
		'eventTypes' => array(
			array( 'value' => 'info', 'caption' => 'Information' ),
			array( 'value' => 'users', 'caption' => 'Users' ),
			array( 'value' => 'games', 'caption' => 'Games' ),
			array( 'value' => 'quests', 'caption' => 'Quests' ),
			array( 'value' => 'warning', 'caption' => 'Warning' ),
		),
		'userRoles' => array(
			array( 'value' => 'user', 'caption' => 'User' ),
			array( 'value' => 'tester', 'caption' => 'Tester' ),
			array( 'value' => 'admin', 'caption' => 'Admin' ),
		),
		'userRolesFilter' => array(
			array( 'value' => '', 'caption' => '*' ),
			array( 'value' => 'user', 'caption' => 'User' ),
			array( 'value' => 'tester', 'caption' => 'Tester' ),
			array( 'value' => 'admin', 'caption' => 'Admin' ),
		),
		'userStatuses' => array(
			array( 'value' => 'activated', 'caption' => 'Activated' ),
			array( 'value' => 'blocked', 'caption' => 'Blocked' ),
		),
		'userStatusesFilter' => array(
			array( 'value' => '', 'caption' => '*' ),
			array( 'value' => 'activated', 'caption' => 'Activated' ),
			array( 'value' => 'blocked', 'caption' => 'Blocked' ),
		),
		'onpage' => array(
			array( 'value' => '5', 'caption' => '5' ),
			array( 'value' => '10', 'caption' => '10' ),
			array( 'value' => '15', 'caption' => '15' ),
			array( 'value' => '20', 'caption' => '20' ),
			array( 'value' => '25', 'caption' => '25' ),
			array( 'value' => '30', 'caption' => '30' ),
			array( 'value' => '50', 'caption' => '50' ),
		),
		'styles' => array(
			array( 'value' => 'base', 'caption' => 'Light' ),
			array( 'value' => 'dark', 'caption' => 'Nigth' ),
			array( 'value' => 'yellow', 'caption' => 'Yellow (not completed)' ),
			array( 'value' => 'red', 'caption' => 'Red (not completed)' ),
		),
		'feedbackTypes' => array(
			array( 'value' => 'complaint', 'caption' => 'Complaint (Жалоба)' ),
			array( 'value' => 'defect', 'caption' => 'Defect (Недочет)' ),
			array( 'value' => 'error', 'caption' => 'Error (Ошибка)' ),
			array( 'value' => 'approval', 'caption' => 'Approval (Одобрение)' ),
			array( 'value' => 'proposal', 'caption' => 'Proposal (Предложение)' ),
		),
		'answerlistTable' => array(
			array( 'value' => 'active', 'caption' => 'Active' ),
			array( 'value' => 'backup', 'caption' => 'Backup' ),
		),
		'answerlistPassedFilter' => array(
			array( 'value' => '', 'caption' => '*' ),
			array( 'value' => 'Yes', 'caption' => 'Yes' ),
			array( 'value' => 'No', 'caption' => 'No' ),
		),
	),
);

echo json_encode($result);
