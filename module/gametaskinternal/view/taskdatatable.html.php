<?php
/**
 * The html template file of index method of pipeline module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
?>
<?php include '../../common/view/header.html.php'; ?>
<?php include 'debug.html.php'; ?>
<?php
include '../../common/view/chart.html.php';
include '../../common/view/datepicker.html.php';
include '../../common/view/datatable.fix.html.php';
//include './taskheader.html.php';
?>

<?php

/*
common::printIcon('gametaskinternal', 'music',   "", $task, 'list');
common::printIcon('gametaskinternal', 'start',   "", $task, 'list');
common::printIcon('gametaskinternal', 'star-empty',   "", $task, 'list');
common::printIcon('gametaskinternal', 'user',   "", $task, 'list');
common::printIcon('gametaskinternal', 'film',   "", $task, 'list');
common::printIcon('gametaskinternal', 'off',   "", $task, 'list');
//*/

$verTasks = array();

foreach ($gameTasks as $t) {
    if (!array_key_exists($t->version)) {
        $verTasks[$t->version] = array();
    }

    $verTasks[$t->version][$t->id] = $t;
}

$columns = 14;
$taskLink = helper::createLink('gametaskinternal', 'view', "taskID=$task->id");
?>


<div class='main'>
    <script>setTreeBox();</script>
    <form method='post' id='gametaskinternalMydept'>
        <?php

        $datatableId = $this->moduleName . ucfirst($this->methodName);
        //echo "oscar: datableId = " . $datatableId;

        $useDatatable = true;//(isset($this->config->datatable->$datatableId->mode) and $this->config->datatable->$datatableId->mode == 'datatable');
        $vars = "orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage";

        if ($useDatatable) include '../../common/view/datatable.html.php';
        $customFields = $this->datatable->getSetting('gametaskinternal');

        //foreach ($customFields as $id => $customField) { echo "customField:    $id = $customField->title <br>";}
        /*if ($project->type == 'ops')
        {
            foreach ($customFields as $id => $customField) {
                if ($customField->id == 'story') unset($customFields[$id]);
            }
        }
        //*/
        $widths = $this->datatable->setFixedFieldWidth($customFields);
        $columns = 0;
        ?>
        <table class='table table-condensed table-hover table-striped tablesorter table-fixed <?php echo ($useDatatable ? 'datatable' : 'table-selectable');?> table-selectable' id='taskList' data-checkable='true' data-fixed-left-width='<?php echo $widths['leftWidth']?>' data-fixed-right-width='<?php echo $widths['rightWidth']?>' data-custom-menu='true' data-checkbox-name='taskIDList[]'>
            <thead>
            <tr>
                <?php
                foreach($customFields as $field)
                {
                    //if($field->show)
                    {
                        echo "<th>$field->title</th>";
                        //$this->datatable->printHead($field, $orderBy, $vars);
                        $columns++;
                    }
                }
                ?>
            </thead>

            <tbody>

            <?php foreach($gameTasks as $task): ?>
                <tr class='text-center' data-id='<?php echo $task->id;?>'>
                    <?php
                    foreach($customFields as $field)
                    {
                        //echo $field->title;
                        $this->gametaskinternal->printCell($field, $task, $users, $depts, $versions, $useDatatable ? 'datatable' : 'table');
                    }
                    ?>
                </tr>
            <?php endforeach; ?>
            </tbody>

            <tfoot>
            <tr>
                <?php if (!isset($columns)) $columns = ($this->cookie->windowWidth > $this->config->wideSize ? 15 : 13) - ($project->type == 'sprint' ? 0 : 1); ?>
                <td colspan='<?php echo $columns; ?>'>
                    <div class='table-actions clearfix'>
                        <?php

                        $memberPairs = array();
                        foreach ($deptUsers as $key => $member) {
                            $memberPairs[$key] = $member->realname;
                            //echo $member;
                        }

                        $memberPairs = $deptUsers;
                        $canBatchEdit = true;//common::hasPriv('task', 'batchEdit', !empty($task) ? $task : null);
                        $canBatchClose = true;//(common::hasPriv('task', 'batchClose', !empty($task) ? $task : null) && strtolower($browseType) != 'closedBy');
                        $canBatchCancel = true;//common::hasPriv('task', 'batchCancel', !empty($task) ? $task : null);
                        $canBatchChangeModule = true;//common::hasPriv('task', 'batchChangeModule', !empty($task) ? $task : null);
                        $canBatchAssignTo = true;//common::hasPriv('task', 'batchAssignTo', !empty($task) ? $task : null);
                        if (count($gameTasks)) {
                            echo html::selectButton();

                            $actionLink = $this->createLink('gametaskinternal', 'batchEdit', "");
                            $misc = $canBatchEdit ? "onclick=\"setFormAction('$actionLink', '', '#gametaskinternalMydept')\"" : "disabled='disabled'";

                            echo "<div class='btn-group dropup'>";
                            //echo html::commonButton($lang->edit, $misc);
                            echo "<button id='moreAction' type='button' class='btn dropdown-toggle' data-toggle='dropdown' disabled='disabled'></button>";

                            //echo "<ul class='dropdown-menu' id='moreActionMenu'>";

                            $actionLink = $this->createLink('gametaskinternal', 'batchActive');
                            $misc = "onclick=\"setFormAction('$actionLink', 'hiddenwin', '#moreAction')\"";
                            echo html::linkButton($lang->gametaskinternal->active, '#', 'self', $misc);


                            $actionLink = $this->createLink('gametaskinternal', 'batchClose');
                            $misc = $canBatchClose ? "onclick=\"setFormAction('$actionLink', 'hiddenwin', '#moreAction')\"" : "class='disabled'";
                            //echo "<li>" . html::a('#', $lang->close, '', $misc) . "</li>";
                            //echo html::a('#', $lang->close, '', $misc);
                            echo html::linkButton($lang->gametaskinternal->close, '#', 'self', $misc);
                            //echo html::commonButton($lang->close, $misc);


                            $actionLink = $this->createLink('gametaskinternal', 'batchComplete');
                            $misc = $canBatchCancel ? "onclick=\"setFormAction('$actionLink', 'hiddenwin', '#moreAction')\"" : "class='disabled'";
                            //echo "<li>" . html::a('#', $lang->task->cancel, '', $misc) . "</li>";
                            //echo html::a('#', $lang->task->cancel, '', $misc);
                            echo html::linkButton($lang->gametaskinternal->complete, '#', 'self', $misc);
                            //echo html::commonButton($lang->task->cancel, $misc);

                            //echo "</ul>";
                            //echo "</ul>";
                            echo "</div>";



                            $actionLink = $this->createLink('gametaskinternal', 'batchAssignTo', "");

                            echo "<div class='btn-group dropup'>";
                            echo "<button id='taskBatchAssignTo' type='button' class='btn dropdown-toggle' data-toggle='dropdown'>" . $lang->gametaskinternal->assignedTo . "<span class='caret'></span></button>";
                            echo "<ul class='dropdown-menu' id='taskBatchAssignToMenu'>";
                            echo html::select('assignedTo', $memberPairs, '', 'class="hidden"');

                            echo '<ul class="dropdown-list">';
                            foreach ($memberPairs as $key => $value) {
                                if (empty($key)) continue;
                                echo "<li class='option' data-key='$key'>" . html::a("javascript:$(\".table-actions #assignedTo\").val(\"$key\");setFormAction(\"$actionLink\", \"hiddenwin\", \"#moreAction\")", $value, '', '') . '</li>';
                            }
                            echo "</ul>";
                            if ($withSearch) echo "<div class='menu-search'><div class='input-group input-group-sm'><input type='text' class='form-control' placeholder=''><span class='input-group-addon'><i class='icon-search'></i></span></div></div>";
                            echo "</div></li>";

                            echo "</ul>";


                            echo "</div>";

                            $actionLink = $this->createLink('gametaskinternal', 'batchDelete');
                            $misc = "onclick=\"setFormAction('$actionLink', 'hiddenwin', '#moreAction')\"";
                            echo html::linkButton($lang->gametaskinternal->delete, '#', 'self', $misc);
                        }
                        echo "<div class='text'>Summary:" . $summary . "</div>";
                        ?>
                    </div>
                    <?php $pager->show(); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
<?php js::set('checkedSummary', $lang->gametaskinternal->checkedSummary);?>
<?php js::set('replaceID', 'taskList')?>
<script>
</script>
<?php include '../../common/view/footer.html.php'; ?>
<?php
//echo js::alert("datatableId:" . $this->view->datatableId . "module:$module method:$method mode:$mode");
?>
