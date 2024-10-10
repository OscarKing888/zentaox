<?php
/**
 * The view file of build module's view method of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     build
 * @version     $Id: view.html.php 4386 2013-02-19 07:37:45Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php js::set('confirmUnlinkStory', $lang->buildex->confirmUnlinkStory)?>
<?php js::set('confirmUnlinkBug', $lang->buildex->confirmUnlinkBug)?>
<?php js::set('flow', $this->config->global->flow)?>
<?php if(isonlybody()):?>
    <style>
        #stories .action{display:none;}
        #bugs .action{display:none;}
        tbody tr td:last-child a{display:none;}
        tbody tr td:first-child input{display:none;}
        tfoot tr td .table-actions .btn{display:none;}
        #titlebar .actions{display:none}
        .row-table .col-side{display:none;}
    </style>
<?php endif;?>
<div id='titlebar'>
    <div class='heading'>
        <span class='prefix'><?php echo html::icon($lang->icons['build']);?> <strong><?php echo $buildObj->id;?></strong></span>
        <strong><?php echo $buildObj->name;?></strong>
        <?php if($buildObj->deleted):?>
            <span class='label label-danger'><?php echo $lang->buildex->deleted;?></span>
        <?php endif; ?>
    </div>
    <div class='actions'>
        <?php
        $browseLink = $this->session->buildList ? $this->session->buildList : $this->createLink('project', 'buildex', "projectID=$buildObj->project");
        if(!$buildObj->deleted)
        {
            if($this->config->global->flow != 'onlyTest')
            {
                echo "<div class='btn-group'>";
                if(common::hasPriv('buildex', 'linkStory')) echo html::a(inlink('view', "buildID=$buildObj->id&type=story&link=true"), '<i class="icon-link"></i> ' . $lang->buildex->linkStory, '', "class='btn'");
                if(common::hasPriv('buildex', 'linkBug'))   echo html::a(inlink('view', "buildID=$buildObj->id&type=bug&link=true"), '<i class="icon-bug"></i> ' . $lang->buildex->linkBug, '', "class='btn'");
                echo '</div>';
            }
            echo "<div class='btn-group'>";
            common::printIcon('buildex', 'edit',   "buildID=$buildObj->id", $build);
            common::printIcon('buildex', 'delete', "buildID=$buildObj->id", $build, 'button', '', 'hiddenwin');
            echo '</div>';
        }
        echo common::printRPN($browseLink);
        ?>
    </div>
</div>
<?php
function showPathField($self, $buildData, $fieldName, $hasOption = true, $builderField ="")
{
    $pathFieldName = $fieldName . "Path";
    $pathConfirmFieldName = $fieldName . "PathConfirm";
    $builderFieldName = $fieldName . "Builder";

    if(!empty($builderField)){
        $builderFieldName = $builderField;
    }

    $isBuilder = $self->app->user->account == $buildData->$builderFieldName;

    $operStatus = $buildData->$pathConfirmFieldName;
    $pathVal = $buildData->$pathFieldName;


    $curVal = 0;
    // 有多余产的不需要显示checkbox字段
    if($hasOption) {
        $curVal = $buildData->$fieldName;
        // show field check box
        echo "<tr><th>{$self->lang->buildex->$fieldName}</th>";
        $checkBoxStr = html::checkboxBoolDisplay($fieldName, $curVal);
        echo "<td>{$checkBoxStr}</td></tr>";
    }

    if($curVal)
    {
        if(empty($pathVal)){
            $pathVal = "<label class='placeholder'>{$self->lang->buildex->updatePathNotFound}</label>";
        }
    }
    else
    {
        $pathVal = "<label class='placeholder'>{$self->lang->buildex->updatePathNotReq}</label>";
    }

    // show path val
    echo "<tr><th>{$self->lang->buildex->$pathFieldName}</th>";
    echo "<td>";
    echo "{$pathVal}";

    $statusTxt = $self->lang->buildex->operStatus[$operStatus];// . "debug";

    if($curVal != 0)
    {
        if ($operStatus) {
            echo "<span class='label label-success'>{$statusTxt}</span>";
        } else {
            echo "<span class='label label-danger'>{$statusTxt}</span>";
        }
    }

    if(!empty($pathVal)){
        //echo html::checkboxBoolDisplay($fieldName, 1);
    }

    // 负责人是当前账号就显示更新操作
    if($isBuilder && $curVal != 0) {
        $params = "buildID={$buildData->id}&fieldName={$pathFieldName}&";
        common::printIcon('buildex', 'updatePathInfo',   $params, $buildData, 'list', 'pencil', '', 'iframe', true);
    }

    echo "</td></tr>";
}
?>
<div class='row-table'>
    <div class='col-main'>
        <div class='main'>
            <?php if($this->config->global->flow == 'onlyTest'):?>
                <fieldset>
                    <legend><?php echo $lang->buildex->desc;?></legend>
                    <div class='article-content'><?php echo $buildObj->desc;?></div>
                </fieldset>
                <?php echo $this->fetch('file', 'printFiles', array('files' => $buildObj->files, 'fieldset' => 'true'));?>
                <?php include '../../common/view/action.html.php';?>
                <div class='actions'>
                    <?php
                    $browseLink = $this->session->buildList ? $this->session->buildList : $this->createLink('product', 'buildex', "productID=$buildObj->product");
                    if(!$buildObj->deleted)
                    {
                        common::printIcon('buildex', 'edit',   "buildID=$buildObj->id", $build);
                        common::printIcon('buildex', 'delete', "buildID=$buildObj->id", $build, 'button', '', 'hiddenwin');
                    }
                    echo common::printRPN($browseLink);
                    ?>
                </div>
            <?php else:?>
                <div class='tabs'>
                    <?php $countStories = count($stories); $countBugs = count($bugs); $countNewBugs = count($generatedBugs);?>
                    <ul class='nav nav-tabs'>
                        <li <?php if($type == 'buildInfo') echo "class='active'"?>><a href='#buildInfo' data-toggle='tab'><?php echo html::icon($lang->icons['plan'], 'blue') . ' ' . $lang->buildex->view;?></a></li>
                        <li <?php if($type == 'story')     echo "class='active'"?>><a href='#stories' data-toggle='tab'><?php echo html::icon($lang->icons['story'], 'green') . ' ' . $lang->buildex->stories;?></a></li>
                        <li <?php if($type == 'bug')       echo "class='active'"?>><a href='#bugs' data-toggle='tab'><?php echo html::icon($lang->icons['bug'], 'green') . ' ' . $lang->buildex->bugs;?></a></li>
                        <li <?php if($type == 'newbug')    echo "class='active'"?>><a href='#newBugs' data-toggle='tab'><?php echo html::icon($lang->icons['bug'], 'red') . ' ' . $lang->buildex->generatedBugs;?></a></li>
                    </ul>
                    <div class='tab-content'>
                        <div class='tab-pane <?php if($type == 'story') echo 'active'?>' id='stories'>
                            <?php if(common::hasPriv('buildex', 'linkStory')):?>
                                <div class='action'><?php echo html::a("javascript:showLink($buildObj->id, \"story\")", '<i class="icon-link"></i> ' . $lang->buildex->linkStory, '', "class='btn btn-sm btn-primary'");?></div>
                                <div class='linkBox'></div>
                            <?php endif;?>
                            <form method='post' target='hiddenwin' action='<?php echo inlink('batchUnlinkStory', "buildID={$buildObj->id}")?>' id='linkedStoriesForm'>
                                <table class='table table-hover table-condensed table-striped tablesorter table-fixed table-selectable' id='storyList'>
                                    <?php $vars = "buildID={$buildObj->id}&type=story&link=$link&param=$param&orderBy=%s";?>
                                    <thead>
                                    <tr>
                                        <th class='w-id {sorter:false}'>     <?php common::printOrderLink('id',       $orderBy, $vars, $lang->idAB);?></th>
                                        <th class='w-pri {sorter:false}'>    <?php common::printOrderLink('pri',      $orderBy, $vars, $lang->priAB);?></th>
                                        <th class='text-left {sorter:false}'><?php common::printOrderLink('title',    $orderBy, $vars, $lang->story->title);?></th>
                                        <th class='w-user {sorter:false}'>   <?php common::printOrderLink('openedBy', $orderBy, $vars, $lang->openedByAB);?></th>
                                        <th class='w-hour {sorter:false}'>   <?php common::printOrderLink('estimate', $orderBy, $vars, $lang->story->estimateAB);?></th>
                                        <th class='w-hour {sorter:false}'>   <?php common::printOrderLink('status',   $orderBy, $vars, $lang->statusAB);?></th>
                                        <th class='w-100px {sorter:false}'>  <?php common::printOrderLink('stage',    $orderBy, $vars, $lang->story->stageAB);?></th>
                                        <th class='w-60px {sorter:false}'>   <?php echo $lang->actions?></th>
                                    </tr>
                                    </thead>
                                    <?php $canBatchUnlink = common::hasPriv('buildex', 'batchUnlinkStory');?>
                                    <?php foreach($stories as $storyID => $story):?>
                                        <?php $storyLink = $this->createLink('story', 'view', "storyID=$story->id", '', true);?>
                                        <tr class='text-center'>
                                            <td class='cell-id'>
                                                <?php if($canBatchUnlink):?>
                                                    <input type='checkbox' name='unlinkStories[]'  value='<?php echo $story->id;?>'/>
                                                <?php endif;?>
                                                <?php echo sprintf('%03d', $story->id);?>
                                            </td>
                                            <td><span class='<?php echo 'pri' . zget($lang->story->priList, $story->pri, $story->pri);?>'><?php echo zget($lang->story->priList, $story->pri, $story->pri);?></span></td>
                                            <td class='text-left nobr' title='<?php echo $story->title?>'><?php echo html::a($storyLink,$story->title, '', "class='preview'");?></td>
                                            <td><?php echo $users[$story->openedBy];?></td>
                                            <td><?php echo $story->estimate;?></td>
                                            <td class='story-<?php echo $story->status;?>'><?php echo $lang->story->statusList[$story->status];?></td>
                                            <td><?php echo $lang->story->stageList[$story->stage];?></td>
                                            <td>
                                                <?php
                                                if(common::hasPriv('buildex', 'unlinkStory'))
                                                {
                                                    $unlinkURL = inlink('unlinkStory', "buildID=$buildObj->id&story=$story->id");
                                                    echo html::a("javascript:ajaxDelete(\"$unlinkURL\",\"storyList\",confirmUnlinkStory)", '<i class="icon-unlink"></i>', '', "class='btn-icon' title='{$lang->buildex->unlinkStory}'");
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach;?>
                                    <tfoot>
                                    <tr>
                                        <td colspan='8'>
                                            <div class='table-actions clearfix'>
                                                <?php if($countStories and $canBatchUnlink) echo html::selectButton() . html::submitButton($lang->buildex->batchUnlink);?>
                                                <div class='text'><?php echo sprintf($lang->buildex->finishStories, $countStories);?></div>
                                            </div>
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </form>
                        </div>
                        <div class='tab-pane <?php if($type == 'bug') echo 'active'?>' id='bugs'>
                            <?php if(common::hasPriv('buildex', 'linkBug')):?>
                                <div class='action'><?php echo html::a("javascript:showLink($buildObj->id, \"bug\")", '<i class="icon-bug"></i> ' . $lang->buildex->linkBug, '', "class='btn btn-sm btn-primary'");?></div>
                                <div class='linkBox'></div>
                            <?php endif;?>
                            <form method='post' target='hiddenwin' action="<?php echo inLink('batchUnlinkBug', "build=$buildObj->id");?>" id='linkedBugsForm'>
                                <table class='table table-hover table-condensed table-striped tablesorter table-fixed table-selectable' id='bugList'>
                                    <?php $vars = "buildID={$buildObj->id}&type=bug&link=$link&param=$param&orderBy=%s";?>
                                    <thead>
                                    <tr>
                                        <th class='w-id {sorter:false}'>     <?php common::printOrderLink('id',           $orderBy, $vars, $lang->idAB);?></th>
                                        <th class='text-left {sorter:false}'><?php common::printOrderLink('title',        $orderBy, $vars, $lang->bug->title);?></th>
                                        <th class='w-100px {sorter:false}'>  <?php common::printOrderLink('status',       $orderBy, $vars, $lang->bug->status);?></th>
                                        <th class='w-user {sorter:false}'>   <?php common::printOrderLink('openedBy',     $orderBy, $vars, $lang->openedByAB);?></th>
                                        <th class='w-date {sorter:false}'>   <?php common::printOrderLink('openedDate',   $orderBy, $vars, $lang->bug->openedDateAB);?></th>
                                        <th class='w-user {sorter:false}'>   <?php common::printOrderLink('resolvedBy',   $orderBy, $vars, $lang->bug->resolvedByAB);?></th>
                                        <th class='w-100px {sorter:false}'>  <?php common::printOrderLink('resolvedDate', $orderBy, $vars, $lang->bug->resolvedDateAB);?></th>
                                        <th class='w-60px {sorter:false}'>   <?php echo $lang->actions?></th>
                                    </tr>
                                    </thead>
                                    <?php $canBatchUnlink = common::hasPriv('buildex', 'batchUnlinkBug');?>
                                    <?php foreach($bugs as $bug):?>
                                        <?php $bugLink = $this->createLink('bug', 'view', "bugID=$bug->id", '', true);?>
                                        <tr class='text-center'>
                                            <td class='cell-id'>
                                                <?php if($canBatchUnlink):?>
                                                    <input type='checkbox' name='unlinkBugs[]'  value='<?php echo $bug->id;?>'/>
                                                <?php endif;?>
                                                <?php echo sprintf('%03d', $bug->id);?>
                                            <td class='text-left nobr' title='<?php echo $bug->title?>'><?php echo html::a($bugLink, $bug->title, '', "class='preview'");?></td>
                                            <td class='bug-<?php echo $bug->status?>'><?php echo $lang->bug->statusList[$bug->status];?></td>
                                            <td><?php echo $users[$bug->openedBy];?></td>
                                            <td><?php echo substr($bug->openedDate, 5, 11)?></td>
                                            <td><?php echo $users[$bug->resolvedBy];?></td>
                                            <td><?php echo substr($bug->resolvedDate, 5, 11)?></td>
                                            <td>
                                                <?php
                                                if(common::hasPriv('buildex', 'unlinkBug'))
                                                {
                                                    $unlinkURL = inlink('unlinkBug', "buildID=$buildObj->id&bug=$bug->id");
                                                    echo html::a("javascript:ajaxDelete(\"$unlinkURL\",\"bugList\",confirmUnlinkBug)", '<i class="icon-unlink"></i>', '', "class='btn-icon' title='{$lang->buildex->unlinkBug}'");
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach;?>
                                    <tfoot>
                                    <tr>
                                        <td colspan='8'>
                                            <div class='table-actions clearfix'>
                                                <?php if($countBugs and $canBatchUnlink) echo html::selectButton() . html::submitButton($lang->buildex->batchUnlink);?>
                                                <div class='text'><?php echo sprintf($lang->buildex->resolvedBugs, $countBugs);?></div>
                                            </div>
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </form>
                        </div>
                        <div class='tab-pane <?php if($type == 'newbug') echo 'active'?>' id='newBugs'>
                            <table class='table table-hover table-condensed table-striped tablesorter table-fixed'>
                                <?php $vars = "buildID={$buildObj->id}&type=newbug&link=$link&param=$param&orderBy=%s";?>
                                <thead>
                                <tr>
                                    <th class='w-id {sorter:false}'>      <?php common::printOrderLink('id',           $orderBy, $vars, $lang->idAB);?></th>
                                    <th class='w-severity {sorter:false}'><?php common::printOrderLink('severity',     $orderBy, $vars, $lang->bug->severityAB);?></th>
                                    <th class='text-left {sorter:false}'> <?php common::printOrderLink('title',        $orderBy, $vars, $lang->bug->title);?></th>
                                    <th class='w-100px {sorter:false}'>   <?php common::printOrderLink('status',       $orderBy, $vars, $lang->bug->status);?></th>
                                    <th class='w-user {sorter:false}'>    <?php common::printOrderLink('openedBy',     $orderBy, $vars, $lang->openedByAB);?></th>
                                    <th class='w-date {sorter:false}'>    <?php common::printOrderLink('openedDate',   $orderBy, $vars, $lang->bug->openedDateAB);?></th>
                                    <th class='w-user {sorter:false}'>    <?php common::printOrderLink('resolvedBy',   $orderBy, $vars, $lang->bug->resolvedByAB);?></th>
                                    <th class='w-100px {sorter:false}'>   <?php common::printOrderLink('resolvedDate', $orderBy, $vars, $lang->bug->resolvedDateAB);?></th>
                                </tr>
                                </thead>
                                <?php foreach($generatedBugs as $bug):?>
                                    <?php $bugLink = $this->createLink('bug', 'view', "bugID=$bug->id", '', true);?>
                                    <tr class='text-center'>
                                        <td><?php echo sprintf('%03d', $bug->id);?></td>
                                        <td><span class='severity<?php echo zget($lang->bug->severityList, $bug->severity, $bug->severity)?>'><?php echo zget($lang->bug->severityList, $bug->severity, $bug->severity);?></span></td>
                                        <td class='text-left nobr' title='<?php echo $bug->title?>'><?php echo html::a($bugLink, $bug->title, '', "class='preview'");?></td>
                                        <td class='bug-<?php echo $bug->status?>'><?php echo $lang->bug->statusList[$bug->status];?></td>
                                        <td><?php echo $users[$bug->openedBy];?></td>
                                        <td><?php echo substr($bug->openedDate, 5, 11)?></td>
                                        <td><?php echo $users[$bug->resolvedBy];?></td>
                                        <td><?php echo substr($bug->resolvedDate, 5, 11)?></td>
                                    </tr>
                                <?php endforeach;?>
                                <tfoot>
                                <tr>
                                    <td colspan='8'>
                                        <div class='table-actions clearfix'>
                                            <div class='text'><?php echo sprintf($lang->buildex->createdBugs, $countNewBugs);?></div>
                                        </div>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class='tab-pane <?php if($type == 'buildInfo') echo 'active'?>' id='buildInfo'>
                            <div class="row-table">
                                <div class='col-main'>
                                    <fieldset>
                                        <legend><?php echo $lang->buildex->basicInfo?></legend>
                                        <table class='table table-data table-condensed table-borderless table-fixed'>
                                            <tr>
                                                <th class='w-150px'><?php echo $lang->buildex->product;?></th>
                                                <?php if($products):?>
                                                    <td>
                                                        <div class='input-group'>
                                                            <?php echo $products[$product->id];?>
                                                            <?php
                                                            if($product->type != 'normal')
                                                            {
                                                                if($product->branch) $branches = array($product->branch => $branches[$product->branch]);
                                                                echo "<span class='input-group-addon fix-padding fix-border'></span>" . html::select('branch', $branches, $product->branch, "class='form-control' style='width:100px; display:inline-block;'");
                                                            }
                                                            ?>
                                                        </div>
                                                    </td>
                                                    <td></td>
                                                <?php else:?>
                                                    <td colspan='2'><?php if(empty($products)) printf($lang->buildex->noProduct, $this->createLink('project', 'manageproducts', "projectID=$projectID&from=buildCreate"));?></td>
                                                <?php endif;?>
                                            </tr>
                                            <tr>
                                                <th><?php echo $lang->buildex->name;?></th>
                                                <td><?php echo $buildObj->name;?></td>
                                            </tr>

                                            <tr>
                                                <th><?php echo $lang->buildex->shippingType;?></th>
                                                <td><?php echo $lang->buildex->shippingTypeList[$buildObj->shippingType];?></td>
                                            </tr>
                                            <tr>
                                                <th><?php echo $lang->buildex->date;?></th>
                                                <td><?php echo $buildObj->date;?></td>
                                            </tr>
                                            <tr>
                                                <th><?php echo $lang->buildex->svnTagOperator;?></th>
                                                <td><?php echo $users[$buildObj->svnTagOperator];?></td>
                                            </tr>
                                            <tr>
                                                <th><?php echo $lang->buildex->srcSVNPath;?></th>
                                                <td><?php echo $buildObj->srcSVNPath?></td>
                                            </tr>
                                            <tr>
                                                <th><?php echo $lang->buildex->desc;?></th>
                                                <td colspan='2'><?php echo $buildObj->desc;?></td>
                                            </tr>
                                        </table>
                                    </fieldset>


                                    <fieldset>
                                        <legend><?php echo $lang->buildex->buildServerOption; ?></legend>
                                        <table class='table table-data table-condensed table-borderless table-fixed'>

                                            <tr>
                                                <th class='w-150px'><?php echo $lang->buildex->buildServerImageBuilder;?></th>
                                                <td><?php echo $users[$buildObj->buildServerImageBuilder];?></td>
                                            </tr>
                                            <?php showPathField($this, $buildObj, 'buildServerImage') ?>
                                        </table>
                                    </fieldset>


                                    <fieldset>
                                        <legend><?php echo $lang->buildex->buildAndroidPackage;?></legend>
                                        <table class='table table-data table-condensed table-borderless table-fixed'>
                                            <tr>
                                                <th class='w-150px'><?php echo $lang->buildex->buildApkBuilder;?></th>
                                                <td><?php echo $users[$buildObj->buildApkBuilder];?></td>
                                            </tr>
                                            <?php showPathField($this, $buildObj, 'buildApk') ?>
                                            <?php showPathField($this, $buildObj, 'buildApkPathRelease', false, 'buildApkBuilder') ?>

                                        </table>
                                    </fieldset>

                                    <fieldset>
                                        <legend><?php echo $lang->buildex->buildiOSPackage;?></legend>
                                        <table class='table table-data table-condensed table-borderless table-fixed'>
                                            <tr>
                                                <th class='w-150px'><?php echo $lang->buildex->buildIosBuilder;?></th>
                                                <td><?php echo $users[$buildObj->buildIosBuilder];?></td>
                                            </tr>
                                            <?php showPathField($this, $buildObj, 'buildIos') ?>
                                        </table>
                                    </fieldset>


                                    <fieldset>
                                        <legend><?php echo $lang->buildex->buildHotpatch;?></legend>
                                        <table class='table table-data table-condensed table-borderless table-fixed'>
                                            <tr>
                                                <th class='w-150px'><?php echo $lang->buildex->buildHotpatchBuilder;?></th>
                                                <td><?php echo $users[$buildObj->buildHotpatchBuilder];?></td>
                                            </tr>

                                            <?php showPathField($this, $buildObj, 'buildHotpatchAndroid', true, 'buildHotpatchBuilder') ?>
                                            <?php showPathField($this, $buildObj, 'buildHotpatchiOS', true, 'buildHotpatchBuilder'); ?>
                                        </table>
                                    </fieldset>

                                    <fieldset>
                                        <legend><?php echo $lang->buildex->qaOption; ?></legend>
                                        <table class='table table-data table-condensed table-borderless table-fixed'>
                                            <tr>
                                                <th class='w-150px'><?php echo $lang->buildex->qaSmoke;?></th>
                                                <td><?php echo html::checkboxBoolDisplay('qaSmoke', $buildObj->qaSmoke);?></td>
                                            </tr>
                                            <tr>
                                                <th><?php echo $lang->buildex->qaSmokeFull;?></th>
                                                <td><?php echo html::checkboxBoolDisplay('qaSmokeFull', $buildObj->qaSmokeFull);?></td>
                                            </tr>
                                            <tr>
                                                <th><?php echo $lang->buildex->qaFunction;?></th>
                                                <td><?php echo html::checkboxBoolDisplay('qaFunction', $buildObj->qaFunction);?></td>

                                            </tr>
                                            <tr>
                                                <th><?php echo $lang->buildex->qaFull;?></th>
                                                <td><?php echo html::checkboxBoolDisplay('qaFull', $buildObj->qaFull);?></td>
                                            </tr>
                                            <tr>
                                                <th><?php echo $lang->buildex->qaRegTesting;?></th>
                                                <td><?php echo html::checkboxBoolDisplay('qaRegTesting', $buildObj->qaRegTesting);?></td>
                                            </tr>
                                        </table>
                                    </fieldset>
                                    <?php if($this->config->global->flow != 'onlyTest'):?>
                                        <?php echo $this->fetch('file', 'printFiles', array('files' => $buildObj->files, 'fieldset' => 'true'));?>
                                    <?php endif;?>
                                </div>
                                <div class="col-side"><?php include '../../common/view/action.html.php';?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif;?>
        </div>
    </div>
</div>
<?php if($this->config->global->flow != 'onlyTest'):?>
    <?php js::set('param', helper::safe64Decode($param))?>
    <?php js::set('link', $link)?>
    <?php js::set('buildID', $buildObj->id)?>
    <?php js::set('type', $type)?>
<?php endif;?>
<?php include '../../common/view/footer.html.php';?>
