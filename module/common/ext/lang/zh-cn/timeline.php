<?php

$lang->menu->timeline     = 'Timeline|timeline|index';

/*oscar:timeline menu*/
$lang->timeline = new stdclass();
$lang->timeline->menu = new stdclass();
$lang->timeline->menu->index        = '首页|timeline|index';
$lang->timeline->menu->restore        = '恢复|timeline|restore';
$lang->timeline->menu->view        = '详细|timeline|view';

/* timeline menu order. */
$lang->menuOrder[4]  = 'timeline';
$lang->timeline->menuOrder[5]  = 'index';
$lang->timeline->menuOrder[30] = 'view';
$lang->timeline->menuOrder[40] = 'restore';