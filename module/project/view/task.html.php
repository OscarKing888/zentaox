<?php
/**
 * The task view file of project module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     project
 * @version     $Id: task.html.php 4894 2013-06-25 01:28:39Z wyd621@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php
include '../../common/view/header.html.php';
include '../../common/view/chart.html.php';
include '../../common/view/datepicker.html.php';
include '../../common/view/datatable.fix.html.php';
include './taskheader.html.php';
js::set('moduleID', $moduleID);
js::set('productID', $productID);
js::set('projectID', $projectID);
js::set('browseType', $browseType);
//echo "status:$status browseType:$browseType moduleID:$moduleID";
?>

<!--<div class='side' id='taskTree'>
    <a class='side-handle' data-id='projectTree'><i class='icon-caret-left'></i></a>
    <div class='side-body'>
        <div class='panel panel-sm'>
            <div class='panel-heading nobr'>
                <?php /*echo html::icon($lang->icons['project']); */?> <strong><?php /*echo $project->name; */?></strong>
            </div>
            <div class='panel-body'>
                <?php
/*                echo $moduleTree;
                */?>
                <div class='text-right'>
                    <?php /*common::printLink('project', 'edit', "projectID=$projectID", $lang->edit); */?>

                    <?php /*common::printLink('tree', 'browsetask', "rootID=$projectID&productID=0", $lang->tree->manage); */?>
                </div>
            </div>
        </div>
    </div>
