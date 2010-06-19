<?php

require_once("config.php");
require_once(WWW_DIR."/lib/adminpage.php");
require_once(WWW_DIR."/lib/movie.php");

$page = new AdminPage();
$movie = new Movie();
$id = 0;

// set the current action
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';

if (isset($_REQUEST["id"]))
{
	$id = $_REQUEST["id"];
	$mov = $movie->getMovieInfo($id);
	
	if (!$mov) {
		$page->show404();
	}
	
	switch($action) 
	{
	    case 'submit':
	    	$coverLoc = WWW_DIR."images/covers/".$id.'-cover.jpg';
	    	$backdropLoc = WWW_DIR."images/covers/".$id.'-backdrop.jpg';
	    	
			if($_FILES['cover']['size'] > 0)
			{
				$tmpName = $_FILES['cover']['tmp_name'];
				$file_info = getimagesize($tmpName);
				if(!empty($file_info))
				{
					move_uploaded_file($_FILES['cover']['tmp_name'], $coverLoc);
				}
			}
			
			if($_FILES['backdrop']['size'] > 0)
			{
				$tmpName = $_FILES['backdrop']['tmp_name'];
				$file_info = getimagesize($tmpName);
				if(!empty($file_info))
				{
					move_uploaded_file($_FILES['backdrop']['tmp_name'], $backdropLoc);	
				}
			}
			
			$_POST['cover'] = (file_exists($coverLoc)) ? 1 : 0;
			$_POST['backdrop'] = (file_exists($backdropLoc)) ? 1 : 0;
			
			$movie->update($id, $_POST["title"], $_POST["plot"], $_POST["year"], $_POST["rating"], $_POST["genre"], $_POST["cover"], $_POST['backdrop']);
			
			header("Location:".WWW_TOP."/movie-list.php");
	        die();
	    break;
	    case 'view':
	    default:				
			$page->title = "Movie Edit";
			$page->smarty->assign('movie', $mov);
		break;   
	}
}

$page->content = $page->smarty->fetch('admin/movie-edit.tpl');
$page->render();

?>