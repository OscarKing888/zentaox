<?php
$config->task = new stdclass();
$config->task->batchCreate = 10;

$config->task->create   = new stdclass();
$config->task->edit     = new stdclass();
$config->task->start    = new stdclass();
$config->task->finish   = new stdclass();
$config->task->activate = new stdclass();

$config->task->create->requiredFields      = 'name,deadline,dept';
$config->task->batchCreate->requiredFields      = 'name,assignedTo';
$config->task->edit->requiredFields        = $config->task->create->requiredFields;
$config->task->finish->requiredFields      = 'consumed';
$config->task->activate->requiredFields    = 'left';

$config->task->editor = new stdclass();
$config->task->editor->create   = array('id' => 'desc', 'tools' => 'simpleTools');
$config->task->editor->edit     = array('id' => 'desc,comment', 'tools' => 'simpleTools');
$config->task->editor->view     = array('id' => 'comment,lastComment', 'tools' => 'simpleTools');
$config->task->editor->assignto = array('id' => 'comment', 'tools' => 'simpleTools');
$config->task->editor->start    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->task->editor->restart  = array('id' => 'comment', 'tools' => 'simpleTools');
$config->task->editor->finish   = array('id' => 'comment', 'tools' => 'simpleTools');
$config->task->editor->close    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->task->editor->activate = array('id' => 'comment', 'tools' => 'simpleTools');
$config->task->editor->cancel   = array('id' => 'comment', 'tools' => 'simpleTools');
$config->task->editor->pause    = array('id' => 'comment', 'tools' => 'simpleTools');

$config->task->exportFieldsOld = '
    id, project, module, story,dept,
    name, desc,
    type, pri,estStarted, realStarted, deadline, status,estimate, consumed, left,
    mailto, progress,
    openedBy, openedDate, assignedTo, assignedDate, 
    finishedBy, finishedDate, canceledBy, canceledDate,
    closedBy, closedDate, closedReason,
    lastEditedBy, lastEditedDate,files
    ';

$config->task->exportFields = '
    id, project, module, story,dept,
    name, pri, estimate, estStarted, deadline,     
    assignedTo, checkBy, status
    ';

$config->task->importFields = 'id,project,module,story,dept,name,pri,estimate,estStarted,deadline,assignedTo';

if($config->global->flow == 'onlyTask') $config->task->exportFields = str_replace(array(' story,'), '', $config->task->exportFields);

//oscar: $config->task->customCreateFields      = 'story,estStarted,deadline,mailto,pri,estimate';
$config->task->customCreateFields      = 'story,pri,files,deadline';
//oscar: $config->task->customBatchCreateFields = 'module,story,assignedTo,estimate,estStarted,deadline,desc,pri';
$config->task->customBatchCreateFields = 'module,story,dept,estimate,pri,deadline';
$config->task->customBatchEditFields   = 'module,assignedTo,status,pri,estimate,record,left,estStarted,deadline,finishedBy,canceledBy,closedBy,closedReason';

$config->task->custom = new stdclass();
$config->task->custom->createFields      = $config->task->customCreateFields;
//oscar:$config->task->custom->batchCreateFields = 'module,story,assignedTo,estimate,desc,pri';
$config->task->custom->batchCreateFields = 'module,story,dept,estimate,pri,deadline';
$config->task->custom->batchEditFields   = 'module,assignedTo,status,pri,estimate,record,left,finishedBy,closedBy,closedReason';


if($config->global->flow == 'onlyTask')
{
    $config->task->customCreateFields        = str_replace(array('story,'), '', $config->task->customCreateFields);
    $config->task->customBatchCreateFields   = str_replace(array('story,'), '', $config->task->customBatchCreateFields);
    $config->task->custom->batchCreateFields = str_replace(array('story,'), '', $config->task->custom->batchCreateFields);
}

$config->task->datatable = new stdclass();
//$config->task->datatable->defaultField = array('id', 'pri', 'name', 'dept', 'status', 'deadline', 'openedDate', 'openedBy', 'assignedTo', 'finishedBy', 'estimate', 'consumed', 'left', 'actions');
// oscar todo modify this to change project-task view table header
$config->task->datatable->defaultField = array('id', 'pri', 'name', 'status', 'deadline', 'openedDate', 'openedBy', 'assignedTo', 'finishedBy', 'estimate', 'checkBy', 'actions');

$config->task->datatable->fieldList['id']['title']    = 'idAB';
$config->task->datatable->fieldList['id']['fixed']    = 'left';
$config->task->datatable->fieldList['id']['width']    = '70';
$config->task->datatable->fieldList['id']['required'] = 'yes';

