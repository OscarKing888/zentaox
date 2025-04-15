<?php

$lang->menu->blog     = 'Blog|blog|index';

/*oscar:Blog menu*/
$lang->blog = new stdclass();
$lang->blog->menu = new stdclass();
$lang->blog->menu->index        = '首页|blog|index';
$lang->blog->menu->restore        = '恢复|blog|restore';
$lang->blog->menu->reportmyteam        = '部门汇总|blog|reportmyteam';
//$lang->blog->menu->reportmyteam = array('link' => '部门汇总|blog|reportmyteam');
$lang->blog->menu->reportproject        = '项目汇总|blog|reportproject';
$lang->blog->menu->searchbydepartment        = '部门查询|blog|searchbydepartment';
$lang->blog->menu->view        = '用户查询|blog|view';

/* blog menu order. */
$lang->blog->menuOrder[5]  = 'index';
$lang->blog->menuOrder[10] = 'restore';
$lang->blog->menuOrder[15] = 'reportmyteam';
$lang->blog->menuOrder[20] = 'reportproject';
$lang->blog->menuOrder[25] = 'searchbydepartment';
$lang->blog->menuOrder[30] = 'view';