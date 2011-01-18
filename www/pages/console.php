<?php
require_once(WWW_DIR."/lib/console.php");
require_once(WWW_DIR."/lib/category.php");

$console = new Console();
$cat = new Category;

if (!$users->isLoggedIn())
	$page->show403();


$concats = $cat->getChildren(Category::CAT_PARENT_GAME);
$ctmp = array();
foreach($concats as $ccat) {
	$ctmp[$ccat['ID']] = $ccat;
}
$category = Category::CAT_PARENT_GAME;
if (isset($_REQUEST["t"]) && array_key_exists($_REQUEST['t'], $ctmp))
	$category = $_REQUEST["t"] + 0;
	
$catarray = array();
$catarray[] = $category;	

$page->smarty->assign('catlist', $ctmp);
$page->smarty->assign('category', $category);

$browsecount = $console->getConsoleCount($catarray, -1, $page->userdata["categoryexclusions"]);

$offset = (isset($_REQUEST["offset"]) && ctype_digit($_REQUEST['offset'])) ? $_REQUEST["offset"] : 0;
$ordering = $console->getConsoleOrdering();
$orderby = isset($_REQUEST["ob"]) && in_array($_REQUEST['ob'], $ordering) ? $_REQUEST["ob"] : '';

$results = $consoles = array();
$results = $console->getConsoleRange($catarray, $offset, ITEMS_PER_PAGE, $orderby, -1, $page->userdata["categoryexclusions"]);
foreach($results as $result) {	
	$consoles[] = $result;
}

$platform = (isset($_REQUEST['platform']) && !empty($_REQUEST['platform'])) ? stripslashes($_REQUEST['platform']) : '';
$page->smarty->assign('platform', $platform);

$title = (isset($_REQUEST['title']) && !empty($_REQUEST['title'])) ? stripslashes($_REQUEST['title']) : '';
$page->smarty->assign('title', $title);

$browseby_link = '&amp;title='.$title.'&amp;platform='.$platform;

$page->smarty->assign('pagertotalitems',$browsecount);
$page->smarty->assign('pageroffset',$offset);
$page->smarty->assign('pageritemsperpage',ITEMS_PER_PAGE);
$page->smarty->assign('pagerquerybase', WWW_TOP."/console?t=".$category.$browseby_link."&amp;ob=".$orderby."&amp;offset=");
$page->smarty->assign('pagerquerysuffix', "#results");

$pager = $page->smarty->fetch($page->getCommonTemplate("pager.tpl"));
$page->smarty->assign('pager', $pager);

if ($category == -1)
	$page->smarty->assign("catname","All");			
else
{
	$cat = new Category();
	$cdata = $cat->getById($category);
	if ($cdata)
		$page->smarty->assign('catname',$cdata["title"]);			
	else
		$page->show404();
}

foreach($ordering as $ordertype) 
	$page->smarty->assign('orderby'.$ordertype, WWW_TOP."/console?t=".$category.$browseby_link."&amp;ob=".$ordertype."&amp;offset=0");

$page->smarty->assign('results',$consoles);		

$page->meta_title = "Browse Console";
$page->meta_keywords = "browse,nzb,console,games,description,details";
$page->meta_description = "Browse for Games";
	
$page->content = $page->smarty->fetch('console.tpl');
$page->render();

?>