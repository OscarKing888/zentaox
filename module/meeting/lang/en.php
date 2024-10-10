<?php
/**
 * The meeting module English file of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     meeting
 * @version     $Id: en.php 4676 2013-04-26 06:08:23Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
$lang->meeting->common       = 'To-Do';
$lang->meeting->index        = "Home";
$lang->meeting->create       = "Create";
$lang->meeting->batchCreate  = "Batch Create";
$lang->meeting->edit         = "Edit";
$lang->meeting->batchEdit    = "Batch Edit";
$lang->meeting->view         = "Info";
$lang->meeting->finish       = "Finish";
$lang->meeting->batchFinish  = "Batch Finish";
$lang->meeting->export       = "Export";
$lang->meeting->delete       = "Delete";
$lang->meeting->import2Today = "Import to Today";
$lang->meeting->import       = "Import";
$lang->meeting->legendBasic  = "Basic Info";

$lang->meeting->id          = 'ID';
$lang->meeting->account     = 'Owner';
$lang->meeting->date        = 'Date';
$lang->meeting->begin       = 'Begin';
$lang->meeting->end         = 'End';
$lang->meeting->beginAB     = 'Begin';
$lang->meeting->endAB       = 'End';
$lang->meeting->beginAndEnd = 'Duration';
$lang->meeting->idvalue     = 'Link ID';
$lang->meeting->type        = 'Type';
$lang->meeting->pri         = 'Priority';
$lang->meeting->name        = 'Name';
$lang->meeting->status      = 'Status';
$lang->meeting->description        = 'Description';
$lang->meeting->private     = 'Private';

$lang->meeting->confirmBug   = 'This To-Do is related to Bug #%s. Do you want to edit it?';
$lang->meeting->confirmTask  = 'This To-Do is related to Task #%s，Do you want to edit it?';
$lang->meeting->confirmStory = 'This To-Do is related to Story #%s，Do you want to edit it?';

$lang->meeting->statusList['wait']  = 'Wait';
$lang->meeting->statusList['doing'] = 'Doing';
$lang->meeting->statusList['done']  = 'Done';
//$lang->meeting->statusList['cancel']   = 'Cancelled';
//$lang->meeting->statusList['postpone'] = 'Delayed';

$lang->meeting->priList[3] = '3';
$lang->meeting->priList[1] = '1';
$lang->meeting->priList[2] = '2';
$lang->meeting->priList[4] = '4';
$lang->meeting->priList[0] = '';

$lang->meeting->typeList['custom'] = 'Custom';
$lang->meeting->typeList['bug']    = 'Bug';
$lang->meeting->typeList['task']   = $lang->projectCommon . 'Task';
$lang->meeting->typeList['story']  = $lang->projectCommon . 'Story';

global $config;
if($config->global->flow == 'onlyTest' or $config->global->flow == 'onlyStory') unset($lang->meeting->typeList['task']);
if($config->global->flow == 'onlyTask' or $config->global->flow == 'onlyStory') unset($lang->meeting->typeList['bug']);

$lang->meeting->confirmDelete  = "Are you sure to delete this To-Do?";
$lang->meeting->thisIsPrivate  = 'This is a private To-Do:)';
$lang->meeting->lblDisableDate = 'Set later.';
$lang->meeting->noTodo         = 'No this type of to-dos.';

$lang->meeting->periods['today']      = 'Today';
$lang->meeting->periods['yesterday']  = 'Yesterday';
$lang->meeting->periods['thisWeek']   = 'This Week';
$lang->meeting->periods['lastWeek']   = 'Last Week';
$lang->meeting->periods['thisMonth']  = 'This Month';
$lang->meeting->periods['lastmonth']  = 'Last Month';
$lang->meeting->periods['thisSeason'] = 'This Season';
$lang->meeting->periods['thisYear']   = 'This Year';
$lang->meeting->periods['future']     = 'Pending';
$lang->meeting->periods['before']     = 'Undone';
$lang->meeting->periods['all']        = 'All';

$lang->meeting->action = new stdclass();
$lang->meeting->action->finished = array('main' => '$date, is finished by <strong>$actor</strong>.');
$lang->meeting->action->marked   = array('main' => '$date, is marked by <strong>$actor</strong> as <strong>$extra</strong>.', 'extra' => 'statusList');
