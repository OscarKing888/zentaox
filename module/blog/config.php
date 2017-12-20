<?php
//$config->blog = new stdClass();
$config->debug = true;

$config->blog->batchCreate = 10;
$config->blog->longlife    = 7;

$config->blog->create  = new stdclass();
$config->blog->restore = new stdclass();



$config->blog->editor = new stdclass();
$config->blog->editor->create     = array('id' => 'content', 'tools' => 'blogTools');

//$config->blog->editor->edit       = array('id' => 'steps,comment', 'tools' => 'bugTools');
//$config->blog->editor->view       = array('id' => 'comment,lastComment', 'tools' => 'bugTools');
//$config->blog->editor->confirmbug = array('id' => 'comment', 'tools' => 'bugTools');
//$config->blog->editor->assignto   = array('id' => 'comment', 'tools' => 'bugTools');
//$config->blog->editor->resolve    = array('id' => 'comment', 'tools' => 'bugTools');
//$config->blog->editor->close      = array('id' => 'comment', 'tools' => 'bugTools');
//$config->blog->editor->activate   = array('id' => 'comment', 'tools' => 'bugTools');

