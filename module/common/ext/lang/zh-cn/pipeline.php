<?php

$lang->menu->pipeline     = 'Pipeline|pipeline|index';

/*oscar:Blog menu*/
$lang->pipeline = new stdclass();
$lang->pipeline->menu = new stdclass();
$lang->pipeline->menu->index        = '首页|pipeline|index';
$lang->pipeline->menu->restore        = '恢复|pipeline|restore';
$lang->pipeline->menu->groupleaders        = '设置组长|pipeline|groupleaders';
$lang->pipeline->menu->autostory        = '设置自动需求关联|pipeline|autostory';


/* pipeline menu order. */
$lang->pipeline->menuOrder[5]  = 'index';
$lang->pipeline->menuOrder[10] = 'restore';
