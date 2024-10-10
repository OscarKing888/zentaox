<?php
/**
 * The meeting module zh-tw file of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青島易軟天創網絡科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     meeting
 * @version     $Id: zh-tw.php 5022 2013-07-05 06:50:39Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
$lang->meeting->common       = '待辦';
$lang->meeting->index        = "待辦一覽";
$lang->meeting->create       = "新增";
$lang->meeting->batchCreate  = "批量添加";
$lang->meeting->edit         = "更新待辦";
$lang->meeting->batchEdit    = "批量編輯";
$lang->meeting->view         = "待辦詳情";
$lang->meeting->finish       = "完成";
$lang->meeting->batchFinish  = "批量完成";
$lang->meeting->export       = "導出";
$lang->meeting->delete       = "刪除待辦";
$lang->meeting->import2Today = "導入到今天";
$lang->meeting->import       = "導入";
$lang->meeting->legendBasic  = "基本信息";

$lang->meeting->id          = '編號';
$lang->meeting->account     = '所有者';
$lang->meeting->date        = '日期';
$lang->meeting->begin       = '開始';
$lang->meeting->end         = '結束';
$lang->meeting->beginAB     = '開始';
$lang->meeting->endAB       = '結束';
$lang->meeting->beginAndEnd = '起止時間';
$lang->meeting->idvalue     = '關聯編號';
$lang->meeting->type        = '類型';
$lang->meeting->pri         = '優先順序';
$lang->meeting->name        = '名稱';
$lang->meeting->status      = '狀態';
$lang->meeting->description        = '描述';
$lang->meeting->private     = '私人事務';

$lang->meeting->confirmBug   = '該Todo關聯的是Bug #%s，需要修改它嗎？';
$lang->meeting->confirmTask  = '該Todo關聯的是Task #%s，需要修改它嗎？';
$lang->meeting->confirmStory = '該Todo關聯的是Story #%s，需要修改它嗎？';

$lang->meeting->statusList['wait']  = '未開始';
$lang->meeting->statusList['doing'] = '進行中';
$lang->meeting->statusList['done']  = '已完成';
//$lang->meeting->statusList['cancel']   = '已取消';
//$lang->meeting->statusList['postpone'] = '已延期';

$lang->meeting->priList[3] = '一般';
$lang->meeting->priList[1] = '最高';
$lang->meeting->priList[2] = '較高';
$lang->meeting->priList[4] = '最低';
$lang->meeting->priList[0] = '';

$lang->meeting->typeList['custom'] = '自定義';
$lang->meeting->typeList['bug']    = 'Bug';
$lang->meeting->typeList['task']   = $lang->projectCommon . '任務';
$lang->meeting->typeList['story']  = $lang->projectCommon . '需求';

global $config;
if($config->global->flow == 'onlyTest' or $config->global->flow == 'onlyStory') unset($lang->meeting->typeList['task']);
if($config->global->flow == 'onlyTask' or $config->global->flow == 'onlyStory') unset($lang->meeting->typeList['bug']);

$lang->meeting->confirmDelete  = "您確定要刪除這條待辦嗎？";
$lang->meeting->thisIsPrivate  = '這是一條私人事務。:)';
$lang->meeting->lblDisableDate = '暫時不設定時間';
$lang->meeting->noTodo         = '該類型沒有待辦事務';

$lang->meeting->periods['today']      = '今日';
$lang->meeting->periods['yesterday']  = '昨日';
$lang->meeting->periods['thisWeek']   = '本週';
$lang->meeting->periods['lastWeek']   = '上周';
$lang->meeting->periods['thisMonth']  = '本月';
$lang->meeting->periods['lastmonth']  = '上月';
$lang->meeting->periods['thisSeason'] = '本季';
$lang->meeting->periods['thisYear']   = '本年';
$lang->meeting->periods['future']     = '待定';
$lang->meeting->periods['before']     = '未完';
$lang->meeting->periods['all']        = '所有';

$lang->meeting->action = new stdclass();
$lang->meeting->action->finished = array('main' => '$date, 由 <strong>$actor</strong>完成');
$lang->meeting->action->marked   = array('main' => '$date, 由 <strong>$actor</strong> 標記為<strong>$extra</strong>。', 'extra' => 'statusList');
