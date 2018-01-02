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

/* blog menu order. */
$lang->menuOrder[0]  = 'blog';
$lang->blog->menuOrder[5]  = 'index';
$lang->blog->menuOrder[10] = 'restore';
$lang->blog->menuOrder[15] = 'reportmyteam';
$lang->blog->menuOrder[20] = 'reportproject';
