<?php

define('TABLE_MEETING', 'gamemeeting');


$config->todo = new stdclass();

$config->meeting->assignToGroupName  = 34;

$config->meeting->batchCreate  = 20;

$config->meeting->create = new stdclass();
$config->meeting->edit   = new stdclass();
$config->meeting->dates  = new stdclass();
$config->meeting->times  = new stdclass();
$config->meeting->create->requiredFields = 'desc';
$config->meeting->edit->requiredFields   = 'desc';
$config->meeting->dates->end             = 15;
$config->meeting->times->begin           = 6;
$config->meeting->times->end             = 23;
$config->meeting->times->delta           = 10;

$config->meeting->editor = new stdclass();
$config->meeting->editor->create = array('id' => 'desc', 'tools' => 'simpleTools');
$config->meeting->editor->edit   = array('id' => 'desc', 'tools' => 'simpleTools');

$config->meeting->list = new stdclass();
$config->meeting->list->exportFields            = 'id, desc,pri,assignedTo,deadline,status';
$config->meeting->list->customBatchCreateFields = 'desc,pri,assignedTo,deadline';
$config->meeting->list->customBatchEditFields   = 'desc,pri,assignedTo,deadline,status';

$config->meeting->custom = new stdclass();
$config->meeting->custom->batchCreateFields = 'desc,pri,assignedTo,deadline';
$config->meeting->custom->batchEditFields   = 'desc,pri,assignedTo,deadline,status';
