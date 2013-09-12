<?
class fhq_quest
{
	private $quest_name, $short_text, $full_text, $score, $min_score, $subject, $answer, $reply_answer, $idquest;
	private $fields;
	
	function fhq_quest()
	{
	   // $field['idquest'] = new field('idquest', 'idquest', new typefield_int() );
	   // $field['quest_name'] = new field('quest_name','quest_name', new typefield_text() );
	   //    $field['short_text'] = new field('short_text','short_text',);
	   //   $field['full_text'] = new field();
	   //   $field['score'] = new field();
	   //   $field['min_score'] = new field();
	   //   $field['subject'] = new field();
	   //   $field['answer'] = new field();
	   //   $field['reply_answer'] = new field();
	}
	
	//очищаем все переменные
	function setEmptyAll()
	{
		$this->idquest = "";
		$this->quest_name = "";
		$this->short_text = "";
		$this->full_text = "";
		$this->score = "";
		$this->min_score = "";
		$this->subject = "";
		$this->answer = "";
		$this->reply_answer = "";
	}

	function setQuestName( $text ) { $this->quest_name = $text; }
	function setShortText( $text ) { $this->short_text = $text; }
	function setFullText( $text ) { $this->full_text = $text; }
	function setScore( $number ) { $this->score = $number; }
	function setMinScore( $number ) { $this->min_score = $number; }
	function setSubject( $text ) { $this->subject = $text; }
	function setAnswer( $text ) { $this->answer = $text; }
	
	function getQuestName() { return $this->quest_name; }


	function check()
	{
		$check = "";

		if( strlen($this->quest_name) < 3 ) $check .= "length of 'Name' must be more than 3 <br>";
		if( strlen($this->short_text) < 10 ) $check .= "length of 'Short text' must be more than 10 <br>";
		if( strlen($this->full_text) < 20 ) $check .= "length of 'Full Text' must be more than 20 <br>";
		if( strlen($this->score) == 0 ) $check .= " 'Score' is empty <br>";
		if( !is_numeric($this->score) ) $check .= " 'Score' is not numeric <br>";
		if( strlen($this->min_score) == 0 ) $check .= " 'Min Score' is empty <br>";
		if( !is_numeric($this->min_score) ) $check .= " 'Min Score' is not numeric <br>";
		if( strlen($this->subject) < 4 ) $check .= "length of 'Subject' must be more than 4 <br>";
		if( strlen(base64_decode($this->answer)) < 8 ) $check .= "length of 'Answer' must be more than 8 <br>";	
		return $check;
	}

	function insert()
	{
		$security = new fhq_security();
		$db = new fhq_database();
		
		if(strlen($this->check()) != 0) return 0;
		$query = "INSERT INTO quest( name, short_text, text, score, min_score, tema, answer )
			VALUES('".base64_encode($this->quest_name)."',
				'".base64_encode($this->short_text)."',
				'".base64_encode($this->full_text)."',
				".$this->score.",
				".$this->min_score.",
				'".base64_encode($this->subject)."',
				'".base64_encode($this->answer)."') ";
		// echo $query;
		$result = $db->query( $query );
		if( $result == 1 ) 
		{
			$this->idquest = mysql_insert_id();
			return $this->idquest;
		};
		return 0;
	}

	function update( &$db, $idquest )
	{
		if( !is_numeric($idquest) ) return false;

		if(strlen($this->check()) != 0) return false;

		$query = "UPDATE quest SET
			name = '".base64_encode($this->quest_name)."',
			short_text = '".base64_encode($this->short_text)."',
			text = '".base64_encode($this->full_text)."',
			score = ".$this->score.",
			min_score = ".$this->min_score.",
			tema = '".base64_encode($this->subject)."',
			answer = '".base64_encode($this->answer)."'
			WHERE idquest = ".$this->idquest.";";

			//echo $query;

		$result = $db->query( $query );
		return ( $result == 1);
		//	if( $result == 1 ) return true;
		// return false;
	}

