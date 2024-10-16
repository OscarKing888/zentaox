<?php
/**
 * The story view file of project module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     project
 * @version     $Id: story.html.php 5117 2013-07-12 07:03:14Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php $canOrder = false;//common::hasPriv('project', 'storySort'); ?>
<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/tablesorter.html.php'; ?>
<?php if ($canOrder) include '../../common/view/sortable.html.php'; ?>
<?php js::set('moduleID', ($type == 'byModule' ? $param : 0)); ?>
<?php js::set('productID', ($type == 'byProduct' ? $param : 0)); ?>
<?php js::set('branchID', ($type == 'byBranch' ? (int)$param : '')); ?>
<?php js::set('confirmUnlinkStory', $lang->project->unlinkMilestoneStory) ?>
<div id='featurebar'>
    <ul class='nav'>
        <li class='active'>
            <?php
            // projectID=$projectID&orderby=order_desc&type=byMilestone&param=$milestone
            if (common::hasPriv('project', 'productMilestone'))
                echo html::a($this->createLink('project', 'productMilestone', "project=$project->id&orderby=desc&type=byMilestone&param=$milestone"), $lang->project->milestone);
            ?>
        </li>

    </ul>
    <div class='actions'>
        <div class='btn-group'>
            <?php

            common::printIcon('project', 'exportProgress', "productID=$productID&projectID=$project->id&orderBy=id_desc", '', 'button', ' icon-download-alt', '', 'export');

            $this->lang->story->create = $this->lang->project->createStory;

            if($milestone != 0)
            {
                echo $milestoneData->name;
                common::printIcon('project', 'linkMilestoneStory',
                    "project=$project->id&milestone=$milestone", '', 'button', 'link', '',
                    'link-story-btn', false, '', '关联需求');
            }
            else
            {
                echo "请先在左边列表选择里程碑再关联";
            }
            ?>
            <?php
            //echo $debugMsg;
            //echo html::select("milestones", $milestones, 0, 'class=form-control chosen');
            ?>

        </div>
    </div>
    <div id='querybox' class='show'></div>
</div>

<div class='side' id='taskTree'>
    <a class='side-handle' data-id='projectTree'><i class='icon-caret-left'></i></a>
    <div class='side-body'>
        <div class='panel panel-sm'>
            <div class='panel-heading nobr'>
                <?php echo html::icon($lang->icons['project']); ?> <strong><?php echo $project->name ; ?></strong>
            </div>
            <div class='panel-body'>
                <?php
                //echo $moduleTree;
                ?>
                <ul class='nav'>

                    <?php

                    $totalProgress = 0;
                    $taskCnt = count($stories);

                    foreach ($stories as $key => $story)
                    {
                        $totalProgress += $story->taskProgress;
                    }

                    $totalProgress = $taskCnt > 0 ? $totalProgress / $taskCnt : 0;
                    $totalProgress = round($totalProgress);
                    $totalProgress = max(0, min(100, $totalProgress));

                    ?>

                    <?php foreach ($milestones as $key => $val): ?>

                        <li>
                            <?php
                                if($milestone == $key)
                                    {
                                        $linkCls= "class=activeText";
                                        $this->app->session->set("milestoneProg_$key", $totalProgress);
                                    }
                                else
                                    $linkCls= "";
                            ?>

                            <?php
                            //echo $milestone
                            $progKey = "milestoneProg_". $key;
                            $progTotal = $this->app->session->$progKey;
                            if(!isset($progTotal))
                            {
                                $progTotal = 0;
                            }

                            common::printLink('project', 'productMilestone',
                                "projectID=$project->id&&orderBy=$orderBy&$type=byMilestone&$param=$key",
                                $val . " [" . $progTotal . "%]", '', $linkCls);
                            ?>

                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class='text-right'>
                    <?php
                    //common::printLink('tree', 'browsetask', "rootID=$projectID&productID=$productID", $lang->tree->manage);
                    ?>

                    <?php
                    common::printLink('project', 'productMilestonesManage', "projectID=$project->id", $lang->project->manageMilestones);
                    ?>                    
                </div>
            </div>
        </div>
    </div>
</div>
<div class='main'>
    <script>setTreeBox();</script>
    <table class='table'>
        <thead>
        <tr class='colhead'>
            <th align="center"><?php echo $lang->project->milestone ?></th>
            <th align="center"><?php echo $lang->project->deadlineRequirement ?></th>
            <th align="center"><?php echo $lang->project->deadline ?></th>
            <th align="center"><?php echo $lang->project->deadlineQA ?></th>
            <th align="center"><?php echo $lang->project->opOnlineDate ?></th>
        </tr>
        </thead>
        <tr>
            <td class="red" align="center" bgcolor1="#eeee88">
                <?php if($milestone != 0){ echo $milestoneData->name;} ?>
            </td>

            <td class="red" align="center" bgcolor1="#eeee88">
                <?php if($milestone != 0){ echo date('Y年 m月 d日', strtotime($milestoneData->deadlineRequirement));} ?>
            </td>

            <td class="red" align="center" bgcolor1="#eeee88">
                <?php if($milestone != 0){ echo date('Y年 m月 d日', strtotime($milestoneData->deadline));} ?>
            </td>

            <td class="red" align="center" bgcolor1="#eeee88">
                <?php if($milestone != 0){ echo date('Y年 m月 d日', strtotime($milestoneData->deadlineQA));} ?>
            </td>

            <td class="red" align="center" bgcolor1="#eeee88">
                <?php if($milestone != 0){ echo date('Y年 m月 d日', strtotime($milestoneData->opOnlineDate));} ?>
            </td>
        </tr>
    </table>
    <form method='post' id='projectStoryForm'>
        <table class='table tablesorter table-condensed table-fixed table-selectable' id='storyList'>
            <thead>
            <tr class='colhead'>
                <?php $vars = "projectID={$project->id}&orderBy=%s&type=$type&param=$param&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"; ?>
                <th class='w-id  {sorter:false}'>  <?php common::printOrderLink('id', $orderBy, $vars, $lang->idAB); ?></th>
                <th class='w-pri {sorter:false}'>  <?php common::printOrderLink('pri', $orderBy, $vars, $lang->priAB); ?></th>
                <th class='{sorter:false}'>        <?php common::printOrderLink('title', $orderBy, $vars, $lang->story->title); ?></th>
                <th class='w-user {sorter:false}'> <?php common::printOrderLink('openedBy', $orderBy, $vars, $lang->openedByAB); ?></th>
                <th class='w-80px {sorter:false}'> <?php common::printOrderLink('assignedTo', $orderBy, $vars, $lang->assignedToAB); ?></th>

                <th title='<?php echo $lang->story->taskCount ?>'
                    class='w-30px'><?php echo $lang->story->taskCountAB; ?></th>
                <th title='<?php echo $lang->story->bugCount ?>'
                    class='w-30px'><?php echo $lang->story->bugCountAB; ?></th>
                <th title='<?php echo $lang->story->caseCount ?>'
                    class='w-30px'><?php echo $lang->story->caseCountAB; ?></th>
                <th class='w-120px'>  <?php echo $lang->task->progress;?></th>
                <th class='w-120px'>  <?php echo $lang->task->progressOfStory;?></th>
                <th class='w-210px {sorter:false}'><?php echo $lang->actions; ?></th>
            </tr>
            </thead>
            <tbody id='storyTableList' class='sortable'>
            <?php
            $totalEstimate = 0;
            $canBatchEdit = common::hasPriv('story', 'batchEdit');
            $canBatchClose = common::hasPriv('story', 'batchClose');
            ?>
            <?php foreach ($stories as $key => $story): ?>
                <?php
                $storyLink = $this->createLink('story', 'view', "storyID=$story->id&version=$story->version&from=project&param=$project->id");
                $totalEstimate += $story->estimate;
                ?>
                <tr class='text-center' id="story<?php echo $story->id; ?>" data-id='<?php echo $story->id; ?>'
                    data-order='<?php echo $story->order ?>' data-estimate='<?php echo $story->estimate ?>'
                    data-cases='<?php echo zget($storyCases, $story->id, 0) ?>'>
                    <td class='cell-id'>
                        <?php if ($canBatchEdit or $canBatchClose): ?>
                            <input type='checkbox' name='storyIDList[<?php echo $story->id; ?>]'
                                   value='<?php echo $story->id; ?>'/>
                        <?php endif; ?>
                        <?php echo html::a($storyLink, sprintf('%03d', $story->id)); ?>
                    </td>
                    <td>
                        <span class='<?php echo 'pri' . zget($lang->story->priList, $story->pri, $story->pri) ?>'><?php echo zget($lang->story->priList, $story->pri, $story->pri); ?></span>
                    </td>
                    <td class='text-left' title="<?php echo $story->title ?>">
                        <?php if (isset($branchGroups[$story->product][$story->branch])) echo "<span class='label label-info label-badge'>" . $branchGroups[$story->product][$story->branch] . '</span>'; ?>
                        <?php echo html::a($storyLink, $story->title, null, "style='color: $story->color'"); ?>
                    </td>
                    <td><?php echo $users[$story->openedBy]; ?></td>
                    <td><?php echo $users[$story->assignedTo]; ?></td>

                    <td class='linkbox'>
                        <?php
                        $tasksLink = $this->createLink('story', 'tasks', "storyID=$story->id&projectID=$project->id");
                        $storyTasks[$story->id] > 0 ? print(html::a($tasksLink, $storyTasks[$story->id], '', 'class="iframe"')) : print(0);
                        ?>
                    <td>
                        <?php
                        $bugsLink = $this->createLink('story', 'bugs', "storyID=$story->id&projectID=$project->id");
                        $storyBugs[$story->id] > 0 ? print(html::a($bugsLink, $storyBugs[$story->id], '', 'class="iframe"')) : print(0);
                        ?>
                    </td>
                    <td>
                        <?php
                        $casesLink = $this->createLink('story', 'cases', "storyID=$story->id&projectID=$project->id");
                        $storyCases[$story->id] > 0 ? print(html::a($casesLink, $storyCases[$story->id], '', 'class="iframe"')) : print(0);
                        ?>
                    </td>

                    <td>
                        <div class=<?php echo $story->taskProgress == 100 ? 'progressCompleted' : 'progressInCompleted' ?> >
                            <?php echo $story->taskProgress . "%";?>
                        </div>
                    </td>

                    <td>
                        <div class=<?php echo $story->storyProgress == 100 ? 'progressCompleted' : 'progressInCompleted' ?> >
                            <?php echo $story->storyProgress . "%";?>
                        </div>
                    </td>

                    <td>
                        <?php
                        $hasDBPriv = common::hasDBPriv($project, 'project');
                        $param = "projectID={$project->id}&story={$story->id}&moduleID={$story->module}";

                        //oscar:
                        $storyID = $story->id;
                        $assignedTo = $story->assignedTo;
                        $storyTitle = baseModel::trimTitle($story->title);
                        $storyModuleID = $story->module;
                        $productID = $story->product;
                        $createBugParams = "productID=$productID&branch=$story->branch&extras=storyID=$storyID,assignedTo=$assignedTo,title=$storyTitle,moduleID=$storyModuleID";
                        common::printIcon('bug', 'create', $createBugParams, '', 'list', 'bug', '_blank');

                        $lang->task->batchCreate = $lang->project->batchWBSRoot;
                        common::printIcon('task', 'batchCreateRoot', "projectID={$project->id}&story={$story->id}", '', 'list', 'flag');
                        //oscar:

                        // oscar:=======================
                        //echo "<table><tr><td>";
                        //echo "<div class='btn-group dropup'>";
                        //echo html::select("batchCreateRootTask[$story->id]", $pipeline, 0, 'class="hiddens"');

                        $pipeline[-1] = "无";
                        echo html::select("batchCreateRootTask_$story->id", $pipeline, -1, "onchange=\"on_createRootTask('$story->project', '$story->id', '$story->title', '$productID')\"");

                        /*
                        echo "<div class='btn-group'>";
                        echo "<button type='button' class='btn dropdown-toggle' data-toggle='dropdown'>"
                            . $this->lang->task->batchCreateChildTask . "<span class='caret'></span></button>";

                        echo "<ul class='dropdown-menu pull-right'>";
                        //echo html::select("batchCreateRootTask[$story->id]", $pipeline, 0, "class='hidden' onclick=\"setFormAction('$actionLink','hiddenwin')\"" );

                        //echo '<ul class="dropdown-list">';
                        foreach ($pipeline as $key => $value) {
                            //error_log("oscar: pipeline $key -> $value");
                            //if (empty($key)) continue;
                            $actionLink = helper::createLink('pipeline', 'batchCreateRootTask', "projectID=$story->project&pipelineId=$key&storyID=$story->id");
                            //echo "<li class='option' data-key='$key'>" . html::a("javascript:$(\"#batchCreateChildTask[$task->id]\").val(\"$key\");setFormAction(\"$actionLink\", \"hiddenwin\")", $value, '', '') . '</li>';
                            echo "<li>" . html::a($actionLink, $value, '', "") . '</li>';
                            echo "<li>" . html::a('#', $value, '', "onclick=\"setFormAction('$actionLink','hiddenwin')\"") . "</li>";
                            //echo "<li>" . $value . '</li>';
                        }
                        //echo "</ul>";

                        echo "</ul>";
                        //echo "</div>";
                        echo "</div>";
                        //*/

                        /*
                        echo "<div class='btn-group dropup clearfix'>";
                        echo "<ul>";
                        foreach ($pipeline as $key => $value) {
                            $actionLink = helper::createLink('pipeline', 'batchCreateChildTask', "projectID=$task->project&pipelineId=$key&rootTaskID=$task->id");
                            echo "<li>" . html::a($actionLink, $value, '') . '</li>';
                        }
                        echo "</ul>";
                        echo "</div>";
                        //*/
                        //echo "</td></tr></table>";
                        // oscar:=======================

                        /* oscar:
                        $lang->task->create = $lang->project->wbs;
                        if(commonModel::isTutorialMode())
                        {
                            $wizardParams = helper::safe64Encode($param);
                            echo html::a($this->createLink('tutorial', 'wizard', "module=task&method=create&params=$wizardParams"), "<i class='icon-plus-border'></i>",'', "class='btn-icon btn-task-create' title='{$lang->project->wbs}'");
                        }
                        else
                        {
                            if($hasDBPriv) common::printIcon('task', 'create', $param, '', 'list', 'plus-border', '', 'btn-task-create');
                        }
                        //*/

                        //oscar: $lang->task->batchCreate = $lang->project->batchWBS;
                        if ($hasDBPriv) {
                            //oscar: common::printIcon('task', 'batchCreate', "projectID={$project->id}&story={$story->id}", '', 'list', 'plus-sign');
                        }

                        $lang->testcase->batchCreate = $lang->testcase->create;
                        //if ($productID && $hasDBPriv) common::printIcon('testcase', 'batchCreate', "productID=$story->product&branch=$story->branch&moduleID=$story->module&storyID=$story->id", '', 'list', 'sitemap');

                        if (common::hasPriv('project', 'unlinkMilestoneStory', $project)) {
                            $unlinkURL = $this->createLink('project', 'unlinkMilestoneStory', "projectID=$project->id&storyID=$story->id&milestone=$milestone&confirm=yes");
                            //echo html::a("javascript:ajaxDelete(\"$unlinkURL\",\"storyList\",unlinkMilestoneStoryConfirm)", '<i class="icon-unlink"></i>', '', "class='btn-icon' title='{$lang->project->unlinkMilestoneStory}'");
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan='<?php echo 11; ?>'>
                    <div class='table-actions clearfix'>
                        <?php
                        $storyInfo = sprintf($lang->project->productMilestoneStories, inlink('productMilestonesManage', "project={$project->id}"));
                        if (count($stories)) {
                            if ($canBatchEdit or $canBatchClose) echo html::selectButton();

                            echo "<div class='btn-group dropup'>";
//                            if ($canBatchEdit) {
//                                $actionLink = $this->createLink('story', 'batchEdit', "productID=0&projectID=$project->id");
//                                echo html::commonButton($lang->edit, "onclick=\"setFormAction('$actionLink')\"");
//                            }
                            if (common::hasPriv('project', 'batchUnlinkMilestoneStory')) {
                                $actionLink = $this->createLink('project', 'batchUnlinkMilestoneStory', "projectID=$project->id&milestone=$milestone");
                                $misc = "onclick=\"setFormAction('$actionLink')\"";
                                //echo '<li>' . html::a('#', $lang->project->unlinkMilestoneStory, '', $misc) . '</li>';
                                echo html::linkButton($lang->project->unlinkStory, '#', '', $misc);
                            }


                            echo "<button id='moreAction' type='button' class='btn dropdown-toggle' data-toggle='dropdown'><span class='caret'></span></button>";
                            echo "<ul class='dropdown-menu' id='moreActionMenu'>";
                            if ($canBatchClose) {
                                $actionLink = $this->createLink('story', 'batchClose', "productID=0&projectID=$project->id");
                                $misc = "onclick=\"setFormAction('$actionLink')\"";
                                echo '<li>' . html::a('#', $lang->close, '', $misc) . '</li>';
                            }

                            if (common::hasPriv('story', 'batchChangeStage')) {
                                echo "<li class='dropdown-submenu'>";
                                echo html::a('javascript:;', $lang->story->stageAB, '', "id='stageItem'");
                                echo "<ul class='dropdown-menu'>";
                                $lang->story->stageList[''] = $lang->null;
                                foreach ($lang->story->stageList as $key => $stage) {
                                    $actionLink = $this->createLink('story', 'batchChangeStage', "stage=$key");
                                    echo "<li>" . html::a('#', $stage, '', "onclick=\"setFormAction('$actionLink','hiddenwin')\"") . "</li>";
                                }
                                echo '</ul></li>';
                            }


                            echo '</ul></div>';
                            $storyInfo = $summary;
                        }

                        $memberPairs = $deptUsers;
                        $actionLink = $this->createLink('story', 'batchAssignTo', "productID=$productID");

                        echo "<div class='btn-group dropup'>";
                        echo "<button id='taskBatchAssignTo' type='button' class='btn dropdown-toggle' data-toggle='dropdown'>" . $lang->story->assignedTo . "<span class='caret'></span></button>";
                        echo "<ul class='dropdown-menu' id='taskBatchAssignToMenu'>";
                        echo html::select('assignedTo', $memberPairs, '', 'class="hidden"');

                        echo '<ul class="dropdown-list">';
                        foreach ($memberPairs as $key => $value) {
                            if (empty($key)) continue;
                            //$actionLink = $this->createLink('story', 'batchAssignTo', "productID=$productID");
                            echo "<li>" . html::a("javascript:$(\"#assignedTo\").val(\"$key\");setFormAction(\"$actionLink\", \"hiddenwin\" )", $value, '', '') . '</li>';
                        }
                        echo "</ul>";
                        //if ($withSearch) echo "<div class='menu-search'><div class='input-group input-group-sm'><input type='text' class='form-control' placeholder=''><span class='input-group-addon'><i class='icon-search'></i></span></div></div>";
                        echo "</div></li>";
                        echo "</ul>";

                        {
                            $actionLink = $this->createLink('story', 'batchChangePriority', "productID=$productID");
                            $priList = (array)$this->lang->story->priList;
                            echo "<div class='btn-group dropup'>";
                            echo "<button id='storyBatchChangePriority' type='button' class='btn dropdown-toggle' data-toggle='dropdown'>" . $lang->story->batchChangePriorityAB . "<span class='caret'></span></button>";
                            echo "<ul class='dropdown-menu' id='storyBatchChangePriorityMenu'>";
                            echo html::select('pri', $priList, '', 'class="hidden"');

                            echo '<ul class="dropdown-list">';
                            foreach ($priList as $key => $value) {
                                if (empty($key)) continue;
                                //$actionLink = $this->createLink('story', 'batchAssignTo', "productID=$productID");
                                echo "<li class='option' data-key='$key'>" . html::a("javascript:$(\"#pri\").val(\"$key\");setFormAction(\"$actionLink\", \"hiddenwin\" )", $value, '', '') . '</li>';
                            }
                            echo "</ul>";
                            //if ($withSearch) echo "<div class='menu-search'><div class='input-group input-group-sm'><input type='text' class='form-control' placeholder=''><span class='input-group-addon'><i class='icon-search'></i></span></div></div>";
                            echo "</div></li>";
                            echo "</ul>";
                        }


                        if (common::hasPriv('testtask', 'createTestTask')) {
                            $actionLink = $this->createLink('testtask', 'createTestTask', "productID=$productID&milestone=$milestone");
                            $misc = "onclick=\"setFormAction('$actionLink')\"";
                            echo html::linkButton($lang->project->createTestTask, '#', '', $misc);
                            echo html::select('createTestTaskTag', $priList, '', 'class="hidden"');
                        }


                        echo "<div class='text'>{$storyInfo}</div>";
                        ?>
                    </div>
                    <?php echo $pager->show(); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
<?php js::set('checkedSummary', $lang->product->checkedSummary); ?>
<?php js::set('projectID', $project->id); ?>
<?php js::set('orderBy', $orderBy) ?>
<script>
    $('#modulemenu .nav li[data-id=<?php echo $browseType?>]').addClass('active');
</script>
<?php include '../../common/view/footer.html.php'; ?>
