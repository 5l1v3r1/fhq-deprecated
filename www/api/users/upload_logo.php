<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

/*
 * API_NAME: Upload user logo 
 * API_DESCRIPTION: Method for upload user logo (only POST request with file)
 * API_ACCESS: admin, authorized user
 * API_INPUT: userid - integer, default value: current user
 * API_INPUT: file - file, default value: current user
 * API_OKRESPONSE: { "result":"ok" }
 */

$curdir_upload_logo = dirname(__FILE__);
include_once ($curdir_upload_logo."/../api.lib/api.base.php");
include_once ($curdir_upload_logo."/../../config/config.php");

$result = APIHelpers::startpage($config);

APIHelpers::checkAuth();

$userid = APIHelpers::getParam('userid', APISecurity::userid());
// $userid = intval($userid);
if (!is_numeric($userid))
	APIHelpers::error(400, 'userid must be numeric');

if (!APISecurity::isAdmin() && $userid != APISecurity::userid())
	APIHelpers::error(403, 'you what change logo for another user, it can do only admin');

if (count($_FILES) <= 0)
	APIHelpers::error(404, 'Not found file');

$keys = array_keys($_FILES);

// $prefix = 'quest'.$id.'_';
// $output_dir = 'files/';
for($i = 0; $i < count($keys); $i++)
{
	$filename = $keys[$i];
	if ($_FILES[$filename]['error'] > 0)
	{
		echo "Error: " . $_FILES[$filename]["error"] . "<br>";
	}
	else
	{
		$full_filename = $curdir_upload_logo.'/../../files/users/'.$userid.'_orig.png'; 
		$full_filename_new = $curdir_upload_logo.'/../../files/users/'.$userid.'.png';
		// chmod($curdir_upload_logo.'/../../files/users/',0755);
		
		move_uploaded_file($_FILES[$filename]["tmp_name"],$full_filename);
		if(!file_exists($full_filename))
			APIHelpers::error(404, 'File was not loaded');
		else {
			if(mime_content_type($full_filename) != 'image/png') {
				unlink($full_filename);
				APIHelpers::error(400, 'File are not png-image');
			}
				
			try {
				// set_error_handler("warning_handler", E_WARNING);
				
				// получение нового размера
				list($width, $height) = getimagesize($full_filename);
				$newwidth = 100;
				$newheight = 100;
				$source = imagecreatefrompng($full_filename);
				imagealphablending($source, true);
				imagesavealpha($source, true);
				
				$thumb = imagecreatetruecolor($newwidth, $newheight);
				imagealphablending($thumb, true);
				// imagesavealpha($thumb, true);
				// $black = imagecolorallocate($thumb, 0, 0, 0);
				// imagecolortransparent($thumb, $black);
				imagecolortransparent($thumb);
				
				imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
				imagepng($thumb, $full_filename_new, 9 , PNG_NO_FILTER);
				imagedestroy($thumb);
				imagedestroy($source);

				unlink($full_filename);
			} catch(Exception $e) {
				unlink($full_filename);
				APIHelpers::error(500, 'Problem with convert image: '.$e->getMessage());
			}
		}
	}
}

$conn = APIHelpers::createConnection($config);

try {
	$query = 'UPDATE users SET logo = ? WHERE id = ?';
	$stmt = $conn->prepare($query);
	if ($stmt->execute(array('files/users/'.$userid.'.png', $userid))) {
		$result['result'] = 'ok';
		$result['data']['logo'] = 'files/users/'.$userid.'.png';
	} else
		$result['result'] = 'fail';
} catch(PDOException $e) {
	APIHelpers::error(500, $e->getMessage());
}

echo json_encode($result);
