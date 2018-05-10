<?php

$config->gametaskinternal = new stdclass();
$config->gametaskinternal->batchCreate = 10;
$config->gametaskinternal->debug = false;


$config->gametaskinternal->editor = new stdclass();
$config->gametaskinternal->editor->edit     = array('id' => 'desc,srcResPath,gameResPath', 'tools' => 'simpleTools');



$config->gametaskinternal->defaultProject = 3;

define('TABLE_GAMETASKINTERNAL',                   'gametaskinternal');
define('TABLE_GAMETASKINTERNALVERSION',           'gametaskinternalversion');
define('TABLE_GAMEGROUPLEADERS',                   'gamegroupleaders');

define('GROUPNAME_CQYH',                            '传奇永恒');


$config->gametaskinternal->create->requiredFields      = 'version,dept,owner,title';


$config->gametaskinternal->reqSizeDeptList[0] = '美术-原画';
$config->gametaskinternal->reqSizeDeptList[1] = '美术-UI';

$config->gametaskinteranl->unassigned = 'unassigned';

$config->gametaskinternal->priList[0] = '';
$config->gametaskinternal->priList[1] = '1';
$config->gametaskinternal->priList[2] = '2';
$config->gametaskinternal->priList[3] = '3';
$config->gametaskinternal->priList[4] = '4';


$config->gametaskinternal->fields = 'id,product,version,dept,owner,assignedTo,workhour,completed,closed,title,count,width,height,desc,srcResPath,gameResPath,pri,createDate';
$config->gametaskinternal->editFields = 'product,version,dept,owner,assignedTo,workhour,title,count,width,height,desc,srcResPath,gameResPath,pri';

$config->gametaskinternal->datatable = new stdclass();

$config->gametaskinternal->datatable->defaultField = array('id', 'pri', 'title', 'version', 'dept', 'owner', 'assignedTo', 'workhour', 'completed', 'closed', 'count', 'width', 'height', 'desc', 'srcResPath', 'gameResPath', 'actions');
$config->gametaskinternal->datatable->indexField = array('id', 'pri', 'title', 'version', 'dept', 'owner', 'assignedTo', 'count', 'desc', 'actions');
$config->gametaskinternal->datatable->completedField = array('id', 'pri', 'title', 'version', 'dept', 'owner', 'assignedTo', 'count', 'completeDate', 'desc', 'actions');

$config->gametaskinternal->datatable->ownerTaskField = array('id', 'pri', 'title', 'version', 'assignedTo',  'completed', 'closed', 'count', 'desc', 'createDate', 'actions');
$config->gametaskinternal->datatable->myTaskField = array('id', 'pri', 'title', 'version', 'owner',  'completed', 'closed', 'count', 'desc', 'actions');
$config->gametaskinternal->datatable->myDeptTaskField = array('id', 'pri', 'title', 'version', 'owner', 'assignedTo', 'completed', 'closed', 'count', 'pri', 'actions');