</div>-->

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
                    <?php foreach ($milestones as $key => $val): ?>

                        <li>
                            <?php
                            if($milestone == $key)
                                $linkCls= "class=activeText";
                            else
                                $linkCls= "";
                            ?>

                            <?php
                            //echo $milestone
                            $taskBrowseType = isset($status) ? $this->session->taskBrowseType : '';
                            common::printLink('project', 'task',
                                "projectID=$project->id&status=$taskBrowseType&$param=$key&moduleType=byMilestone&&orderBy=$orderBy",
                                $val, '', $linkCls);
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
    <form method='post' id='projectTaskForm'>
        <?php
        $datatableId = $this->moduleName . ucfirst($this->methodName);
        $useDatatable = (isset($this->config->datatable->$datatableId->mode) and $this->config->datatable->$datatableId->mode == 'datatable');
        $vars = "projectID=$project->id&status=$status&parma=$param&moduleType=byMilestone&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage";

        if ($useDatatable) include '../../common/view/datatable.html.php';
        $customFields = $this->datatable->getSetting('project');
        if ($project->type == 'ops') {
            foreach ($customFields as $id => $customField) {
                if ($customField->id == 'story') unset($customFields[$id]);
            }
        }
        $widths = $this->datatable->setFixedFieldWidth($customFields);
        $columns = 0;
        ?>
        <table class='table table-condensed table-hover table-striped tablesorter table-fixed <?php echo($useDatatable ? 'datatable' : 'table-selectable'); ?> table-selectable'
               id='taskList' data-checkable='true' data-fixed-left-width='<?php echo $widths['leftWidth'] ?>'
               data-fixed-right-width='<?php echo $widths['rightWidth'] ?>' data-custom-menu='true'
               data-checkbox-name='taskIDList[]'>
            <thead>
            <tr>
                <?php
                foreach ($customFields as $field) {
                    if ($field->show) {
                        $this->datatable->printHead($field, $orderBy, $vars);
                        $columns++;
                    }
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($tasks as $task): ?>
                <?php
                $taskStatus = 'wait';
                //echo $child->status
                if(!$task->checkedStatus)
                {
                    if($task->status == 'done'){
                        $taskStatus = 'wait_check';
                    }
                    else {
                        $taskStatus = $task->status;
                    }                        }
                else
                {
                    $taskStatus = 'checked';
                }
                ?>

                <tr class='text-center' data-id='<?php echo $task->id; ?>' data-status='<?php echo $taskStatus ?>'
                    data-estimate='<?php echo $task->estimate ?>' data-consumed='<?php echo $task->consumed ?>'
                    data-left='<?php echo $task->left ?>'>
                    <?php foreach ($customFields as $field) $this->task->printCell($field, $task, $users, $browseType, $branchGroups, $modulePairs, $useDatatable ? 'datatable' : 'table', false, $depts, $pipeline, $versions); ?>
                </tr>
                <?php if (!empty($task->children)): ?>
                    <?php foreach ($task->children as $key => $child): ?>
                        <?php $class = $key == 0 ? ' table-child-top' : ''; ?>
                        <?php $class .= ($key + 1 == count($task->children)) ? ' table-child-bottom' : ''; ?>
                        <tr class='text-center table-children<?php echo $class; ?> parent-<?php echo $task->id; ?>'
                            <?php
                            $taskStatus = 'wait';
                            //echo $child->status
                            if(!$child->checkedStatus)
                            {
                                if($child->status == 'done'){
                                    $taskStatus = 'wait_check';
                                }
                                else {
                                    $taskStatus = $child->status;
                                }                        }
                            else
                            {
                                $taskStatus = 'checked';
                            }
                            ?>

                            data-id='<?php echo $child->id ?>' data-status='<?php echo $taskStatus; ?>'
                            data-estimate='<?php echo $child->estimate ?>'
                            data-consumed='<?php echo $child->consumed ?>' data-left='<?php echo $child->left ?>'>
                            <?php //echo html::select('batchCreateChildTask', $pipeline, '', 'class="hidden"');?>
                            <?php
                            foreach ($customFields as $field)
                                $this->task->printCell($field, $child, $users, $browseType, $branchGroups, $modulePairs, $useDatatable ? 'datatable' : 'table', true, $depts, $pipeline, $versions);
                            ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <?php if (!isset($columns)) $columns = ($this->cookie->windowWidth > $this->config->wideSize ? 15 : 13) - ($project->type == 'sprint' ? 0 : 1); ?>
                <td colspan='<?php echo $columns; ?>'>
                    <div class='table-actions clearfix'>
                        <?php

                        $tools = array('batchAssignTo' => true, 'batchAssignToDept' => true, 'batchClose' => true, 'batchComplete' => true, 'batchComplete' => true, 'batchSetWorkhour' => true, 'batchDelete' => true, 'batchChangeVersion' => true);

                        $memberPairs = array();
                        foreach ($deptUsers as $key => $member) {
                            $memberPairs[$key] = $member->realname;
                            //echo $member;
                        }

                        $deptPairs = array();
                        foreach ($depts as $key => $val) {
                            $deptPairs[$key] = $val;
                            //echo "dept $key -> $val <br>";
                        }

                        $versionPairs = array();
                        $versionPairs[0] = '无';
                        foreach ($versions as $key => $val) {
                            $versionPairs[$key] = $val;
                            //echo "dept $key -> $val <br>";
                        }

                        $memberPairs = $deptUsers;
                        $canBatchEdit = common::hasPriv('task', 'batchEdit', !empty($task) ? $task : null);
                        $canBatchClose = (common::hasPriv('task', 'batchClose', !empty($task) ? $task : null) && strtolower($browseType) != 'closedBy');
                        $canBatchCancel = common::hasPriv('task', 'batchCancel', !empty($task) ? $task : null);
                        $canBatchChangeModule = common::hasPriv('task', 'batchChangeModule', !empty($task) ? $task : null);
                        $canBatchAssignTo = common::hasPriv('task', 'batchAssignTo', !empty($task) ? $task : null);

                        if (count($tasks)) {
                            echo html::selectButton();

                            $actionLink = $this->createLink('task', 'batchEdit', "");
                            $misc = $canBatchEdit ? "onclick=\"setFormAction('$actionLink', '', '#gametaskinternalMydept')\"" : "disabled='disabled'";

                            echo "<div class='btn-group dropup'>";
                            //echo html::commonButton($lang->edit, $misc);
                            echo "<button id='moreAction' type='button' class='btn dropdown-toggle' data-toggle='dropdown' disabled='disabled'></button>";

                            //echo "<ul class='dropdown-menu' id='moreActionMenu'>";

                            if ($tools['batchActive']) {
                                $actionLink = $this->createLink('task', 'batchActive');
                                $misc = "onclick=\"setFormAction('$actionLink', 'hiddenwin', '#moreAction')\"";
                                echo html::linkButton($lang->task->active, '#', 'self', $misc);
                            }


                            if ($tools['batchClose']) {
                                $actionLink = $this->createLink('task', 'batchClose');
                                $misc = $canBatchClose ? "onclick=\"setFormAction('$actionLink', 'hiddenwin', '#moreAction')\"" : "class='disabled'";
                                //echo "<li>" . html::a('#', $lang->close, '', $misc) . "</li>";
                                //echo html::a('#', $lang->close, '', $misc);
                                echo html::linkButton($lang->task->close, '#', 'self', $misc);
                                //echo html::commonButton($lang->close, $misc);


                                $actionLink = $this->createLink('task', 'batchCancel');
                                $misc = $canBatchClose ? "onclick=\"setFormAction('$actionLink', 'hiddenwin', '#moreAction')\"" : "class='disabled'";
                                //echo "<li>" . html::a('#', $lang->close, '', $misc) . "</li>";
                                //echo html::a('#', $lang->close, '', $misc);
                                echo html::linkButton($lang->task->cancel, '#', 'self', $misc);
                            }


                            if ($tools['batchComplete']) {
                                $actionLink = $this->createLink('task', 'batchComplete');
                                $misc = $canBatchCancel ? "onclick=\"setFormAction('$actionLink', 'hiddenwin', '#moreAction')\"" : "class='disabled'";
                                //echo "<li>" . html::a('#', $lang->task->cancel, '', $misc) . "</li>";
                                //echo html::a('#', $lang->task->cancel, '', $misc);
                                echo html::linkButton($lang->task->complete, '#', 'self', $misc);
                                //echo html::commonButton($lang->task->cancel, $misc);
                            }



                            //echo "</ul>";
                            //echo "</ul>";
                            echo "</div>";

                            if ($tools['batchSetWorkhour']) {
                                $actionLink = $this->createLink('task', 'batchSetWorkhour', "");

                                echo "<div class='btn-group dropup'>";
                                echo "<button id='taskbatchSetWorkhour' type='button' class='btn dropdown-toggle' data-toggle='dropdown'>" . $lang->task->changeWorkHour . "<span class='caret'></span></button>";
                                echo "<ul class='dropdown-menu' id='taskbatchSetWorkhourMenu'>";

                                $workhourPairs = array(1 => '1 H', 2 => '2 H', 4 => '4 H', 8 => '1 Day', 12 => '1.5 Day', 16 => '2 Day', 24 => '3 Day', 32 => '4 Day', 40 => '5 Day');

                                echo html::select('workHour', $workhourPairs, '', 'class="hidden"');

                                echo '<ul class="dropdown-list">';
                                foreach ($workhourPairs as $key => $value) {
                                    if (empty($key)) continue;
                                    echo "<li class='option' data-key='$key'>" . html::a("javascript:$(\"#workHour\").val(\"$key\");setFormAction(\"$actionLink\", \"hiddenwin\", \"#moreAction\")", $value, '', '') . '</li>';
                                }
                                echo "</ul>";
                                echo "</div></li>";
                                echo "</ul>";
                                //echo "</div>";
                            }


                            if ($tools['batchAssignTo']) {

                                //$actionLink = $this->createLink('task', 'batchAssignTo', "");
                                $actionLink = $this->createLink('task', 'batchAssignTo', "projectID=$projectID");

                                /*
                                {
                                    echo "<div class='btn-group dropup'>";
                                    echo "<button id='taskbatchAssignToDept' type='button' class='btn dropdown-toggle' data-toggle='dropdown'>" . $lang->task->assignedToDept . "<span class='caret'></span></button>";
                                    echo "<ul class='dropdown-menu' id='taskbatchAssignToDeptMenu'>";
                                    echo html::select('assignedToDept', $deptPairs, '', 'class="hidden"');

                                    echo '<li class="dropdown-submenu">';
                                    foreach ($deptPairs as $key => $value) {
                                        if (empty($key)) continue;
                                        echo html::a('javascript:;', $lang->story->review, '', "id='reviewItem'");
                                        echo '<ul class="dropdown-list">';
                                        $actionLink = $this->createLink('task', 'batchAssignToDept', "dept=$key");
                                        echo "<li class='option' data-key='$key'>" . html::a("javascript:$(\"#assignedToDept\").val(\"$key\");setFormAction(\"$actionLink\", \"hiddenwin\", \"#moreAction\")", $value, '', '') . '</li>';
                                        echo "</ul>";
                                    }
                                    echo "</li>";
                                    echo "</div></li>";
                                    echo "</ul>";
                                    //echo "</div>";
                                }

                                //*/



                                echo "<div class='btn-group dropup'>";
                                echo "<button id='taskBatchAssignTo' type='button' class='btn dropdown-toggle' data-toggle='dropdown'>" . $lang->task->assignedTo . "<span class='caret'></span></button>";
                                echo "<ul class='dropdown-menu' id='taskBatchAssignToMenu'>";
                                echo html::select('assignedTo', $memberPairs, '', 'class="hidden"');

                                //*

                                foreach ($deptWithUsers as $key => $value) {
                                    if (empty($key)) continue;
                                    echo '<li class="dropdown-submenu">';
                                    echo html::a('javascript:;', $deptPairs[$key], '', "id='dept-id-$key'");
                                    echo '<ul class="dropdown-menu">';
                                    foreach($value as $account => $realName)
                                    {
                                        $actionLink = $this->createLink('task', 'batchAssignTo', "projectID=$projectID&account=$account");
                                        echo "<li class='option' data-key='$account'>" . html::a("javascript:$(\"#assignedTo\").val(\"$account\");setFormAction(\"$actionLink\", \"hiddenwin\");", $realName, '', '') . '</li>';
                                    }
                                    echo '</li>';
                                    echo '</ul>';
                                }

                                //if (true || $withSearch) echo "<div class='menu-search'><div class='input-group input-group-sm'><input type='text' class='form-control' placeholder=''><span class='input-group-addon'><i class='icon-search'></i></span></div></div>";
                                echo "</ul>";
                                //*/

                                /*
                                echo '<ul class="dropdown-menu">';
                                foreach ($memberPairs as $key => $value) {
                                    if (empty($key)) continue;
                                    //echo "<li class='option' data-key='$key'>" . html::a("javascript:$(\".table-actions #assignedTo\").val(\"$key\");setFormAction(\"$actionLink\", \"hiddenwin\", \"#moreAction\")", $value, '', '') . '</li>';
                                    $actionLink = $this->createLink('task', 'batchAssignTo', "projectID=$projectID");
                                    echo "<li class='option' data-key='$key'>" . html::a("javascript:$(\"#assignedTo\").val(\"$key\");setFormAction(\"$actionLink\", \"hiddenwin\")", $value, '', '') . '</li>';
                                }
                                echo "</ul>";
                                //*/


                                echo "</div></li>";
                                echo "</ul>";
                                //echo "</div>";
                            }

                            //if ($tools['batchAssignToCheckByGD'])
                            {

                                //$actionLink = $this->createLink('task', 'batchAssignTo', "");
                                $actionLink = $this->createLink('task', 'batchAssignToCheckByGD', "projectID=$projectID");

                                echo "<div class='btn-group dropup'>";
                                echo "<button id='taskBatchAssignToCheckByGD' type='button' class='btn dropdown-toggle' data-toggle='dropdown'>" . $lang->task->batchAssignToCheckByGD . "<span class='caret'></span></button>";
                                echo "<ul class='dropdown-menu' id='taskBatchAssignToCheckByGDMenu'>";
                                echo html::select('checkByGD', $memberPairs, '', 'class="hidden"');

                                foreach ($deptWithUsers as $key => $value) {
                                    if (empty($key)) continue;
                                    echo '<li class="dropdown-submenu">';
                                    echo html::a('javascript:;', $deptPairs[$key], '', "id='checkByGD-dept-id-$key'");
                                    echo '<ul class="dropdown-menu">';
                                    foreach($value as $account => $realName)
                                    {
                                        $actionLink = $this->createLink('task', 'batchAssignToCheckByGD', "projectID=$projectID&account=$account");
                                        echo "<li class='option' data-key='$account'>" . html::a("javascript:$(\"#checkByGD\").val(\"$account\");setFormAction(\"$actionLink\", \"hiddenwin\");", $realName, '', '') . '</li>';
                                    }
                                    echo '</li>';
                                    echo '</ul>';
                                }

                                /*
                                echo '<ul class="dropdown-list">';
                                foreach ($memberPairs as $key => $value) {
                                    if (empty($key)) continue;
                                    //echo "<li class='option' data-key='$key'>" . html::a("javascript:$(\".table-actions #assignedTo\").val(\"$key\");setFormAction(\"$actionLink\", \"hiddenwin\", \"#moreAction\")", $value, '', '') . '</li>';
                                    $actionLink = $this->createLink('task', 'batchAssignToCheckByGD', "projectID=$projectID");
                                    echo "<li class='option' data-key='$key'>" . html::a("javascript:$(\"#assignToCheckByGD\").val(\"$key\");setFormAction(\"$actionLink\", \"hiddenwin\")", $value, '', '') . '</li>';
                                }
                                //*/


                                echo "</ul>";
                                //if ($withSearch) echo "<div class='menu-search'><div class='input-group input-group-sm'><input type='text' class='form-control' placeholder=''><span class='input-group-addon'><i class='icon-search'></i></span></div></div>";
                                echo "</div></li>";
                                echo "</ul>";
                                //echo "</div>";
                            }

                            //if (false && $tools['batchChangeModule'])
                            {
                                echo "<div class='btn-group dropup'>";
                                echo "<button id='taskbatchChangeModule' type='button' class='btn dropdown-toggle' data-toggle='dropdown'>" . $lang->task->moduleAB . "<span class='caret'></span></button>";
                                echo "<ul class='dropdown-menu' id='taskbatchChangeModuleMenu'>";
                                echo html::select('batchChangeModule', $deptPairs, '', 'class="hidden"');

                                echo '<ul class="dropdown-list">';
                                foreach ($modules as $moduleId => $module) {
                                    $actionLink = $this->createLink('task', 'batchChangeModule', "moduleID=$moduleId");
                                    if (empty($moduleId)) continue;
                                    echo "<li class='option' data-key='$moduleId'>" . html::a("javascript:$(\"#batchChangeModule\").val(\"$moduleId\");setFormAction(\"$actionLink\", \"hiddenwin\", \"#moreAction\")", $module, '', '') . '</li>';
                                }
                                echo "</ul>";
                                echo "</div></li>";
                                echo "</ul>";
                                //echo "</div>";
                            }


                            if (false && $tools['batchAssignToDept'])
                            {
                                echo "<div class='btn-group dropup'>";
                                echo "<button id='taskbatchAssignToDept' type='button' class='btn dropdown-toggle' data-toggle='dropdown'>" . $lang->task->assignedToDept . "<span class='caret'></span></button>";
                                echo "<ul class='dropdown-menu' id='taskbatchAssignToDeptMenu'>";
                                echo html::select('assignedToDept', $deptPairs, '', 'class="hidden"');

                                echo '<ul class="dropdown-list">';
                                foreach ($deptPairs as $key => $value) {
                                    if (empty($key)) continue;
                                    $actionLink = $this->createLink('task', 'batchAssignToDept', "dept=$key");
                                    echo "<li class='option' data-key='$key'>" . html::a("javascript:$(\"#assignedToDept\").val(\"$key\");setFormAction(\"$actionLink\", \"hiddenwin\", \"#moreAction\")", $value, '', '') . '</li>';
                                }
                                echo "</ul>";
                                echo "</div></li>";
                                echo "</ul>";
                                //echo "</div>";
                            }

                            {
                                $actionLink = $this->createLink('task', 'batchChangePriority', "productID=$productID");
                                $priList = (array)$this->lang->task->priList;
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


                            if (false && $tools['batchChangeVersion']) {
                                $actionLink = $this->createLink('project', 'batchChangeVersion', "");

                                echo "<div class='btn-group dropup'>";
                                echo "<button id='taskbatchChangeVersion' type='button' class='btn dropdown-toggle' data-toggle='dropdown'>" . $lang->task->batchChangeVersion . "<span class='caret'></span></button>";
                                echo "<ul class='dropdown-menu' id='taskbatchChangeVersionMenu'>";
                                echo html::select('changeVersion', $versionPairs, '', 'class="hidden"');

                                echo '<ul class="dropdown-list">';
                                foreach ($versionPairs as $key => $value) {
                                    //if (empty($key)) continue;
                                    echo "<li class='option' data-key='$key'>" . html::a("javascript:$(\"#changeVersion\").val(\"$key\");setFormAction(\"$actionLink\", \"hiddenwin\", \"#moreAction\")", $value, '', '') . '</li>';
                                }
                                echo "</ul>";
                                echo "</div></li>";
                                echo "</ul>";
                                //echo "</div>";
                            }

                            //echo "<button id='test_dpt' onclick='on_test_dpt()'>Test Dpt</button>";
                            //echo "<button id='test_dpt' onclick='on_test_setdpt()'>Test Set Dpt</button>";

                            if (false && $tools['batchDelete']) {

                                $actionLink = $this->createLink('task', 'batchDelete');
                                $misc = "onclick=\"setFormAction('$actionLink', 'hiddenwin', '#moreAction')\"";
                                echo html::linkButton($lang->task->delete, '#', 'self', $misc);
                            }

                            $PMRights = common::hasPriv('task', 'batchSetEstStartFromRealStart', !empty($task) ? $task : null);
                            //if ($PMRights)
                            {
                                $actionLink = $this->createLink('task', 'batchSetEstStartFromRealStart');
                                $misc = "onclick=\"setFormAction('$actionLink', 'hiddenwin', '#moreAction')\"";
                                echo html::linkButton($lang->task->batchSetEstStartFromRealStart, '#', 'self', $misc);
                            }
                        }
                        echo "<div class='text'>" . $summary . "</div>";
                        ?>
                    </div>
                    <?php $pager->show(); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
<?php js::set('checkedSummary', $lang->project->checkedSummary); ?>
<?php js::set('replaceID', 'taskList') ?>
<script>
    $('#project<?php echo $projectID;?>').addClass('active')
    $('#listTab').addClass('active')
    $('#<?php echo ($browseType == 'bymodule' and $this->session->taskBrowseType == 'bysearch') ? 'all' : $this->session->taskBrowseType;?>Tab').addClass('active');
    <?php if($browseType == 'bysearch'):?>
    $shortcut = $('#QUERY<?php echo (int)$param;?>Tab');
    if ($shortcut.size() > 0) {
        $shortcut.addClass('active');
        $('#bysearchTab').removeClass('active');
        $('#querybox').removeClass('show');
    }
    <?php endif;?>
    statusActive = '<?php echo isset($lang->project->statusSelects[$this->session->taskBrowseType]);?>';
    if (statusActive) $('#statusTab').addClass('active')
    <?php if(isset($this->config->project->homepage) and $this->config->project->homepage != 'browse'):?>
    $('#modulemenu .nav li.right:last').after("<li class='right'><a style='font-size:12px' href='javascript:setHomepage(\"project\", \"browse\")'><i class='icon icon-cog'></i> <?php echo $lang->homepage?></a></li>")
    <?php endif;?>
</script>
<?php include '../../common/view/footer.html.php'; ?>
