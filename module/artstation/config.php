<?php
$config->artstation = new stdClass();
$config->artstation->debug = false;


$config->artstation->editor = new stdclass();
$config->artstation->editor->create     = array('id' => 'contentimages', 'tools' => 'blogImageTools');
$config->artstation->editor->edit     = array('id' => 'contentimages', 'tools' => 'blogImageTools');
//$config->artstation->editor->create     = array('id' => 'content', 'tools' => 'artstationTools');
//$config->artstation->editor->create     = array('id' => 'contentimages', 'tools' => 'artstationImageTools');

//$config->artstation->editor->edit       = array('id' => 'steps,comment', 'tools' => 'bugTools');
//$config->artstation->editor->view       = array('id' => 'comment,lastComment', 'tools' => 'bugTools');
//$config->artstation->editor->confirmbug = array('id' => 'comment', 'tools' => 'bugTools');
//$config->artstation->editor->assignto   = array('id' => 'comment', 'tools' => 'bugTools');
//$config->artstation->editor->resolve    = array('id' => 'comment', 'tools' => 'bugTools');
//$config->artstation->editor->close      = array('id' => 'comment', 'tools' => 'bugTools');
//$config->artstation->editor->activate   = array('id' => 'comment', 'tools' => 'bugTools');

define('TABLE_ARTSTATION',                   'artstation');
define('TABLE_ARTSTATION_LIKE',              'artstationlike');
define('TABLE_ARTSTATION_COMMENT',           'artstationcomment');

$config->artstation->fields = 'product,type,title,tags,content,requirement';
$config->artstation->commentFields = 'imageid,content';
$config->artstation->imageContentFieldName = "contentimages";
//$config->artstation->imageContentFieldName = "content";

$config->artistation->leadartist = 'dengdapeng';