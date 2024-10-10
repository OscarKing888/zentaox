<?php
/**
 * The build module zh-cn file of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     build
 * @version     $Id: zh-cn.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
$lang->buildex->common       = "版本(新)";
$lang->buildex->create       = "创建版本";
$lang->buildex->edit         = "编辑版本";
$lang->buildex->linkStory    = "关联需求";
$lang->buildex->linkBug      = "关联Bug";
$lang->buildex->delete       = "删除版本";
$lang->buildex->deleted      = "已删除";
$lang->buildex->view         = "版本详情";
$lang->buildex->batchUnlink          = '批量移除';
$lang->buildex->batchUnlinkStory     = '批量移除需求';
$lang->buildex->batchUnlinkBug       = '批量移除Bug';

$lang->buildex->confirmDelete      = "您确认删除该版本吗？";
$lang->buildex->confirmUnlinkStory = "您确认移除该需求吗？";
$lang->buildex->confirmUnlinkBug   = "您确认移除该Bug吗？";

$lang->buildex->basicInfo = '基本信息';

$lang->buildex->id        = 'ID';
$lang->buildex->product   = $lang->productCommon;
$lang->buildex->branch    = '平台/分支';
$lang->buildex->project   = '所属' . $lang->projectCommon;
$lang->buildex->name      = '名称版本号';
$lang->buildex->date      = '预计打包日期';
$lang->buildex->builder   = '构建者';
$lang->buildex->scmPath   = '源代码地址';
$lang->buildex->filePath  = '下载地址';
$lang->buildex->desc      = '版本需求描述';
$lang->buildex->files     = '上传发行包';
$lang->buildex->last      = '上个版本';
$lang->buildex->packageType        = '包类型';
$lang->buildex->unlinkStory        = '移除需求';
$lang->buildex->unlinkBug          = '移除Bug';
$lang->buildex->stories            = '完成的需求';
$lang->buildex->bugs               = '解决的Bug';
$lang->buildex->generatedBugs      = '产生的Bug';
$lang->buildex->noProduct          = " <span style='color:red'>该{$lang->projectCommon}没有关联{$lang->productCommon}，无法创建版本，请先<a href='%s'>关联{$lang->productCommon}</a></span>";

$lang->buildex->finishStories = ' 本次共完成 %s 个需求';
$lang->buildex->resolvedBugs  = ' 本次共解决 %s 个Bug';
$lang->buildex->createdBugs   = ' 本次共产生 %s 个Bug';

$lang->buildex->placeholder = new stdclass();
$lang->buildex->placeholder->scmPath  = ' 软件源代码库，如Subversion、Git库地址';
$lang->buildex->placeholder->filePath = ' 该版本软件包下载存储地址';

$lang->buildex->action = new stdclass();
$lang->buildex->action->buildopened = '$date, 由 <strong>$actor</strong> 创建版本 <strong>$extra</strong>。' . "\n";


$lang->buildex->shippingType = '版本目标';

$lang->buildex->shippingTypeList[0] = '';
$lang->buildex->shippingTypeList[1] = '上线发布';
$lang->buildex->shippingTypeList[2] = '内部QA';
$lang->buildex->shippingTypeList[3] = 'TestFlight/Google Play Test';
$lang->buildex->shippingTypeList[4] = '对外测试包';

$lang->buildex->buildOption = '版本方案';

$lang->buildex->buildAndroidPackage = '安卓版本';
$lang->buildex->buildApk = '需要制作Apk版本';
$lang->buildex->buildApkBuilder = 'Apk版本构建者';
$lang->buildex->buildApkPath = 'Apk测试包下载地址';
$lang->buildex->buildApkPathReleasePath = 'Apk正式包下载地址';


$lang->buildex->buildiOSPackage = 'iOS版本';
$lang->buildex->buildIos = '需要制作iOS版本';
$lang->buildex->buildIosBuilder = 'iOS版本构建者';
$lang->buildex->buildIosPath = 'iOS测试包下载地址';


$lang->buildex->buildHotpatch = '热更补丁';
$lang->buildex->buildHotpatchBuilder = '热更补丁构建者';
$lang->buildex->buildHotpatchAndroid = '需要出Android热更补丁';
$lang->buildex->buildHotpatchAndroidPath = 'Android补丁包下载地址';
$lang->buildex->buildHotpatchiOS = '需要出iOS热更补丁';
$lang->buildex->buildHotpatchiOSPath = 'iOS补丁包下载地址';

$lang->buildex->buildServerOption = '服务器版本';
$lang->buildex->buildServerImage = '需要服务器Image更新';
$lang->buildex->buildServerImageBuilder = 'Server版本构建者';
$lang->buildex->buildServerImagePath = '服务器Image名字';


$lang->buildex->srcSVNPath = '采用SVN分支地址';
$lang->buildex->svnOption = 'SVN操作';
$lang->buildex->svnTagOperator = 'SVN tag操作者';
$lang->buildex->svnTagPath = 'tag路径';
//$lang->buildex->buildDevDesc = '研发补充说明';

$lang->buildex->qaOption = 'QA计划类型';
$lang->buildex->qaSmoke = '快速冒烟测试';
$lang->buildex->qaSmokeFull = '完整冒烟测试';
$lang->buildex->qaFunction = '针对性功能测试';
$lang->buildex->qaFull = '全功能覆盖测试';
$lang->buildex->qaRegTesting = '回归测试';

$lang->buildex->opOption = '运营方案';
$lang->buildex->opAnnouncementContent = '公告内容';

$lang->buildex->op->googleStoreOper = 'Google上架负责人';
$lang->buildex->op->appStoreOper = 'iOS上架负责人';
$lang->buildex->op->patchOper = '热更补丁CDN推送负责人';

$lang->buildex->op->storeStatus[0] = '未提交';
$lang->buildex->op->storeStatus[1] = '已经提交';
$lang->buildex->op->storeStatus[2] = '审核通过';
$lang->buildex->op->storeStatus[3] = '审核不通过';
$lang->buildex->op->storeStatus[4] = '已经外放';

$lang->buildex->operStatus[0] = '待确认';
$lang->buildex->operStatus[1] = '已完成';

$lang->buildex->updatePathInfo = '更新路径信息';
$lang->buildex->updatePathNotFound = '未填写';
$lang->buildex->updatePathNotReq = '不需要';

$lang->buildex->buildServerImagePathPlaceHolder = "602245705509.dkr.ecr.us-west-2.amazonaws.com/firestrike-server:21041302";
$lang->buildex->buildApkPathPlaceHolder = "\\\\172.20.1.188\\package\ReleaseQA\\Android'";
$lang->buildex->buildApkPathReleasePathPlaceHolder = "\\\\172.20.1.188\\package\Release\\Android'";
$lang->buildex->buildIosPathPlaceHolder = "\\\\172.20.1.188\\package\ReleaseQA\\iOS'";
$lang->buildex->buildHotpatchAndroidPathPlaceHolder = "\\\\172.20.1.188\\package\Patch\\Android'";
$lang->buildex->buildHotpatchiOSPathPlaceHolder = "\\\\172.20.1.188\\package\Patch\\iOS'";