	function select( $id )
	{
		$security = new fhq_security();
		$db = new fhq_database();
		
		// echo "id = $id<br>";
		if( !is_numeric($id) ) return false;

		$query = "SELECT * FROM quest WHERE idquest = $id LIMIT 0,1;";
		$result = $db->query( $query );
		// echo $query."<br>";
		if( !$db->count($result) == 1 ) return false;

		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		
		$this->quest_name = base64_decode($row['name']);
		// echo "quest_name: // ".$this->quest_name."<br>";
		$this->short_text = base64_decode($row['short_text']);
		$this->full_text = base64_decode($row['text']);
		$this->score = $row['score'];
		$this->min_score = $row['min_score'];
		$this->subject = base64_decode($row['tema']);
		$this->answer = base64_decode($row['answer']);
		return true;
	}
	
	function delete_quest( &$db, $id )
	{
		$query = "DELETE FROM quest WHERE idquest=$id";
		$result = $db->query($query);
		return ($result == 1);
	}
	
	function fillQuestFromGet()
	{
		$this->quest_name = htmlspecialchars($_GET['quest_name']);
		$this->short_text = htmlspecialchars($_GET['quest_short_text']);
		$this->full_text = htmlspecialchars($_GET['quest_full_text']);
		$this->score = $_GET['quest_score'];
		$this->min_score = $_GET['quest_min_score'];
		$this->subject = htmlspecialchars($_GET['quest_subject']);
		$this->answer = htmlspecialchars($_GET['quest_answer']);
	}
	
	function getForm()
	{
		return '
		<table>
		<tr>
			<td>Name:</td>
			<td><input type="text" id="quest_name" size=30 value="'.$this->quest_name.'"/></td>
		</tr>
		<tr>
			<td>Short Text:</td>
			<td><input type="text" size=30 id="quest_short_text" value="'.$this->short_text.'"/></td>
		</tr>
		<tr>
			<td>Full Text:</td>
			<td><textarea class="full_text" id="quest_full_text">'.$this->full_text.'</textarea></td>
		</tr>

		<tr>
			<td>Score(+):</td>
			<td><input type="text" size=30 id="quest_score" value="'.$this->score.'"/></td>
		</tr>
		<tr>
			<td>Min Score(>):</td>
			<td><input type="text" size=30 id="quest_min_score" value="'.$this->min_score.'"/></td>
		</tr>
		<tr>
			<td>Subject:</td>
			<td><input type="text" size=30 id="quest_subject" value="'.$this->subject.'"/></td>
		</tr>
		<tr>
			<td>Answer:</td>
			<td><input type="text" size=30 id="quest_answer" value="'.$this->answer.'"/></td>
		</tr>
		<tr>
			<td></td>
			<td><br>
			<a href="javascript:void(0);" onclick="
				var quest_name = document.getElementById(\'quest_name\').value;
				var quest_short_text = document.getElementById(\'quest_short_text\').value;
				var quest_full_text = document.getElementById(\'quest_full_text\').value;
				var quest_score = document.getElementById(\'quest_score\').value;
				var quest_min_score = document.getElementById(\'quest_min_score\').value;
				var quest_subject = document.getElementById(\'quest_subject\').value;
				var quest_answer = document.getElementById(\'quest_answer\').value;

				load_content_page(\'save_quest\', {
						\'quest_name\' : quest_name, 
						\'quest_short_text\' : quest_short_text, 
						\'quest_full_text\' : quest_full_text, 
						\'quest_score\' : quest_score, 
						\'quest_min_score\' : quest_min_score, 
						\'quest_subject\' : quest_subject, 
						\'quest_answer\' : quest_answer, 
						\'quest_name\' : quest_name
					});
			">
			Save quest
			</a>
				
			</td>
		</tr>
		<tr>
			<td></td>
			<td><div id="quest_error"></div></td>
		</tr>
		</table>';
	}
	
	function echo_view_quest()
	{
		echo ' 
			<font size=1>Name:</font> <br> #'.$this->idquest.' '.htmlspecialchars_decode($this->quest_name).' <br><br>
			<font size=1>Score:</font> <br> +'.$this->score.' <br><br>
			<font size=1>Subject:</font> <br> '.htmlspecialchars_decode($this->subject).' <br><br>
			<font size=1>Short Text:</font> <br> '.htmlspecialchars_decode($this->short_text).' <br><br>
			<font size=1>Full Text:</font> <br> '.htmlspecialchars_decode($this->full_text).'<br><br>
		';
				 
				 // parse_bb_code($quest_text)
	}
	
};
?>
	