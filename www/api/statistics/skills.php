<?php
/*
 * API_NAME: Skills List
 * API_DESCRIPTION: Method will be returned skill list
 * API_ACCESS: authorized users
 * API_INPUT: token - guid, secret token
 * API_INPUT: page - integer, number of page - need for pagging
 * API_INPUT: onpage - integer, records on page - need for pagging
 * API_INPUT: questsubject - string, look in types
 * API_INPUT: username - string, filter by user nick
 * API_INPUT: token - string, token
 */

$curdir_statistics_skills = dirname(__FILE__);
include_once ($curdir_statistics_skills."/../api.lib/api.base.php");
include_once ($curdir_statistics_skills."/../api.lib/api.types.php");
include_once ($curdir_statistics_skills."/../../config/config.php");

$response = APIHelpers::startpage($config);

APIHelpers::checkAuth();

$response['result'] = 'ok';
$questsubject = APIHelpers::getParam('questsubject', '');


$conn = APIHelpers::createConnection($config);

$max_score = array();

foreach (APITypes::$types['questTypes'] as $key => $value) {
	$max_score[APITypes::$types['questTypes'][$key]['value']] = 0;
}


$filter_where = [];
$filter_values = [];

$filter_where[] = 'q.state = ?';
$filter_values[] = 'open';

$filter_where[] = 'q.for_person = ?';
$filter_values[] = 0;

if (APIHelpers::issetParam('subject')) {
	$sub = APIHelpers::getParam('subject', '');
	if ($sub != '') {
		$filter_where[] = 'q.subject = ?';
		$filter_values[] = $sub;
	}
}

$where = implode(' AND ', $filter_where);
if ($where != '') {
	$where = ' AND '.$where;
}

// max score for subjects
try {
	$stmt = $conn->prepare('
			SELECT
				q.subject,
				sum(q.score) as sum_subject
			FROM 
				quest q
			WHERE
				! ISNULL( q.subject )
				'.$where.'
			GROUP BY
				q.subject
	');
	$stmt->execute($filter_values);
	while ($row = $stmt->fetch()) {
		$max_score[$row['subject']] = intval($row['sum_subject']);
	}
} catch(PDOException $e) {
	APIHelpers::showerror(1069, $e->getMessage());
}

foreach ($max_score as $key => $value) {
	if ($value == 0) {
		unset($max_score[$key]);
	}
}

// page
$page = APIHelpers::getParam('page', 0);
if (!is_numeric($page))
	APIHelpers::showerror(1070, 'Parameter "page" must be numeric');
$page = intval($page);
$response['data']['page'] = $page;

// onpage
$onpage = APIHelpers::getParam('onpage', 25);
if (!is_numeric($onpage))
	APIHelpers::showerror(1186, 'parameter "onpage" must be numeric');
$onpage = intval($onpage);
$response['data']['onpage'] = intval($onpage);

$filter_where[] = 'uq.startdate <> ?';
$filter_values[] = '0000-00-00 00:00:00';

$filter_where[] = 'uq.stopdate <> ?';
$filter_values[] = '0000-00-00 00:00:00';

$filter_user_where = [];
$filter_user_values = [];

$filter_user_where[] = 'u.role = ?';
$filter_user_values[] = 'user';

$filter_user_where[] = 'u.status = ?';
$filter_user_values[] = 'activated';

if (APIHelpers::issetParam('user')) {
	$filter_user_where[] = 'u.nick LIKE ?';
	$filter_user_values[] = '%'.APIHelpers::getParam('user', '').'%';
}

$where = implode(' AND ', $filter_where);
if ($where != '') {
	$where = ' AND '.$where;
}

$where_users = implode(' AND ', $filter_user_where);
$filter_userids = "";

try {
	$response['data']['found'] = 0;
	$stmt_count = $conn->prepare('
		SELECT
			count(*) as cnt
		FROM
			users u
		WHERE
			'.$where_users.'
	');
	$stmt_count->execute($filter_user_values);
	if ($row_count = $stmt_count->fetch()) {
		$response['data']['found'] = intval($row_count['cnt']);
	}

	$userids = [];
	$stmt = $conn->prepare('SELECT
				u.id,
				u.nick,
				u.logo
			FROM
				users u
			WHERE
				'.$where_users.'
			LIMIT '.($page*$onpage).','.$onpage.';'
	);
	$stmt->execute($filter_user_values);

	$response['data']['skills'] = array();
	while ($row = $stmt->fetch()) {
		$userid = intval($row['id']);
		$userids[] = 'uq.iduser = '.$userid;
		$response['data']['skills'][$userid] = array(
			'user' => array(
				'userid' => intval($row['id']),
				'nick' => $row['nick'],
				'logo' => $row['logo'],
			),
		);

		foreach ($max_score as $key => $value) {
			$response['data']['skills'][$userid]['subjects'][$key] = array(
				'max' => intval($value),
				'score' => 0
			);
		}
	}
	
	if (count($userids) > 0) {
		$filter_userids = ' AND ('.implode(' OR ', $userids).')';
	}

} catch(PDOException $e) {
	APIHelpers::showerror(1187, $e->getMessage());
}

// count quests
try {
	$stmt = $conn->prepare('
			SELECT
				uq.iduser,
				q.subject,
				SUM( q.score ) as sum_score
			FROM
				userquest uq
			INNER JOIN quest q ON
				uq.idquest = q.idquest
			WHERE
				! ISNULL( q.subject )
				'.$where.'
				'.$filter_userids.'
			GROUP BY 
				uq.iduser, q.subject
	');
	$stmt->execute($filter_values);
	while ($row = $stmt->fetch()) {
		$userid = intval($row['iduser']);
		if (isset($response['data']['skills'][$userid])) {
			$subject = $row['subject'];
			$response['data']['skills'][$userid]['subjects'][$row['subject']]['score'] = intval($row['sum_score']);	
		}
	}
} catch(PDOException $e) {
	APIHelpers::showerror(1189, $e->getMessage());
}

APIHelpers::endpage($response);