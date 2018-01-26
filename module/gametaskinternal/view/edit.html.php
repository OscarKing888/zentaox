<?php include '../../common/view/header.html.php'; ?>
<?php
include '../../common/view/chart.html.php';
include '../../common/view/datepicker.html.php';
include '../../common/view/datatable.fix.html.php';
?>

<div id='titlebar'>
    <div class='heading'>
        <span class='prefix'><?php echo html::icon($lang->icons['task']); ?>
            <strong><?php echo $gameTask->id; ?></strong></span>
        <strong style='color: <?php echo $gameTask->color; ?>'>
            <?php if (!empty($gameTask->parent)) echo '<span class="label">' . $this->lang->task->childrenAB . '</span> '; ?>
            <?php if (!empty($gameTask->team)) echo '<span class="label">' . $this->lang->task->multipleAB . '</span> '; ?>
            <?php echo isset($gameTask->parentName) ? $gameTask->parentName . '/' : ''; ?><?php echo $gameTask->title; ?>
        </strong>
        <?php if ($gameTask->deleted): ?>
            <span class='label label-danger'><?php echo $lang->task->deleted; ?></span>
        <?php endif; ?>
        <?php if ($gameTask->fromBug != 0): ?>
            <small><?php echo html::icon($lang->icons['bug']) . " {$lang->task->fromBug}$lang->colon$gameTask->fromBug"; ?></small>
        <?php endif; ?>
    </div>
    <div class='actions'>
        <?php
        $browseLink = $app->session->taskList != false ? $app->session->taskList : $this->createLink('gametaskinternal', 'details', "");
        $actionLinks = '';
        if (!$gameTask->deleted) {
            ob_start();
            echo "<div class='btn-group'>";
            //common::printIcon('task', 'assignTo',       "projectID=$gameTask->project&taskID=$gameTask->id", $task, 'button', '', '', 'iframe', true, '', empty($gameTask->team) ? $lang->task->assignTo : $lang->task->transfer);
            //common::printIcon('task', 'start',          "taskID=$gameTask->id", $task, 'button', '', '', 'iframe', true);
            //common::printIcon('task', 'restart',        "taskID=$gameTask->id", $task, 'button', '', '', 'iframe', true);
            //common::printIcon('task', 'recordEstimate', "taskID=$gameTask->id", $task, 'button', '', '', 'iframe', true);
            //common::printIcon('task', 'pause',          "taskID=$gameTask->id", $task, 'button', '', '', 'iframe', true);
            //common::printIcon('task', 'finish',         "taskID=$gameTask->id", $task, 'button', '', '', 'iframe showinonlybody text-success', true);
            //common::printIcon('task', 'close',          "taskID=$gameTask->id", $task, 'button', '', '', 'iframe', true);
            //common::printIcon('task', 'activate',       "taskID=$gameTask->id", $task, 'button', '', '', 'iframe text-success', true);
            //common::printIcon('task', 'cancel',         "taskID=$gameTask->id", $task, 'button', '', '', 'iframe', true);
            echo '</div>';

            echo "<div class='btn-group'>";
            //if(empty($gameTask->team) or empty($gameTask->children)) common::printIcon('task', 'batchCreate',    "project=$gameTask->project&storyID=$gameTask->story&moduleID=$gameTask->module&taskID=$gameTask->id", $task, 'button','plus','','','','',' ');
            common::printIcon('task', 'edit', "taskID=$gameTask->id", $task);
            //common::printCommentIcon('task', $task);
            //common::printIcon('task', 'create', "productID=0&storyID=0&moduleID=0&taskID=$gameTask->id", $task, 'button', 'copy');
            //common::printIcon('task', 'delete', "projectID=$gameTask->project&taskID=$gameTask->id", $task);
            echo '</div>';

            echo "<div class='btn-group'>";
            if (!empty($gameTask->parent)) echo html::a(helper::createLink('task', 'view', "taskID=$gameTask->parent"), "<i class='icon-pre icon-double-angle-left'></i>", '', "class='btn' title='{$lang->task->parent}'");
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
                <legend><?php echo $lang->gametaskinternal->version; ?></legend>
                <div class='article-content'><?php echo $versions[$gameTask->version]; ?></div>
            </fieldset>

            <fieldset>
                <legend><?php echo $lang->gametaskinternal->desc; ?></legend>
                <div class='article-content'><?php echo $gameTask->desc; ?></div>
            </fieldset>

            <fieldset>
                <legend><?php echo $lang->gametaskinternal->srcResPath; ?></legend>
                <div class='article-content'><?php echo $gameTask->srcResPath; ?></div>
            </fieldset>

            <fieldset>
                <legend><?php echo $lang->gametaskinternal->gameResPath; ?></legend>
                <div class='article-content'><?php echo $gameTask->gameResPath; ?></div>
            </fieldset>

            <td colspan='10' class='text-center'>
                <?php
                echo html::submitButton();
                echo html::backButton();
                //echo html::a(inlink('edit', "id=$gameTask->id"), $lang->gametaskinternal->edit);
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
                        <td><?php echo $allProducts[$gameTask->product]; ?></td>
                    </tr>
                    <tr>
                        <th class='w-80px'><?php echo $lang->gametaskinternal->version; ?></th>
                        <td><?php echo $versions[$gameTask->version]; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->gametaskinternal->dept; ?></th>
                        <td><?php echo $depts[$gameTask->dept]; ?> </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->gametaskinternal->owner; ?></th>
                        <td>
                            <?php echo $users[$gameTask->owner]; ?>
                        </td>
                    </tr>

                    <tr>
                        <th><?php echo $lang->gametaskinternal->assignedTo; ?></th>
                        <td><?php echo $gameTask->assignedTo ? $allUsers[$task->assignedTo] : $lang->gametaskinternal->assignedToNull; ?></td>
                    </tr>

                    <?php if($gameTask->sizeWidth > 0): ?>
                        <tr>
                            <th><?php echo $lang->gametaskinternal->width; ?></th>
                            <td><?php echo $gameTask->sizeWidth; ?></td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->gametaskinternal->height; ?></th>
                            <td><?php echo $gameTask->sizeHeight; ?></td>
                        </tr>
                    <?php endif;?>

                    <tr>
                        <th><?php echo $lang->gametaskinternal->workhour; ?></th>
                        <td><?php echo $gameTask->workhour; ?></td>
                    </tr>


                    <tr>
                        <th><?php echo $lang->gametaskinternal->completeStat; ?></th>
                        <td><?php echo $task->completed ? $this->lang->gametaskinternal->completed : $this->lang->gametaskinternal->incomplete; ?></td>
                    </tr>


                    <tr>
                        <th><?php echo $lang->gametaskinternal->closeStat; ?></th>
                        <td><?php echo $task->closed ? $this->lang->gametaskinternal->closed : $this->lang->gametaskinternal->unclose; ?></td>
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
