<?php

$lang->menu->blog     = 'Blog|blog|index';

/*oscar:Blog menu*/
$lang->blog = new stdclass();
$lang->blog->menu = new stdclass();
$lang->blog->menu->index        = '首页|blog|index';
$lang->blog->menu->restore        = '恢复|blog|restore';
$lang->blog->menu->reportmyteam        = '部门汇总|blog|reportmyteam';
$lang->blog->menu->reportmyteam = array('link' => '部门汇总|blog|reportmyteam');
$lang->blog->menu->reportproject        = '项目汇总|blog|reportproject';

/* blog menu order. */
$lang->menuOrder[7]  = 'blog';
$lang->blog->menuOrder[5]  = 'index';
$lang->blog->menuOrder[10] = 'restore';
$lang->blog->menuOrder[15] = 'reportmyteam';
$lang->blog->menuOrder[20] = 'reportproject';
