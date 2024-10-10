<?php
//$config->pipeline = new stdClass();
$config->pipeline->debug = false;


$config->pipeline->editor = new stdclass();
$config->pipeline->editor->create     = array('id' => 'contentimages', 'tools' => 'pipelineImageTools');
$config->pipeline->editor->edit     = array('id' => 'contentimages', 'tools' => 'pipelineImageTools');
//$config->pipeline->editor->create     = array('id' => 'content', 'tools' => 'pipelineTools');
//$config->pipeline->editor->create     = array('id' => 'contentimages', 'tools' => 'pipelineImageTools');

//$config->pipeline->editor->edit       = array('id' => 'steps,comment', 'tools' => 'bugTools');
//$config->pipeline->editor->view       = array('id' => 'comment,lastComment', 'tools' => 'bugTools');
//$config->pipeline->editor->confirmbug = array('id' => 'comment', 'tools' => 'bugTools');
//$config->pipeline->editor->assignto   = array('id' => 'comment', 'tools' => 'bugTools');
//$config->pipeline->editor->resolve    = array('id' => 'comment', 'tools' => 'bugTools');
//$config->pipeline->editor->close      = array('id' => 'comment', 'tools' => 'bugTools');
//$config->pipeline->editor->activate   = array('id' => 'comment', 'tools' => 'bugTools');

$config->pipeline->defaultStages = 2;

define('TABLE_PIPELINE',                   'gamepipeline');
define('TABLE_PIPELINE_STAGES',           'gamepipelinestages');

define('TABLE_GAMEGROUPLEADERS',                   'gamegroupleaders');



$config->pipeline->fields = 'product,content,contentimages,date';

$config->pipeline->create->requiredFields = 'pipename';
$config->pipeline->imageContentFieldName = "contentimages";
//$config->pipeline->imageContentFieldName = "content";
