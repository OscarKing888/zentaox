<?php
$config->testtask = new stdclass();
$config->testtask->create = new stdclass();
$config->testtask->edit   = new stdclass();
$config->testtask->create->requiredFields = 'project,build,begin,end,name';
$config->testtask->edit->requiredFields   = 'project,build,begin,end,name';

$config->testtask->editor = new stdclass();
$config->testtask->editor->create  = array('id' => 'desc', 'tools' => 'simpleTools');
$config->testtask->editor->edit    = array('id' => 'desc,report', 'tools' => 'simpleTools');
$config->testtask->editor->view    = array('id' => 'lastComment', 'tools' => 'simpleTools');
$config->testtask->editor->start   = array('id' => 'comment', 'tools' => 'simpleTools');
$config->testtask->editor->close   = array('id' => 'report,comment', 'tools' => 'simpleTools');
$config->testtask->editor->block   = array('id' => 'comment', 'tools' => 'simpleTools');
$config->testtask->editor->activate= array('id' => 'comment', 'tools' => 'simpleTools');

$config->testtask->datatable = new stdclass();
$config->testtask->datatable->defaultField = array('id', 'pri', 'title', 'type', 'assignedTo', 'lastRunner', 'lastRunDate', 'lastRunResult', 'status', 'bugs', 'results', 'stepNumber','actions');




define('TABLE_TESTTASKS',                   'test_tasks');
define('TABLE_TESTTASKSTORIES',             'test_task_stories');