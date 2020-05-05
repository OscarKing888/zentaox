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
//echo "status:$status browseType:$browseType moduleID:$moduleID";
//echo "deptID:$deptID";
?>

<div class='main'>

    <div id='titlebar'>
        <div class='heading'>
            <span class='prefix'><?php echo html::icon($lang->icons['story']); ?> <strong><?php echo $testtask->id; ?></strong></span>
            <strong style='color: <?php echo $story->color; ?>'><?php echo $testtask->name; ?></strong>
        </div>

    </div>

    <fieldset>
        <legend>测试说明</legend>
        <div><?php echo nl2br(htmlspecialchars_decode($testtask->testComments));?></div>
    </fieldset>

    <form method='post' id='testTaskForm'>

        <table class='table table-condensed table-hover table-striped tablesorter table-fixed table-selectable'
               id='taskList' data-checkable='true' data-custom-menu='true'
               data-checkbox-name='taskIDList[]'>


            <thead>
            <tr class='colhead'>
                <th class='w-id header'>ID</th>
                <th class='text-left header'>需求</th>
                <th class='text-left header'><?php echo $lang->assignedToAB; ?></th>
                <th class='text-left header'>状态</th>

                <th title='<?php echo $lang->story->taskCount ?>'
                    class='w-30px'><?php echo $lang->story->taskCountAB; ?></th>
                <th title='<?php echo $lang->story->bugCount ?>'
                    class='w-30px'><?php echo $lang->story->bugCountAB; ?></th>
                <th title='<?php echo $lang->story->caseCount ?>'
                    class='w-30px'><?php echo $lang->story->caseCountAB; ?></th>

                <th class='text-left header'>操作</th>
            </tr>
            </thead>

            <tbody>
                <?php foreach ($stories as $story):?>

                <?php
                    //echo "process:" . $story->storyDat->title;
                    $storyID = $story->storyDat->id;
                    $storyVersion = 0;//$story->storyDat->verstion;
                    //echo "process:" . $storyID;
                ?>
                <tr class='text-left odd' data-id='<?php echo $story->id; ?>'>
                    <td class='cell-id' >
                        <input type='checkbox' name='taskIDList[<?php echo $story->id; ?>]' value='<?php echo $story->id; ?>'/>
                        <?php echo $story->id; ?>
                    </td>

                    <td>
                        <?php
                        $storyLink = $this->createLink('story', 'view', "storyID=$storyID&version=$storyVersion&from=testtask&param=$projectID");
                        $displayTitle = "#" . $storyID . "  " . $story->storyDat->title;
                        echo html::a($storyLink, $displayTitle, null, "class='iframe' style='color: " . $story->storyDat->color . "'");
                        ?>
                    </td>

                    <td><?php echo $users[$story->assignedTo]; ?></td>
                    <td <?php echo "class='test-story-$story->status'";?>> <?php echo $lang->task->statusList[$story->status]; ?></td>
                    <td class='linkbox'>
                        <?php
                        $tasksLink = $this->createLink('story', 'tasks', "storyID=$storyID&projectID=$projectID");
                        $storyTasks[$storyID] > 0 ? print(html::a($tasksLink, $storyTasks[$storyID], '', 'class="iframe"')) : print(0);
                        ?>
                    <td>
                        <?php
                        $bugsLink = $this->createLink('story', 'bugs', "storyID=$storyID&projectID=$projectID");
                        $storyBugs[$storyID] > 0 ? print(html::a($bugsLink, $storyBugs[$storyID], '', 'class="iframe"')) : print(0);
                        ?>
                    </td>
                    <td>
                        <?php
                        $casesLink = $this->createLink('story', 'cases', "storyID=$storyID&projectID=$projectID");
                        $storyCases[$storyID] > 0 ? print(html::a($casesLink, $storyCases[$storyID], '', 'class="iframe"')) : print(0);
                        ?>
                    </td>
                    <td>
                        <?php
                        //$storyID = $story->storyDat->id;
                        $assignedTo = $story->storyDat->assignedTo;
                        $storyTitle = $story->storyDat->title;
                        $storyModuleID = $story->storyDat->module;
                        //$productID = $story->product;
                        $createBugParams = "productID=$productID&branch=$story->branch&extras=storyID=$storyID,assignedTo=$assignedTo,title=$storyTitle,moduleID=$storyModuleID";
                        common::printIcon('bug', 'create', $createBugParams, '', 'list', 'bug', '_blank');

                        //common::printIcon('testtask', 'setTestTaskStoryStatusFail',    "taskStoryID=$story->id", $story, 'list',  '', 'hiddenwin');
                        common::printIcon('testtask', 'setTestTaskStoryStatusDone',    "taskStoryID=$story->id", $story, 'list',  '', 'hiddenwin');
                        common::printIcon('testtask', 'setTestTaskStoryStatusCancel',    "taskStoryID=$story->id", $story, 'list',  '', 'hiddenwin');
                        //echo $story->id;
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>

            <tfoot>
            <tr>

                <td colspan='8'>
                    <div class='table-actions clearfix'>
                        <?php

                        /*
                        foreach ($users as $k => $v)
                        {
                            error_log("view test task users:$k = $v");
                            $memberPairs[$k] = $v;
                        }
                        //*/

                        $memberPairs = $users;

                        if (count($stories)) {
                            echo html::selectButton();

                            $actionLink = $this->createLink('task', 'batchEdit', "");
                            $misc = $canBatchEdit ? "onclick=\"setFormAction('$actionLink', '', '#gametaskinternalMydept')\"" : "disabled='disabled'";


                            // assigned to
                            {
                                $actionLink = $this->createLink('testtask', 'batchAssignTestStoryTo', "productID=$productID");

                                echo "<div class='btn-group dropup'>";
                                echo "<button id='taskBatchAssignTo' type='button' class='btn dropdown-toggle' data-toggle='dropdown'>" . $lang->story->assignedTo . "<span class='caret'></span></button>";
                                echo "<ul class='dropdown-menu' id='taskBatchAssignToMenu'>";
                                echo html::select('assignedTo', $memberPairs, '', 'class="hidden"');

                                foreach ($memberPairs as $key => $value) {
                                    if (empty($key)) continue;
                                    $actionLink = $this->createLink('testtask', 'batchAssignTestStoryTo', "user=$key");
                                    echo "<li class='option' data-key='$key'>" . html::a("javascript:$(\"#assignedTo\").val(\"$key\");setFormAction(\"$actionLink\", \"hiddenwin\")", $value, '', '') . '</li>';
                                }

                                echo "</ul>";
                                echo "</div>";

                                // delete
                                if(false)
                                {
                                    $actionLink = $this->createLink('testtask', 'batchUnlinkStoryFromTestTask', "");
                                    $misc = "onclick=\"alert('www');setFormAction('$actionLink', 'hiddenwin', '#moreAction')\"";
                                    //echo html::linkButton("移除需求", $actionLink, 'self', '');
                                    echo html::linkButton("移除需求", $actionLink, 'self', $misc);
                                }
                            }
                        }

                        ?>
                    </div>
                    <?php
                    //$pager->show();
                    ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>

<?php include '../../common/view/footer.html.php'; ?>