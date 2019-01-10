<?php

$lang->menu->blog     = 'Blog|blog|index';

/*oscar:Blog menu*/
$lang->blog = new stdclass();
$lang->blog->menu = new stdclass();
$lang->blog->menu->index        = 'Home|blog|index';
$lang->blog->menu->restore        = 'Restore|blog|restore';
$lang->blog->menu->reportmyteam        = 'My Department|blog|reportmyteam';
//$lang->blog->menu->reportmyteam = array('link' => '部门汇总|blog|reportmyteam');
$lang->blog->menu->reportproject        = 'My Project|blog|reportproject';
$lang->blog->menu->searchbydepartment        = 'Search by Department|blog|searchbydepartment';
$lang->blog->menu->view        = 'Search by User|blog|view';

/* blog menu order. */
$lang->blog->menuOrder[5]  = 'index';
$lang->blog->menuOrder[10] = 'restore';
$lang->blog->menuOrder[15] = 'reportmyteam';
$lang->blog->menuOrder[20] = 'reportproject';
$lang->blog->menuOrder[25] = 'searchbydepartment';
$lang->blog->menuOrder[30] = 'view';
