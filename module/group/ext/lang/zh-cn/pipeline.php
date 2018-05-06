<?php
/* pipeline module. */
//$lang->resource->pipeline = new stdclass();

$lang->resource->pipeline->index         = 'index';
$lang->resource->pipeline->restore       = 'restorepage';

$lang->resource->pipeline->create       = 'create';
$lang->resource->pipeline->delete       = 'delete';
$lang->resource->pipeline->restorepipeline       = 'restore';
$lang->resource->pipeline->view = 'view';
$lang->resource->pipeline->edit       = 'edit';
$lang->resource->pipeline->batchCreateRootTask       = 'batchCreateRootTask';

$lang->resource->pipeline->groupleaders = 'groupleaders';



$lang->pipeline->methodOrder[0]  = 'index';
$lang->pipeline->methodOrder[1]  = 'restore ';

$lang->pipeline->methodOrder[15]  = 'create';
$lang->pipeline->methodOrder[25]  = 'delete';
$lang->resource->methodOrder[30]  = 'restore';
$lang->pipeline->methodOrder[35]  = 'view';
$lang->pipeline->methodOrder[40]  = 'edit';
$lang->pipeline->methodOrder[45]  = 'batchCreateRootTask';
