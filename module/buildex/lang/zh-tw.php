<?php
/**
 * The build module zh-tw file of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青島易軟天創網絡科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     build
 * @version     $Id: zh-tw.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
$lang->buildex->common       = "版本";
$lang->buildex->create       = "創建版本";
$lang->buildex->edit         = "編輯版本";
$lang->buildex->linkStory    = "關聯需求";
$lang->buildex->linkBug      = "關聯Bug";
$lang->buildex->delete       = "刪除版本";
$lang->buildex->deleted      = "已刪除";
$lang->buildex->view         = "版本詳情";
$lang->buildex->batchUnlink          = '批量移除';
$lang->buildex->batchUnlinkStory     = '批量移除需求';
$lang->buildex->batchUnlinkBug       = '批量移除Bug';

$lang->buildex->confirmDelete      = "您確認刪除該版本嗎？";
$lang->buildex->confirmUnlinkStory = "您確認移除該需求嗎？";
$lang->buildex->confirmUnlinkBug   = "您確認移除該Bug嗎？";

$lang->buildex->basicInfo = '基本信息';

$lang->buildex->id        = 'ID';
$lang->buildex->product   = $lang->productCommon;
$lang->buildex->branch    = '平台/分支';
$lang->buildex->project   = '所屬' . $lang->projectCommon;
$lang->buildex->name      = '名稱編號';
$lang->buildex->date      = '打包日期';
$lang->buildex->builder   = '構建者';
$lang->buildex->scmPath   = '原始碼地址';
$lang->buildex->filePath  = '下載地址';
$lang->buildex->desc      = '描述';
$lang->buildex->files     = '上傳發行包';
$lang->buildex->last      = '上個版本';
$lang->buildex->packageType        = '包類型';
$lang->buildex->unlinkStory        = '移除需求';
$lang->buildex->unlinkBug          = '移除Bug';
$lang->buildex->stories            = '完成的需求';
$lang->buildex->bugs               = '解決的Bug';
$lang->buildex->generatedBugs      = '產生的Bug';
$lang->buildex->noProduct          = " <span style='color:red'>該{$lang->projectCommon}沒有關聯{$lang->productCommon}，無法創建版本，請先<a href='%s'>關聯{$lang->productCommon}</a></span>";

$lang->buildex->finishStories = ' 本次共完成 %s 個需求';
$lang->buildex->resolvedBugs  = ' 本次共解決 %s 個Bug';
$lang->buildex->createdBugs   = ' 本次共產生 %s 個Bug';

$lang->buildex->placeholder = new stdclass();
$lang->buildex->placeholder->scmPath  = ' 軟件原始碼庫，如Subversion、Git庫地址';
$lang->buildex->placeholder->filePath = ' 該版本軟件包下載存儲地址';

$lang->buildex->action = new stdclass();
$lang->buildex->action->buildopened = '$date, 由 <strong>$actor</strong> 創建版本 <strong>$extra</strong>。' . "\n";
