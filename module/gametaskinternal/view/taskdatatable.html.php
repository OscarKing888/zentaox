

<?php

$columns = 14;
$taskLink = helper::createLink('gametaskinternal', 'view', "taskID=$task->id");
//echo $customFieldsName;
//echo count($gameTasks);
?>


<div class='main'>
    <script>setTreeBox();</script>
    <form method='post' id='gametaskinternalMydept'>
        <?php

        $datatableId = $this->moduleName . ucfirst($this->methodName);
        //echo "oscar: datableId = " . $datatableId;

        $useDatatable = false;//(isset($this->config->datatable->$datatableId->mode) and $this->config->datatable->$datatableId->mode == 'datatable');
        $vars = "orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage";

        if ($useDatatable) include '../../common/view/datatable.html.php';
        //$customFields = $this->datatable->getSettingEx('gametaskinternal', 'indexField');
        $customFields = $this->datatable->getSettingEx('gametaskinternal', $customFieldsName);

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
                    if($field->show)
                    {
                        //echo "<th>$field->title</th>";
                        //echo $field->title;
                        $this->datatable->printHead($field, $orderBy, $vars);
                        $columns++;
                    }
                    else
                    {
                        //echo "<th><div class='red'>+++ $field->title</div></th>";
                        //$columns++;
                    }
                }
                ?>
            </thead>

            <tbody>

            <?php foreach ($gameTasks as $task): ?>
                <tr class='text-center' data-id='<?php echo $task->id; ?>'>
                    <?php
                    foreach ($customFields as $field) {
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

                        $deptPairs = array();
                        foreach ($depts as $key => $val) {
                            $deptPairs[$key] = $val;
                            //echo "dept $key -> $val <br>";
                        }

                        $versionPairs = array();
                        foreach ($versions as $key => $val) {
                            $versionPairs[$key] = $val;
                            //echo "dept $key -> $val <br>";
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

                            if ($tools['batchActive']) {
                                $actionLink = $this->createLink('gametaskinternal', 'batchActive');
                                $misc = "onclick=\"setFormAction('$actionLink', 'hiddenwin', '#moreAction')\"";
                                echo html::linkButton($lang->gametaskinternal->active, '#', 'self', $misc);
                            }


                            if ($tools['batchClose']) {
                                $actionLink = $this->createLink('gametaskinternal', 'batchClose');
                                $misc = $canBatchClose ? "onclick=\"setFormAction('$actionLink', 'hiddenwin', '#moreAction')\"" : "class='disabled'";
                                //echo "<li>" . html::a('#', $lang->close, '', $misc) . "</li>";
                                //echo html::a('#', $lang->close, '', $misc);
                                echo html::linkButton($lang->gametaskinternal->close, '#', 'self', $misc);
                                //echo html::commonButton($lang->close, $misc);
                            }

                            if ($tools['batchComplete']) {
                                $actionLink = $this->createLink('gametaskinternal', 'batchComplete');
                                $misc = $canBatchCancel ? "onclick=\"setFormAction('$actionLink', 'hiddenwin', '#moreAction')\"" : "class='disabled'";
                                //echo "<li>" . html::a('#', $lang->task->cancel, '', $misc) . "</li>";
                                //echo html::a('#', $lang->task->cancel, '', $misc);
                                echo html::linkButton($lang->gametaskinternal->complete, '#', 'self', $misc);
                                //echo html::commonButton($lang->task->cancel, $misc);
                            }

                            //echo "</ul>";
                            //echo "</ul>";
                            echo "</div>";


                            if ($tools['batchAssignTo']) {
                                $actionLink = $this->createLink('gametaskinternal', 'batchAssignTo', "");

                                echo "<div class='btn-group dropup'>";
                                echo "<button id='taskBatchAssignTo' type='button' class='btn dropdown-toggle' data-toggle='dropdown'>" . $lang->gametaskinternal->assignedTo . "<span class='caret'></span></button>";
                                echo "<ul class='dropdown-menu' id='taskBatchAssignToMenu'>";
                                echo html::select('assignedTo', $memberPairs, '', 'class="hidden"');

                                echo '<ul class="dropdown-list">';
                                foreach ($memberPairs as $key => $value) {
                                    if (empty($key)) continue;
                                    //echo "<li class='option' data-key='$key'>" . html::a("javascript:$(\".table-actions #assignedTo\").val(\"$key\");setFormAction(\"$actionLink\", \"hiddenwin\", \"#moreAction\")", $value, '', '') . '</li>';
                                    echo "<li class='option' data-key='$key'>" . html::a("javascript:$(\"#assignedTo\").val(\"$key\");setFormAction(\"$actionLink\", \"hiddenwin\", \"#moreAction\")", $value, '', '') . '</li>';
                                }
                                echo "</ul>";
                                if ($withSearch) echo "<div class='menu-search'><div class='input-group input-group-sm'><input type='text' class='form-control' placeholder=''><span class='input-group-addon'><i class='icon-search'></i></span></div></div>";
                                echo "</div></li>";
                                echo "</ul>";
                                echo "</div>";
                            }


                            if ($tools['batchAssignToDept']) {
                                $actionLink = $this->createLink('gametaskinternal', 'batchAssignToDept', "");

                                echo "<div class='btn-group dropup'>";
                                echo "<button id='taskbatchAssignToDept' type='button' class='btn dropdown-toggle' data-toggle='dropdown'>" . $lang->gametaskinternal->assignedToDept . "<span class='caret'></span></button>";
                                echo "<ul class='dropdown-menu' id='taskbatchAssignToDeptMenu'>";
                                echo html::select('assignedToDept', $deptPairs, '', 'class="hidden"');

                                echo '<ul class="dropdown-list">';
                                foreach ($deptPairs as $key => $value) {
                                    if (empty($key)) continue;
                                    echo "<li class='option' data-key='$key'>" . html::a("javascript:$(\"#assignedToDept\").val(\"$key\");setFormAction(\"$actionLink\", \"hiddenwin\", \"#moreAction\")", $value, '', '') . '</li>';
                                }
                                echo "</ul>";
                                echo "</div></li>";
                                echo "</ul>";
                                echo "</div>";
                            }


                            if ($tools['batchChangeVersion']) {
                                $actionLink = $this->createLink('gametaskinternal', 'batchChangeVersion', "");

                                echo "<div class='btn-group dropup'>";
                                echo "<button id='taskbatchChangeVersion' type='button' class='btn dropdown-toggle' data-toggle='dropdown'>" . $lang->gametaskinternal->changeVersion . "<span class='caret'></span></button>";
                                echo "<ul class='dropdown-menu' id='taskbatchChangeVersionMenu'>";
                                echo html::select('changeVersion', $versionPairs, '', 'class="hidden"');

                                echo '<ul class="dropdown-list">';
                                foreach ($versionPairs as $key => $value) {
                                    if (empty($key)) continue;
                                    echo "<li class='option' data-key='$key'>" . html::a("javascript:$(\"#changeVersion\").val(\"$key\");setFormAction(\"$actionLink\", \"hiddenwin\", \"#moreAction\")", $value, '', '') . '</li>';
                                }
                                echo "</ul>";
                                echo "</div></li>";
                                echo "</ul>";
                                echo "</div>";
                            }


                            if ($tools['batchSetWorkhour']) {
                                $actionLink = $this->createLink('gametaskinternal', 'batchSetWorkhour', "");

                                echo "<div class='btn-group dropup'>";
                                echo "<button id='taskbatchSetWorkhour' type='button' class='btn dropdown-toggle' data-toggle='dropdown'>" . $lang->gametaskinternal->changeWorkHour . "<span class='caret'></span></button>";
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
                                echo "</div>";
                            }


                            //echo "<button id='test_dpt' onclick='on_test_dpt()'>Test Dpt</button>";
                            //echo "<button id='test_dpt' onclick='on_test_setdpt()'>Test Set Dpt</button>";

                            if ($tools['batchDelete']) {

                                $actionLink = $this->createLink('gametaskinternal', 'batchDelete');
                                $misc = "onclick=\"setFormAction('$actionLink', 'hiddenwin', '#moreAction')\"";
                                echo html::linkButton($lang->gametaskinternal->delete, '#', 'self', $misc);
                            }

                            if ($tools['batchRestore']) {
                                $actionLink = $this->createLink('gametaskinternal', 'batchRestore');
                                $misc = "onclick=\"setFormAction('$actionLink', 'hiddenwin', '#moreAction')\"";
                                echo html::linkButton($lang->gametaskinternal->restore, '#', 'self', $misc);
                            }
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
<?php js::set('checkedSummary', $lang->gametaskinternal->checkedSummary); ?>
<?php js::set('replaceID', 'taskList') ?>
