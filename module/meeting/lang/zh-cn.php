<?php
/**
 * The meeting module zh-cn file of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     meeting
 * @version     $Id: zh-cn.php 5022 2013-07-05 06:50:39Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
$lang->meeting->common       = '事务';
$lang->meeting->index        = "事务一览";
$lang->meeting->create       = "新增";
$lang->meeting->batchCreate  = "批量添加";
$lang->meeting->edit         = "更新事务";
$lang->meeting->batchEdit    = "批量编辑";
$lang->meeting->view         = "事务详情";
$lang->meeting->finish       = "完成";
$lang->meeting->batchFinish  = "批量完成";
$lang->meeting->activate  = "激活";

$lang->meeting->export       = "导出";
$lang->meeting->delete       = "删除事务";
$lang->meeting->import2Today = "导入到今天";
$lang->meeting->import       = "导入";
$lang->meeting->legendBasic  = "基本信息";

$lang->meeting->id          = '编号';
$lang->meeting->account     = '所有者';
$lang->meeting->createDate        = '创建日期';
$lang->meeting->deadline        = '截止日期';
$lang->meeting->assignedTo       = '指派给';

$lang->meeting->pri         = '优先级';
$lang->meeting->name        = '名称';
$lang->meeting->status      = '状态';
$lang->meeting->description        = '事务';
$lang->meeting->private     = '私人事务';

$lang->meeting->confirmBug   = '该Todo关联的是Bug #%s，需要修改它吗？';
$lang->meeting->confirmTask  = '该Todo关联的是Task #%s，需要修改它吗？';
$lang->meeting->confirmStory = '该Todo关联的是Story #%s，需要修改它吗？';

$lang->meeting->statusList['wait']  = '未开始';
$lang->meeting->statusList['doing'] = '进行中';
$lang->meeting->statusList['done']  = '已完成';
//$lang->meeting->statusList['cancel']   = '已取消';
//$lang->meeting->statusList['postpone'] = '已延期';

$lang->meeting->priList[3] = '3';
$lang->meeting->priList[1] = '1';
$lang->meeting->priList[2] = '2';
$lang->meeting->priList[4] = '4';
$lang->meeting->priList[0] = '0';

$lang->meeting->typeList['custom'] = '自定义';
$lang->meeting->typeList['bug']    = 'Bug';
$lang->meeting->typeList['task']   = $lang->projectCommon . '任务';
$lang->meeting->typeList['story']  = $lang->projectCommon . '需求';

global $config;
if($config->global->flow == 'onlyTest' or $config->global->flow == 'onlyStory') unset($lang->meeting->typeList['task']);
if($config->global->flow == 'onlyTask' or $config->global->flow == 'onlyStory') unset($lang->meeting->typeList['bug']);

$lang->meeting->confirmDelete  = "您确定要删除这条事务吗？";
$lang->meeting->thisIsPrivate  = '这是一条私人事务。:)';
$lang->meeting->lblDisableDate = '暂时不设定时间';
$lang->meeting->noTodo         = '该类型没有事务事务';

$lang->meeting->periods['today']      = '今日';
$lang->meeting->periods['yesterday']  = '昨日';
$lang->meeting->periods['thisWeek']   = '本周';
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
$lang->meeting->action->marked   = array('main' => '$date, 由 <strong>$actor</strong> 标记为<strong>$extra</strong>。', 'extra' => 'statusList');