$config->task->datatable->fieldList['project']['title']    = 'project';
$config->task->datatable->fieldList['project']['fixed']    = 'left';
$config->task->datatable->fieldList['project']['width']    = 'auto';
$config->task->datatable->fieldList['project']['required'] = 'yes';

$config->task->datatable->fieldList['pri']['title']    = 'priAB';
$config->task->datatable->fieldList['pri']['fixed']    = 'left';
$config->task->datatable->fieldList['pri']['width']    = '40';
$config->task->datatable->fieldList['pri']['required'] = 'no';

$config->task->datatable->fieldList['name']['title']    = 'name';
$config->task->datatable->fieldList['name']['fixed']    = 'left';
$config->task->datatable->fieldList['name']['width']    = '350';
$config->task->datatable->fieldList['name']['required'] = 'yes';

$config->task->datatable->fieldList['type']['title']    = 'type';
$config->task->datatable->fieldList['type']['fixed']    = 'no';
$config->task->datatable->fieldList['type']['width']    = '80';
$config->task->datatable->fieldList['type']['required'] = 'no';

$config->task->datatable->fieldList['dept']['title']    = 'dept';
$config->task->datatable->fieldList['dept']['fixed']    = 'no';
$config->task->datatable->fieldList['dept']['width']    = 'auto';
$config->task->datatable->fieldList['dept']['required'] = 'no';

$config->task->datatable->fieldList['milestone']['title']    = 'milestone';
$config->task->datatable->fieldList['milestone']['fixed']    = 'no';
$config->task->datatable->fieldList['milestone']['width']    = 'auto';
$config->task->datatable->fieldList['milestone']['required'] = 'yes';

$config->task->datatable->fieldList['status']['title']    = 'statusAB';
$config->task->datatable->fieldList['status']['fixed']    = 'no';
$config->task->datatable->fieldList['status']['width']    = '60';
$config->task->datatable->fieldList['status']['required'] = 'no';

$config->task->datatable->fieldList['estimate']['title']    = 'estimateAB';
$config->task->datatable->fieldList['estimate']['fixed']    = 'no';
$config->task->datatable->fieldList['estimate']['width']    = '40';
$config->task->datatable->fieldList['estimate']['required'] = 'no';

$config->task->datatable->fieldList['consumed']['title']    = 'consumedAB';
$config->task->datatable->fieldList['consumed']['fixed']    = 'no';
$config->task->datatable->fieldList['consumed']['width']    = '50';
$config->task->datatable->fieldList['consumed']['required'] = 'no';

$config->task->datatable->fieldList['left']['title']    = 'leftAB';
$config->task->datatable->fieldList['left']['fixed']    = 'no';
$config->task->datatable->fieldList['left']['width']    = '40';
$config->task->datatable->fieldList['left']['required'] = 'no';

$config->task->datatable->fieldList['progress']['title']    = 'progress';
$config->task->datatable->fieldList['progress']['fixed']    = 'no';
$config->task->datatable->fieldList['progress']['width']    = '40';
$config->task->datatable->fieldList['progress']['required'] = 'no';
$config->task->datatable->fieldList['progress']['sort']     = 'no';

$config->task->datatable->fieldList['deadline']['title']    = 'deadlineAB';
$config->task->datatable->fieldList['deadline']['fixed']    = 'no';
$config->task->datatable->fieldList['deadline']['width']    = '70';
$config->task->datatable->fieldList['deadline']['required'] = 'yes';

$config->task->datatable->fieldList['openedBy']['title']    = 'openedByAB';
$config->task->datatable->fieldList['openedBy']['fixed']    = 'no';
$config->task->datatable->fieldList['openedBy']['width']    = '70';
$config->task->datatable->fieldList['openedBy']['required'] = 'yes';

$config->task->datatable->fieldList['openedDate']['title']    = 'openedDate';
$config->task->datatable->fieldList['openedDate']['fixed']    = 'no';
$config->task->datatable->fieldList['openedDate']['width']    = '90';
$config->task->datatable->fieldList['openedDate']['required'] = 'no';

$config->task->datatable->fieldList['estStarted']['title']    = 'estStarted';
$config->task->datatable->fieldList['estStarted']['fixed']    = 'no';
$config->task->datatable->fieldList['estStarted']['width']    = '90';
$config->task->datatable->fieldList['estStarted']['required'] = 'no';

$config->task->datatable->fieldList['realStarted']['title']    = 'realStarted';
$config->task->datatable->fieldList['realStarted']['fixed']    = 'no';
$config->task->datatable->fieldList['realStarted']['width']    = '90';
$config->task->datatable->fieldList['realStarted']['required'] = 'no';

