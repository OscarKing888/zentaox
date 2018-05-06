<?php

$lang->menu->gametaskinternal     = '美术内包管理|gametaskinternal|index';

/*oscar:Blog menu*/
$lang->gametaskinternal = new stdclass();
$lang->gametaskinternal->menu = new stdclass();
$lang->gametaskinternal->menu->index        = '首页|gametaskinternal|index';
$lang->gametaskinternal->menu->version        = '版本|gametaskinternal|version';
$lang->gametaskinternal->menu->create        = '添加需求|gametaskinternal|create';
$lang->gametaskinternal->menu->details        = '所有任务|gametaskinternal|details';
$lang->gametaskinternal->menu->mytasks        = '由我负责|gametaskinternal|mytasks';
$lang->gametaskinternal->menu->assignedToMe        = '指派给我|gametaskinternal|assignedtome';
$lang->gametaskinternal->menu->mydept        = '我的部门|gametaskinternal|mydept';
$lang->gametaskinternal->menu->completedlist        = '已完成|gametaskinternal|completedlist';
$lang->gametaskinternal->menu->incompletelist        = '未完成|gametaskinternal|incompletelist';
$lang->gametaskinternal->menu->unassigned        = '未指派|gametaskinternal|unassigned';
//$lang->gametaskinternal->menu->groupleaders        = '设置组长|gametaskinternal|groupleaders';
$lang->gametaskinternal->menu->restore        = '恢复|gametaskinternal|restore';


/* gametaskinternal menu order. */
$lang->menuOrder[2]  = 'gametaskinternal';
$lang->gametaskinternal->menuOrder[5]  = 'index';
$lang->gametaskinternal->menuOrder[10] = 'version';
