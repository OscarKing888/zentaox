<?php
include '../../common/view/header.lite.html.php';
include '../../common/view/chart.html.php';
?>
<div id='titlebar'>
    <div class='heading'>
        <span><?php echo html::icon($lang->icons['report']);?></span>
        <small class='text-muted'> <?php echo $lang->story->tasks;?></small>
    </div>
</div>

<?php
include 'info_story.html.php';
?>

<div class='tasksList'>
    <form class='form-condensed' target='hiddenwin'>
        <table class='table table-fixed'>
            <thead>
            <tr class='text-center'>
                <th class='w-40px'>    <?php echo $lang->idAB;?></th>
                <th class='w-p80'>   <?php echo $lang->task->name;?></th>
                <th class='w-pri'>   <?php echo $lang->priAB;?></th>
                <th class='w-status'><?php echo $lang->statusAB;?></th>
                <th class='w-user'>  <?php echo $lang->task->assignedToAB;?></th>
                <th class='w-60px'>  <?php echo $lang->task->progress;?></th>
                <th class='w-80px'>  <?php echo $lang->actions;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($tasks as $key => $task):?>
                <?php
                // oscar[
                $taskDisplayStatus = 'wait';
                if(!$task->checkedStatus)
                {
                    if($task->status == 'done'){
                        if($task->checkBy == $this->app->user->account)
                        {
                            $taskDisplayStatus = 'wait_check-by-me';
                        }
                        else {
                            $taskDisplayStatus = 'wait_check';
                        }
                    }
                    else {
                        $taskDisplayStatus = $task->status;
                    }
                }
                else
                {
                    $taskDisplayStatus = 'checked';
                }
                // oscar]
                ?>

                <tr class='text-center'>
                    <td><?php echo $task->id;?></td>
                    <td class='text-left' title="<?php echo $task->name?>">
                        <?php
                        //echo $task->name;
                        //$this->task->printCell('name', $task)
                        $deptNames = explode('/', $depts[$task->dept]);
                        $deptName = " - <span class='task-dept'>" . $deptNames[count($deptNames) - 1] . "</span>";

                        $taskLink = helper::createLink('task', 'view', "taskID=$task->id");
                        echo html::a($taskLink, $task->name . $deptName, '_blank', "class='iframe' style='color: $task->color'");
                        ?>
                    </td>
                    <td><span class='<?php echo 'pri' . zget($lang->task->priList, $task->pri, $task->pri)?>'><?php echo $task->pri == '0' ? '' : zget($lang->task->priList, $task->pri, $task->pri);?></span></td>
                    <td <?php echo "class='task-$taskDisplayStatus'"; ?> ><?php echo $lang->task->statusList[$taskDisplayStatus];?></td>
                    <td><?php echo zget($users, $task->assignedTo, $task->assignedTo);?></td>

                    <td><div <?php echo "class='progress-pie22 " . ($task->progress == 100 ? "progressCompleted'" : "progressInCompleted'"); ?> title="<?php echo $task->progress?>%" data-value='<?php echo $task->progress;?>'><?php echo $task->progress . '%';?></div></td>
                    <td>
                        <?php
                        //oscar:
                        $storyID = $task->story;
                        $assignedTo = $task->assignedTo;
                        $bugTitle = $task->name . "_" .  $this->dept->getByID($task->dept)->name;
                        $bugTitle = baseModel::trimTitle($bugTitle);
                        $storyModuleID = $task->module;
                        $productID = $this->dao->select('product')->from(TABLE_PROJECTPRODUCT)
                            ->where('project')->eq($task->project)
                            ->fetch('product');
                        $projectID = $task->project;
                        $createBugParams = "productID=$productID&branch=0&extras=taskID=$task->id,projectID=$projectID,storyID=$storyID,assignedTo=$assignedTo,title=$bugTitle,moduleID=$storyModuleID";
                        common::printIcon('bug', 'create', $createBugParams, '', 'list', 'bug', '_blank');

                        //echo $task->checkBy . $task->checkedStatus;
                        if($task->checkBy == $this->app->user->account && !$task->checkedStatus)// && $task->status == 'done')
                        {
                            common::printIcon('task', 'checkByGD', "taskID=$task->id", $task, 'list', '', '', 'iframe', true);
                        }

                        if($task->checkBy == $this->app->user->account && $task->checkedStatus)
                        {
                            common::printIcon('task', 'uncheckByGD', "taskID=$task->id", $task, 'list', '', '', 'iframe', true);
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="9"><?php echo $summary;?></td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
<?php include '../../common/view/footer.lite.html.php';?>