$config->gametaskinternal->toolsIndex = array('batchActive'=>true, 'batchClose'=>true, 'batchComplete'=>false, 'batchAssignTo'=>false, 'batchAssignToDept'=>true, 'batchChangeVersion'=>true, 'batchSetWorkhour'=>false, 'batchDelete'=>true, 'batchRestore'=>false);
$config->gametaskinternal->toolsDetails = array('batchActive'=>true, 'batchClose'=>true, 'batchComplete'=>false, 'batchAssignTo'=>false, 'batchAssignToDept'=>true, 'batchChangeVersion'=>true, 'batchSetWorkhour'=>false, 'batchDelete'=>true, 'batchRestore'=>false);
$config->gametaskinternal->toolsMyTask = array('batchActive'=>true, 'batchClose'=>true, 'batchComplete'=>false, 'batchAssignTo'=>false, 'batchAssignToDept'=>true, 'batchChangeVersion'=>true, 'batchSetWorkhour'=>false, 'batchDelete'=>true, 'batchRestore'=>false);
$config->gametaskinternal->toolsAssignedToMe = array('batchActive'=>false, 'batchClose'=>false, 'batchComplete'=>true, 'batchAssignTo'=>true, 'batchAssignToDept'=>false, 'batchChangeVersion'=>false, 'batchSetWorkhour'=>true, 'batchDelete'=>false, 'batchRestore'=>false);
$config->gametaskinternal->toolsMyDept = array('batchActive'=>false, 'batchClose'=>false, 'batchComplete'=>true, 'batchAssignTo'=>true, 'batchAssignToDept'=>true, 'batchChangeVersion'=>false, 'batchSetWorkhour'=>true, 'batchDelete'=>false, 'batchRestore'=>false);
$config->gametaskinternal->toolsCompletedlist = array('batchActive'=>true, 'batchClose'=>true, 'batchComplete'=>false, 'batchAssignTo'=>false, 'batchAssignToDept'=>true, 'batchChangeVersion'=>true, 'batchSetWorkhour'=>false, 'batchDelete'=>true, 'batchRestore'=>false);
$config->gametaskinternal->toolsIncompletedlist = array('batchActive'=>false, 'batchClose'=>false, 'batchComplete'=>false, 'batchAssignTo'=>false, 'batchAssignToDept'=>true, 'batchChangeVersion'=>true, 'batchSetWorkhour'=>false, 'batchDelete'=>true, 'batchRestore'=>false);
$config->gametaskinternal->toolsRestore = array('batchActive'=>false, 'batchClose'=>false, 'batchComplete'=>false, 'batchAssignTo'=>false, 'batchAssignToDept'=>true, 'batchChangeVersion'=>true, 'batchSetWorkhour'=>false, 'batchDelete'=>false, 'batchRestore'=>true);


$config->gametaskinternal->datatable->fieldList['id']['title']    = 'idAB';
$config->gametaskinternal->datatable->fieldList['id']['fixed']    = 'left';
$config->gametaskinternal->datatable->fieldList['id']['width']    = 'auto';
$config->gametaskinternal->datatable->fieldList['id']['required'] = 'yes';

$config->gametaskinternal->datatable->fieldList['version']['title']    = 'version';
$config->gametaskinternal->datatable->fieldList['version']['fixed']    = 'no';
$config->gametaskinternal->datatable->fieldList['version']['width']    = 'auto';
$config->gametaskinternal->datatable->fieldList['version']['required'] = 'no';

$config->gametaskinternal->datatable->fieldList['dept']['title']    = 'dept';
$config->gametaskinternal->datatable->fieldList['dept']['fixed']    = 'no';
$config->gametaskinternal->datatable->fieldList['dept']['width']    = 'auto';
$config->gametaskinternal->datatable->fieldList['dept']['required'] = 'no';

    $config->gametaskinternal->datatable->fieldList['owner']['title']    = 'owner';
$config->gametaskinternal->datatable->fieldList['owner']['fixed']    = 'no';
$config->gametaskinternal->datatable->fieldList['owner']['width']    = 'auto';
$config->gametaskinternal->datatable->fieldList['owner']['required'] = 'no';

$config->gametaskinternal->datatable->fieldList['assignedTo']['title']    = 'assignedTo';
$config->gametaskinternal->datatable->fieldList['assignedTo']['fixed']    = 'no';
$config->gametaskinternal->datatable->fieldList['assignedTo']['width']    = 'auto';
$config->gametaskinternal->datatable->fieldList['assignedTo']['required'] = 'no';

$config->gametaskinternal->datatable->fieldList['workhour']['title']    = 'workhour';
$config->gametaskinternal->datatable->fieldList['workhour']['fixed']    = 'left';
$config->gametaskinternal->datatable->fieldList['workhour']['width']    = 'auto';
$config->gametaskinternal->datatable->fieldList['workhour']['required'] = 'no';

$config->gametaskinternal->datatable->fieldList['completed']['title']    = 'completeStat';
$config->gametaskinternal->datatable->fieldList['completed']['fixed']    = 'left';
$config->gametaskinternal->datatable->fieldList['completed']['width']    = 'auto';
$config->gametaskinternal->datatable->fieldList['completed']['required'] = 'no';

$config->gametaskinternal->datatable->fieldList['closed']['title']    = 'closeStat';
$config->gametaskinternal->datatable->fieldList['closed']['fixed']    = 'left';
$config->gametaskinternal->datatable->fieldList['closed']['width']    = 'auto';
$config->gametaskinternal->datatable->fieldList['closed']['required'] = 'no';

