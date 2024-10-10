<?php
/**
 * The build module English file of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     build
 * @version     $Id: en.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
$lang->buildex->common       = "Build";
$lang->buildex->create       = "Create Build";
$lang->buildex->edit         = "Edit";
$lang->buildex->linkStory    = "Link Story";
$lang->buildex->linkBug      = "Link Bug";
$lang->buildex->delete       = "Delete Build";
$lang->buildex->deleted      = "Deleted";
$lang->buildex->view         = "Build Details";
$lang->buildex->batchUnlink          = 'Batch Unlink';
$lang->buildex->batchUnlinkStory     = 'Batch Story Unlink';
$lang->buildex->batchUnlinkBug       = 'Batch Bug Unlink';

$lang->buildex->confirmDelete      = "Do you want to delete this Build?";
$lang->buildex->confirmUnlinkStory = "Do you want to unlink this Story?";
$lang->buildex->confirmUnlinkBug   = "Do you want to unlink this Bug?";

$lang->buildex->basicInfo = 'Basic Info';

$lang->buildex->id        = 'ID';
$lang->buildex->product   = $lang->productCommon;
$lang->buildex->branch    = 'Platform/Branch';
$lang->buildex->project   = $lang->projectCommon;
$lang->buildex->name      = 'Name';
$lang->buildex->date      = 'Date';
$lang->buildex->builder   = 'Builder';
$lang->buildex->scmPath   = 'SCM Path';
$lang->buildex->filePath  = 'File Path';
$lang->buildex->desc      = 'Description';
$lang->buildex->files     = 'Upload Files';
$lang->buildex->last      = 'Last Build';
$lang->buildex->packageType        = 'Package Type';
$lang->buildex->unlinkStory        = 'Unlink Story';
$lang->buildex->unlinkBug          = 'Unlink Bug';
$lang->buildex->stories            = 'Finished Story';
$lang->buildex->bugs               = 'Resolved Bug';
$lang->buildex->generatedBugs      = 'Left Bug';
$lang->buildex->noProduct          = " <span style='color:red'>This {$lang->projectCommon} has not linked to {$lang->productCommon}, so Build cannot be created. Please first <a href='%s'> link {$lang->productCommon}</a></span>";

$lang->buildex->finishStories = '  %s Story(ies) have been finished.';
$lang->buildex->resolvedBugs  = '  %s Bug(s) have been resolved.';
$lang->buildex->createdBugs   = '  %s Bug(s) have been created.';

$lang->buildex->placeholder = new stdclass();
$lang->buildex->placeholder->scmPath  = ' Source code repository, e.g. Subversion/Git Library path';
$lang->buildex->placeholder->filePath = ' Path of this Build package for downloading.';

$lang->buildex->action = new stdclass();
$lang->buildex->action->buildopened = '$date, created by <strong>$actor</strong>, Build <strong>$extra</strong>.' . "\n";
