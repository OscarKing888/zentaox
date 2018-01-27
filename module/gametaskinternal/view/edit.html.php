<?php include '../../common/view/header.html.php'; ?>
<?php
include '../../common/view/chart.html.php';
include '../../common/view/datepicker.html.php';
include '../../common/view/datatable.fix.html.php';
include '../../common/view/kindeditor.html.php';
?>
<form class='form-condensed' method='post' enctype='multipart/form-data' target='hiddenwin' id='dataform'>

    <div id='titlebar'>
        <div class='heading'>
        <span class='prefix'><?php echo html::icon($lang->icons['task']); ?>
            <strong><?php echo $task->id; ?></strong></span>
            <strong style='color: <?php echo $task->color; ?>'>
                <?php echo $task->title; ?>
            </strong>
            <small><?php echo html::icon($lang->icons['edit']) . ' ' . $lang->task->edit; ?></small>
            <?php if ($task->deleted): ?>
                <span class='label label-danger'><?php echo $lang->task->deleted; ?></span>
            <?php endif; ?>
        </div>
        <div class='actions'>
            <?php echo html::submitButton($lang->save) ?>
        </div>
    </div>

    <div class='row-table'>
        <div class='col-main'>
            <div class='main'>

                <div class='form-group'>
                    <div class='input-group'>

                        <fieldset class='fieldset-pure'>
                            <legend><?php echo $lang->gametaskinternal->title; ?></legend>
                            <?php echo html::input('title', $task->title, 'class="form-control" autocomplete="off" placeholder="' . $lang->gametaskinternal->title . '"'); ?>
                        </fieldset>

                        <fieldset class='fieldset-pure'>
                            <legend><?php echo $lang->gametaskinternal->desc; ?></legend>
                            <div class='form-group'>
                                <?php echo html::textarea('desc', htmlspecialchars($task->desc), "rows='20' class='form-control'"); ?>
                            </div>
                        </fieldset>

                        <fieldset class='fieldset-pure'>
                            <legend><?php echo $lang->gametaskinternal->srcResPath; ?></legend>
                            <div class='form-group'>
                                <?php echo html::textarea('srcResPath', htmlspecialchars($task->srcResPath), "rows='20' class='form-control'"); ?>
                            </div>
                        </fieldset>

                        <fieldset class='fieldset-pure'>
                            <legend><?php echo $lang->gametaskinternal->gameResPath; ?></legend>
                            <div class='form-group'>
                                <?php echo html::textarea('gameResPath', htmlspecialchars($task->gameResPath), "rows='20' class='form-control'"); ?>
                            </div>
                        </fieldset>

                        <div class='actions actions-form'>
                            <?php
                            echo html::submitButton();
                            echo html::backButton();
                            //echo html::a(inlink('edit', "id=$task->id"), $lang->gametaskinternal->edit);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class='col-side'>
            <div class='main main-side'>

                <fieldset>
                    <legend><?php echo $lang->task->legendBasic; ?></legend>
                    <table class='table table-form'>
                        <tr>
                            <th class='w-80px'><?php echo $lang->gametaskinternal->product; ?></th>
                            <td><?php echo html::select('product', $allProducts, $task->product, 'class="form-control chosen"'); ?></td>
                        </tr>
                        <tr>
                            <th class='w-80px'><?php echo $lang->gametaskinternal->version; ?></th>
                            <td><?php echo html::select('version', $versions, $task->version, 'class="form-control chosen"'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->gametaskinternal->dept; ?></th>
                            <td><?php echo html::select('dept', $depts, $task->dept, 'class="form-control chosen"'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->gametaskinternal->owner; ?></th>
                            <td><?php echo html::select('owner', $users, $task->owner, 'class="form-control chosen"'); ?></td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->gametaskinternal->assignedTo; ?></th>
                            <td><?php echo html::select('assignedTo', $allUsers, $task->assignedTo, 'class="form-control chosen"'); ?></td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->gametaskinternal->width; ?></th>
                            <td><?php echo html::input('sizeWidth', $task->sizeWidth, "class='form-control'"); ?></td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->gametaskinternal->height; ?></th>
                            <td><?php echo html::input('sizeHeight', $task->sizeHeight, "class='form-control'"); ?></td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->gametaskinternal->workhour; ?></th>
                            <td><?php echo html::input('workhour', $task->workhour, "class='form-control'"); ?></td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->gametaskinternal->pri; ?></th>
                            <td><?php echo html::select("pri", (array)$config->gametaskinternal->priList, $task->pri, 'class=form-control chosen'); ?></td>
                        </tr>

                        <tr>
                            <th><?php echo "操作" ?></th>
                            <td>
                                <?php
                                echo html::submitButton();
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
</form>

<?php include '../../common/view/footer.html.php'; ?>
