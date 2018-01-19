<?php

$config->gametaskinternal = new stdclass();
$config->gametaskinternal->batchCreate = 10;
$config->gametaskinternal->debug = false;


$config->gametaskinternal->editor = new stdclass();
$config->gametaskinternal->editor->create     = array('id' => 'contentimages', 'tools' => 'pipelineImageTools');
$config->gametaskinternal->editor->edit     = array('id' => 'contentimages', 'tools' => 'pipelineImageTools');



$config->gametaskinternal->defaultProject = 3;

define('TABLE_GAMETASKINTERNAL',                   'gametaskinternal');
define('TABLE_GAMETASKINTERNALVERSION',           'gametaskinternalversion');
define('TABLE_GAMEGROUPLEADERS',                   'gamegroupleaders');

define('GROUPNAME_CQYH',                            '传奇永恒');


$config->gametaskinternal->create->requiredFields      = 'version,dept,owner,title';