<?php include '../../common/view/header.html.php'; ?>
<?php
include '../../common/view/chart.html.php';
include '../../common/view/datepicker.html.php';
include '../../common/view/datatable.fix.html.php';
?>

<div id='titlebar'>
    <div class='heading'>
        <span class='prefix'><?php echo html::icon($lang->icons['task']); ?>
            <strong><?php echo $task->id; ?></strong></span>
        <strong style='color: <?php echo $task->color; ?>'>
            <?php echo $task->title; ?>
        </strong>
        <?php if ($task->deleted): ?>
            <span class='label label-danger'><?php echo $lang->task->deleted; ?></span>
        <?php endif; ?>
    </div>
    <div class='actions'>
        <?php
        $browseLink = $app->session->taskList != false ? $app->session->taskList : $this->createLink('gametaskinternal', 'details', "");
        $actionLinks = '';
        if (!$task->deleted) {
            ob_start();
            echo "<div class='btn-group'>";
            //common::printIcon('task', 'assignTo',       "projectID=$task->project&taskID=$task->id", $task, 'button', '', '', 'iframe', true, '', empty($task->team) ? $lang->task->assignTo : $lang->task->transfer);
            //common::printIcon('task', 'start',          "taskID=$task->id", $task, 'button', '', '', 'iframe', true);
            //common::printIcon('task', 'restart',        "taskID=$task->id", $task, 'button', '', '', 'iframe', true);
            //common::printIcon('task', 'recordEstimate', "taskID=$task->id", $task, 'button', '', '', 'iframe', true);
            //common::printIcon('task', 'pause',          "taskID=$task->id", $task, 'button', '', '', 'iframe', true);
            //common::printIcon('task', 'finish',         "taskID=$task->id", $task, 'button', '', '', 'iframe showinonlybody text-success', true);
            //common::printIcon('task', 'close',          "taskID=$task->id", $task, 'button', '', '', 'iframe', true);
            //common::printIcon('task', 'activate',       "taskID=$task->id", $task, 'button', '', '', 'iframe text-success', true);
            //common::printIcon('task', 'cancel',         "taskID=$task->id", $task, 'button', '', '', 'iframe', true);
            echo '</div>';

            echo "<div class='btn-group'>";
            //if(empty($task->team) or empty($task->children)) common::printIcon('task', 'batchCreate',    "project=$task->project&storyID=$task->story&moduleID=$task->module&taskID=$task->id", $task, 'button','plus','','','','',' ');
            common::printIcon('gametaskinternal', 'edit', "taskID=$task->id", $task);
            //common::printCommentIcon('task', $task);
            //common::printIcon('task', 'create', "productID=0&storyID=0&moduleID=0&taskID=$task->id", $task, 'button', 'copy');
            //common::printIcon('task', 'delete', "projectID=$task->project&taskID=$task->id", $task);
            echo '</div>';

            echo "<div class='btn-group'>";
            if (!empty($task->parent)) echo html::a(helper::createLink('task', 'view', "taskID=$task->parent"), "<i class='icon-pre icon-double-angle-left'></i>", '', "class='btn' title='{$lang->task->parent}'");
            common::printRPN($browseLink, $preAndNext);
            echo '</div>';

            $actionLinks = ob_get_contents();
            ob_end_clean();
            echo $actionLinks;
        } else {
            common::printRPN($browseLink);
        }
        ?>
    </div>
</div>

<div class='row-table'>
    <div class='col-main'>
        <div class='main'>

            <fieldset>
                <legend><?php echo $lang->gametaskinternal->desc; ?></legend>
                <div class='article-content'><?php echo $task->desc; ?></div>
            </fieldset>

            <fieldset>
                <legend><?php echo $lang->gametaskinternal->srcResPath; ?></legend>
                <div class='article-content'><?php echo $task->srcResPath; ?></div>
            </fieldset>

            <fieldset>
                <legend><?php echo $lang->gametaskinternal->gameResPath; ?></legend>
                <div class='article-content'><?php echo $task->gameResPath; ?></div>
            </fieldset>

            <td class='text-center'>
                <?php
                echo html::linkButton($lang->gametaskinternal->edit, inlink('edit', "id=$task->id"));
                echo html::backButton();
                //echo html::a(inlink('edit', "id=$task->id"), $lang->gametaskinternal->edit);
                ?>
            </td>
        </div>
    </div>
    <div class='col-side'>
        <div class='main main-side'>

            <fieldset>
                <legend><?php echo $lang->task->legendBasic; ?></legend>
                <table class='table table-data table-condensed table-borderless'>
                    <tr>
                        <th class='w-80px'><?php echo $lang->gametaskinternal->product; ?></th>
                        <td><?php echo $allProducts[$task->product]; ?></td>
                    </tr>
                    <tr>
                        <th class='w-80px'><?php echo $lang->gametaskinternal->version; ?></th>
                        <td><?php echo $versions[$task->version]; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->gametaskinternal->dept; ?></th>
                        <td><?php echo $depts[$task->dept]; ?> </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->gametaskinternal->owner; ?></th>
                        <td>
                            <?php echo $users[$task->owner]; ?>
                        </td>
                    </tr>

                    <tr>
                        <th><?php echo $lang->gametaskinternal->assignedTo; ?></th>
                        <td><?php echo $task->assignedTo ? $allUsers[$task->assignedTo] : $lang->gametaskinternal->assignedToNull; ?></td>
                    </tr>

                    <?php if($task->sizeWidth > 0): ?>
                    <tr>
                        <th><?php echo $lang->gametaskinternal->width; ?></th>
                        <td><?php echo $task->sizeWidth; ?></td>
                    </tr>

                    <tr>
                        <th><?php echo $lang->gametaskinternal->height; ?></th>
                        <td><?php echo $task->sizeHeight; ?></td>
                    </tr>
                    <?php endif;?>

                    <tr>
                        <th><?php echo $lang->gametaskinternal->workhour; ?></th>
                        <td><?php echo $task->workhour; ?></td>
                    </tr>

                    <tr>
                        <th><?php echo $lang->gametaskinternal->pri; ?></th>
                        <td><?php  echo "<span class='pri" .$task->pri . "'>";
                            echo $task->pri == '0' ? '' : $task->pri;
                            echo "</span>"; ?></td>
                    </tr>


                    <tr>
                        <th><?php echo $lang->gametaskinternal->completeStat; ?></th>
                        <td><?php echo $task->completed ? $this->lang->gametaskinternal->completed : $this->lang->gametaskinternal->incomplete; ?></td>
                    </tr>


                    <tr>
                        <th><?php echo $lang->gametaskinternal->closeStat; ?></th>
                        <td><?php echo $task->closed ? $this->lang->gametaskinternal->closed : $this->lang->gametaskinternal->unclose; ?></td>
                    </tr>

                    <tr>
                        <th><?php echo "操作" ?></th>
                    <td>
                        <?php
                        echo html::linkButton($lang->gametaskinternal->edit, inlink('edit', "id=$task->id"));
                        echo html::backButton();
                        //echo html::a(inlink('edit', "id=$task->id"), $lang->gametaskinternal->edit);
                        ?>
                    </td>
                    </tr>
                </table>
            </fieldset>
        </div>
    </div>
</div>

<?php include '../../common/view/syntaxhighlighter.html.php'; ?>
<?php include '../../common/view/footer.html.php'; ?>
<?php
//echo js::alert("datatableId:" . $this->view->datatableId . "module:$module method:$method mode:$mode");
?>
