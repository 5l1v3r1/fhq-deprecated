<?
	include_once "engine/fhq.php";

	$security = new fhq_security();
		
	if(!$security->isLogged())
	{
		refreshTo("index.php");
		return;
	};
	
	$db = new fhq_database();
	
	$content_page = "";
	$number_of_page = 0;
	if(isset($_GET['content_page'])) $content_page = $_GET['content_page'];
	if(isset($_GET['number_of_page'])) $number_of_page = $_GET['number_of_page'];
	
	if($content_page == "quests_all")
	{
		$page = new fhq_page_listofquests('all');
		$page->echo_content($number_of_page);
		exit;
	}
	else if($content_page == "quests_allow")
	{	
		$page = new fhq_page_listofquests('allow');
		$page->echo_content($number_of_page);
		exit;
	}
	else if($content_page == "quests_process")
	{
		$page = new fhq_page_listofquests('process');
		$page->echo_content($number_of_page);
		exit;
	}
	else if($content_page == "quests_completed")
	{
		$page = new fhq_page_listofquests('completed');
		$page->echo_content($number_of_page);
		exit;
	}
	else if($content_page == "feedback_my")
	{
	    $feedback = new fhq_feedback();
	    $feedback->echo_menu();
	    $feedback->echo_list();
		exit;
	}
	else if($content_page == "feedbacks")
	{
	    $feedback = new fhq_feedback();
	    $feedback->echo_list();
		exit;
	}
	else if($content_page == "feedback_add")
	{
		$feedback = new fhq_feedback();
	    $feedback->echo_menu();
	    
	    
	    if( isset($_GET['full_text']) && isset($_GET['feedback_type']) )
		{
			$feedback->full_text = $_GET['full_text'];
			$feedback->type = $_GET['feedback_type'];

			$check = $feedback->check();
			if( strlen($check) == 0 )
			{
				$result = $feedback->add();
				//echo "$result";
				if($result == '1') 
				{
					$feedback->echo_list();
					exit;
				};
			};
			echo 'error: '.$check.'<br>';
		};
		
		if( isset($_GET['answer_text']) 
			&& isset($_GET['feedback_id']) 
			&& isset($_GET['feedback_id_token']) 
		)
		{
			echo $feedback->recvAnswer();
			$feedback->echo_list();
			exit;
	    };
		
	    $feedback->echo_insert_form("?action=feedback_add","POST");
		echo "feedback_add";
		exit;
	}
	else if($content_page == "dr_zoyberg")
	{
		echo '<img src="images/dr_zoyberg.gif"/>';
		exit;
	}
	else if($content_page == "top100")
	{
		echo "not work yet";
		exit;
	}
	else if($content_page == "user_info")
	{
		echo '<pre>
Your name: '.$security->nick().'
Your score: '.$security->score().'
Role: '.$security->role().'
Your place: place / all
</pre>';

		exit;
	}
	else if($content_page == "view_quest")
	{
		echo "not work yet";
		exit;
	}
	else if($content_page == "take_quest")
	{
		echo "not work yet";
		exit;
	}
	else if($content_page == "pass_quest")
	{
		echo "not work yet";
		exit;
	}
	else if($content_page == "add_quest")
	{
		if(!$security->isAdmin())
		{
			echo "Forbidden";
			exit;
		};
		
		$quest =  new fhq_quest();
		
		$quest->setEmptyAll();
		echo $quest->getForm();

		// if($check != "" ) $check = "<font color='#FE2E64'>Uncorrect(!):<br>$check</font><br>";		
		exit;
	}
	else if($content_page == "save_quest")
	{
		if(!$security->isAdmin())
		{
			echo "Forbidden";
			exit;
		};
		
		$quest =  new fhq_quest();
		
		$quest->fillQuestFromGet();
		
		$check = $quest->check();

		if( strlen($check) > 0 )
		{
			echo $quest->getForm();
			echo '<font color="#ff0000">'.$check.'</font>';
			exit;
		}
		
		// insert;
		
		$id = $quest->insert();
		
		if($id == 0)
		{
			echo $quest->getForm();
			echo '<font color="#ff0000">could not inserted</font>';
			exit;
		}
		
		if(!$quest->select($id))
		{
			echo '<font color="#ff0000">Not found quest with id = '.$id.'</font>';
			exit;
		}
		
		$quest->echo_view_quest();
		
		
		//echo "must be insert and redirect to view";
		// 

		
		/*if( isset($_POST['save']) )
		{
			$quest->setQuestName($_POST['quest_name']);
			$quest->setShortText($_POST['short_text']);
			$quest->setFullText($_POST['full_text']);
			$quest->setScore($_POST['score']);
			$quest->setMinScore($_POST['min_score']);
			$quest->setSubject($_POST['subject']);
			$quest->setAnswer($_POST['answer']);
			
			
			$check = $quest->check();

			//good
			if( strlen($check) == 0 )
			{
				$id = $quest->insert($db);
				if( $id != 0 ) refreshTo("main.php?action=quest&id=$id");
			}
		};	*/

		// if($check != "" ) $check = "<font color='#FE2E64'>Uncorrect(!):<br>$check</font><br>";		
		exit;
	}
	else if($content_page == "recalculate_score")
	{
		if(isset($_SESSION['recalculate_score_last_time']))
		{
				$oldtime = $_SESSION['recalculate_score_last_time'];
				$secs = time() - $oldtime;
				if($secs < 60) 
				{
					echo 'wait '.(60 - $secs).' seconds';
					return;
				}
		}
		
		$_SESSION['recalculate_score_last_time'] = time();
		
		$query = '
			SELECT 
				SUM(quest.score) as sum_score 
			FROM 
				userquest 
			INNER JOIN quest ON quest.idquest = userquest.idquest 
			WHERE 
				(userquest.iduser = '.$security->iduser().') 
				AND ( userquest.stopdate <> \'0000-00-00 00:00:00\' );
		';
		
		$result = $db->query( $query );
		$new_score = mysql_result($result, 0, 'sum_score');
		$_SESSION['score'] = $new_score;
		
		$query = 'UPDATE user SET score = '.$new_score.' WHERE iduser = '.$security->iduser();
		$result = $db->query( $query );
		echo $new_score;
		exit;
	}
	else 
	{
		// echo "404 page not found ;)";
		// exit;
	}
	
?>