$config->task->datatable->fieldList['assignedTo']['title']    = 'assignedTo';
$config->task->datatable->fieldList['assignedTo']['fixed']    = 'no';
$config->task->datatable->fieldList['assignedTo']['width']    = '80';
$config->task->datatable->fieldList['assignedTo']['required'] = 'no';

$config->task->datatable->fieldList['assignedDate']['title']    = 'assignedDate';
$config->task->datatable->fieldList['assignedDate']['fixed']    = 'no';
$config->task->datatable->fieldList['assignedDate']['width']    = '90';
$config->task->datatable->fieldList['assignedDate']['required'] = 'no';

$config->task->datatable->fieldList['finishedBy']['title']    = 'finishedByAB';
$config->task->datatable->fieldList['finishedBy']['fixed']    = 'no';
$config->task->datatable->fieldList['finishedBy']['width']    = '80';
$config->task->datatable->fieldList['finishedBy']['required'] = 'no';

$config->task->datatable->fieldList['finishedDate']['title']    = 'finishedDateAB';
$config->task->datatable->fieldList['finishedDate']['fixed']    = 'no';
$config->task->datatable->fieldList['finishedDate']['width']    = '80';
$config->task->datatable->fieldList['finishedDate']['required'] = 'no';

$config->task->datatable->fieldList['canceledBy']['title']    = 'canceledBy';
$config->task->datatable->fieldList['canceledBy']['fixed']    = 'no';
$config->task->datatable->fieldList['canceledBy']['width']    = '80';
$config->task->datatable->fieldList['canceledBy']['required'] = 'no';

$config->task->datatable->fieldList['canceledDate']['title']    = 'canceledDate';
$config->task->datatable->fieldList['canceledDate']['fixed']    = 'no';
$config->task->datatable->fieldList['canceledDate']['width']    = '80';
$config->task->datatable->fieldList['canceledDate']['required'] = 'no';

$config->task->datatable->fieldList['closedBy']['title']    = 'closedBy';
$config->task->datatable->fieldList['closedBy']['fixed']    = 'no';
$config->task->datatable->fieldList['closedBy']['width']    = '80';
$config->task->datatable->fieldList['closedBy']['required'] = 'no';

$config->task->datatable->fieldList['closedDate']['title']    = 'closedDate';
$config->task->datatable->fieldList['closedDate']['fixed']    = 'no';
$config->task->datatable->fieldList['closedDate']['width']    = '90';
$config->task->datatable->fieldList['closedDate']['required'] = 'no';

$config->task->datatable->fieldList['closedReason']['title']    = 'closedReason';
$config->task->datatable->fieldList['closedReason']['fixed']    = 'no';
$config->task->datatable->fieldList['closedReason']['width']    = '80';
$config->task->datatable->fieldList['closedReason']['required'] = 'no';

global $lang;
$config->task->datatable->fieldList['story']['title']    = "storyAB";
$config->task->datatable->fieldList['story']['fixed']    = 'no';
$config->task->datatable->fieldList['story']['width']    = '40';
$config->task->datatable->fieldList['story']['required'] = 'no';
$config->task->datatable->fieldList['story']['name']     = $lang->task->story;

$config->task->datatable->fieldList['mailto']['title']    = 'mailto';
$config->task->datatable->fieldList['mailto']['fixed']    = 'no';
$config->task->datatable->fieldList['mailto']['width']    = '100';
$config->task->datatable->fieldList['mailto']['required'] = 'no';

$config->task->datatable->fieldList['lastEditedBy']['title']    = 'lastEditedBy';
$config->task->datatable->fieldList['lastEditedBy']['fixed']    = 'no';
$config->task->datatable->fieldList['lastEditedBy']['width']    = '80';
$config->task->datatable->fieldList['lastEditedBy']['required'] = 'no';

$config->task->datatable->fieldList['lastEditedDate']['title']    = 'lastEditedDate';
$config->task->datatable->fieldList['lastEditedDate']['fixed']    = 'no';
$config->task->datatable->fieldList['lastEditedDate']['width']    = '90';
$config->task->datatable->fieldList['lastEditedDate']['required'] = 'no';

$config->task->datatable->fieldList['actions']['title']    = 'actions';
$config->task->datatable->fieldList['actions']['fixed']    = 'right';
$config->task->datatable->fieldList['actions']['width']    = '180'; // oscar:180
$config->task->datatable->fieldList['actions']['required'] = 'yes';

$config->task->datatable->fieldList['checkBy']['title']    = 'checkBy';
$config->task->datatable->fieldList['checkBy']['fixed']    = 'right';
$config->task->datatable->fieldList['checkBy']['width']    = '90';
$config->task->datatable->fieldList['checkBy']['required'] = 'yes';