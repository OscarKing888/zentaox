<?php
//$config->timeline = new stdClass();
$config->timeline->debug = false;


$config->timeline->editor = new stdclass();
$config->timeline->editor->create     = array('id' => 'contentimages', 'tools' => 'blogImageTools');
$config->timeline->editor->edit     = array('id' => 'contentimages', 'tools' => 'blogImageTools');

define('TABLE_TIMELINE',                   'timeline');

$config->timeline->fields = 'product,content,contentimages,date';
$config->timeline->imageContentFieldName = "contentimages";
//$config->timeline->imageContentFieldName = "content";
