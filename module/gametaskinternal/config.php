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


$config->gametaskinternal->reqSizeDeptList[0] = '美术-原画';
$config->gametaskinternal->reqSizeDeptList[1] = '美术-UI';

$config->gametaskinternal->priList[0] = '';
$config->gametaskinternal->priList[1] = '1';
$config->gametaskinternal->priList[2] = '2';
$config->gametaskinternal->priList[3] = '3';
$config->gametaskinternal->priList[4] = '4';


$config->gametaskinternal->datatable = new stdclass();

$config->gametaskinternal->datatable->defaultField = array('id', 'version', 'dept', 'owner', 'assignedTo', 'workhour', 'completed', 'closed', 'title', 'count', 'width', 'height', 'desc', 'srcResPath', 'gameResPath', 'pri', 'actions');

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

$config->gametaskinternal->datatable->fieldList['height']['title']    = 'height';
$config->gametaskinternal->datatable->fieldList['height']['fixed']    = 'left';
$config->gametaskinternal->datatable->fieldList['height']['width']    = 'auto';
$config->gametaskinternal->datatable->fieldList['height']['required'] = 'no';

$config->gametaskinternal->datatable->fieldList['desc']['title']    = 'desc';
$config->gametaskinternal->datatable->fieldList['desc']['fixed']    = 'left';
$config->gametaskinternal->datatable->fieldList['desc']['width']    = 'auto';
$config->gametaskinternal->datatable->fieldList['desc']['required'] = 'no';

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


$config->testcase->datatable->fieldList['actions']['title']    = 'actions';
$config->testcase->datatable->fieldList['actions']['fixed']    = 'right';
$config->testcase->datatable->fieldList['actions']['width']    = '240';
$config->testcase->datatable->fieldList['actions']['required'] = 'yes';
$config->testcase->datatable->fieldList['actions']['sort']     = 'no';