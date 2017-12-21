<?php
//$config->blog = new stdClass();
$config->blog->debug = false;


$config->blog->editor = new stdclass();
$config->blog->editor->create     = array('id' => 'contentimages', 'tools' => 'blogImageTools');
$config->blog->editor->edit     = array('id' => 'contentimages', 'tools' => 'blogImageTools');
//$config->blog->editor->create     = array('id' => 'content', 'tools' => 'blogTools');
//$config->blog->editor->create     = array('id' => 'contentimages', 'tools' => 'blogImageTools');

//$config->blog->editor->edit       = array('id' => 'steps,comment', 'tools' => 'bugTools');
//$config->blog->editor->view       = array('id' => 'comment,lastComment', 'tools' => 'bugTools');
//$config->blog->editor->confirmbug = array('id' => 'comment', 'tools' => 'bugTools');
//$config->blog->editor->assignto   = array('id' => 'comment', 'tools' => 'bugTools');
//$config->blog->editor->resolve    = array('id' => 'comment', 'tools' => 'bugTools');
//$config->blog->editor->close      = array('id' => 'comment', 'tools' => 'bugTools');
//$config->blog->editor->activate   = array('id' => 'comment', 'tools' => 'bugTools');

$config->blog->dbname = 'gameblog';
$config->blog->fields = 'product,content,contentimages,date';
$config->blog->imageContentFieldName = "contentimages";
//$config->blog->imageContentFieldName = "content";