$config->gametaskinternal->datatable->fieldList['title']['title']    = 'title';
$config->gametaskinternal->datatable->fieldList['title']['fixed']    = 'left';
$config->gametaskinternal->datatable->fieldList['title']['width']    = 'auto';
$config->gametaskinternal->datatable->fieldList['title']['required'] = 'yes';

$config->gametaskinternal->datatable->fieldList['count']['title']    = 'count';
$config->gametaskinternal->datatable->fieldList['count']['fixed']    = 'left';
$config->gametaskinternal->datatable->fieldList['count']['width']    = 'auto';
$config->gametaskinternal->datatable->fieldList['count']['required'] = 'no';

$config->gametaskinternal->datatable->fieldList['width']['title']    = 'width';
$config->gametaskinternal->datatable->fieldList['width']['fixed']    = 'left';
$config->gametaskinternal->datatable->fieldList['width']['width']    = 'auto';
$config->gametaskinternal->datatable->fieldList['width']['required'] = 'no';
$config->gametaskinternal->datatable->fieldList['width']['sort']     = 'no';

$config->gametaskinternal->datatable->fieldList['height']['title']    = 'height';
$config->gametaskinternal->datatable->fieldList['height']['fixed']    = 'left';
$config->gametaskinternal->datatable->fieldList['height']['width']    = 'auto';
$config->gametaskinternal->datatable->fieldList['height']['required'] = 'no';
$config->gametaskinternal->datatable->fieldList['height']['sort']     = 'no';

$config->gametaskinternal->datatable->fieldList['desc']['title']    = 'desc';
$config->gametaskinternal->datatable->fieldList['desc']['fixed']    = 'left';
$config->gametaskinternal->datatable->fieldList['desc']['width']    = 'auto';
$config->gametaskinternal->datatable->fieldList['desc']['required'] = 'no';
$config->gametaskinternal->datatable->fieldList['desc']['sort']     = 'no';

$config->gametaskinternal->datatable->fieldList['srcResPath']['title']    = 'srcResPath';
$config->gametaskinternal->datatable->fieldList['srcResPath']['fixed']    = 'left';
$config->gametaskinternal->datatable->fieldList['srcResPath']['width']    = 'auto';
$config->gametaskinternal->datatable->fieldList['srcResPath']['required'] = 'no';

$config->gametaskinternal->datatable->fieldList['gameResPath']['title']    = 'gameResPath';
$config->gametaskinternal->datatable->fieldList['gameResPath']['fixed']    = 'left';
$config->gametaskinternal->datatable->fieldList['gameResPath']['width']    = 'auto';
$config->gametaskinternal->datatable->fieldList['gameResPath']['required'] = 'no';

$config->gametaskinternal->datatable->fieldList['pri']['title']    = 'pri';
$config->gametaskinternal->datatable->fieldList['pri']['fixed']    = 'left';
$config->gametaskinternal->datatable->fieldList['pri']['width']    = 'auto';
$config->gametaskinternal->datatable->fieldList['pri']['required'] = 'no';

$config->gametaskinternal->datatable->fieldList['createDate']['title']    = 'createDate';
$config->gametaskinternal->datatable->fieldList['createDate']['fixed']    = 'left';
$config->gametaskinternal->datatable->fieldList['createDate']['width']    = 'auto';
$config->gametaskinternal->datatable->fieldList['createDate']['required'] = 'no';

$config->gametaskinternal->datatable->fieldList['completeDate']['title']    = 'completedDate';
$config->gametaskinternal->datatable->fieldList['completeDate']['fixed']    = 'left';
$config->gametaskinternal->datatable->fieldList['completeDate']['width']    = 'auto';
$config->gametaskinternal->datatable->fieldList['completeDate']['required'] = 'no';

$config->gametaskinternal->datatable->fieldList['actions']['title']    = 'actions';
$config->gametaskinternal->datatable->fieldList['actions']['fixed']    = 'right';
$config->gametaskinternal->datatable->fieldList['actions']['width']    = '240';
$config->gametaskinternal->datatable->fieldList['actions']['required'] = 'yes';
$config->gametaskinternal->datatable->fieldList['actions']['sort']     = 'no';