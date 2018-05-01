<?php
/**
 * The html template file of add method of pipeline module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
?>
<?php
include '../../common/view/header.html.php';
include '../../common/view/form.html.php';
include '../../common/view/kindeditor.html.php';
include '../../common/view/datepicker.html.php';
?>
<?php
//js::alert("init create" . count($steps));
?>

<div class='container'>
    <div class='panel'>
        <div class='panel-heading'><strong><?php echo $lang->pipeline->create; ?></strong></div>
        <form class='form-condensed' method='post' enctype='multipart/form-data' id='dataform' data-type='ajax'>
            <table class='table table-form'>
                <tr>
                    <th><?php echo $lang->pipeline->name; ?></th>
                    <td class='text-left' id='nameBox' colspan="1">
                        <?php echo html::input('pipename', $pipename, "class='form-control minw-60px' autocomplete='off'"); ?>
                    </td>
                </tr>

                <tr>
                    <th><?php echo $lang->testcase->steps;?></th>
                    <td colspan='2'>
                        <table class='table table-form mg-0 table-bordered' style='border: 1px solid #ddd'>
                            <thead>
                            <tr>
                                <th class='w-40px text-right'><?php echo "stage name";?></th>
                                <th width="45%"><?php echo "cost time";?></th>
                                <th><?php echo "???";?></th>
                                <th class='step-actions'><?php echo $lang->actions;?></th>
                            </tr>
                            </thead>
                            <tbody id='steps' class='sortable' data-group-name='<?php echo $lang->testcase->groupName ?>'>
                            <tr class='template step' id='stepTemplate'>
                                <td class='step-id'></td>
                                <td>
                                    <div class='input-group'>
                                        <span class='input-group-addon step-item-id'></span>
                                        <textarea rows='1' class='form-control autosize step-steps' name='steps[]'></textarea>
                                        <span class="input-group-addon step-type-toggle">
                                          <input type='hidden' name='stepType[]' value='item' class='step-type'>
                                          <label class="checkbox-inline"><input tabindex='-1' type="checkbox" class='step-group-toggle'> <?php echo $lang->testcase->group ?></label>
                                        </span>
                                    </div>
                                </td>
                                <td><textarea rows='1' class='form-control autosize step-expects' name='expects[]'></textarea></td>
                                <td class='step-actions'>
                                    <div class='btn-group'>
                                        <button type='button' class='btn btn-step-add' tabindex='-1'><i class='icon icon-plus'></i></button>
                                        <button type='button' class='btn btn-step-move' tabindex='-1'><i class='icon icon-move'></i></button>
                                        <button type='button' class='btn btn-step-delete' tabindex='-1'><i class='icon icon-remove'></i></button>
                                    </div>
                                </td>
                            </tr>
                            <?php
                            //echo count($steps)
                            ?>
                            <?php foreach($steps as $stepID => $step):?>
                                <tr class='step'>
                                    <td class='step-id'></td>
                                    <td>
                                        <div class='input-group'>
                                            <span class='input-group-addon step-item-id'></span>
                                            <?php echo html::textarea('steps[]', $step->desc, "rows='1' class='form-control autosize step-steps'") ?>
                                            <span class='input-group-addon step-type-toggle'>
                                                <?php if(!isset($step->type)) $step->type = 'step';?>
                                                <input type='hidden' name='stepType[]' value='<?php echo $step->type;?>' class='step-type'>
                                                <label class="checkbox-inline"><input tabindex='-1' type="checkbox" class='step-group-toggle'<?php if($step->type === 'group') echo ' checked' ?>>
                                                    <?php echo $lang->testcase->group ?>
                                                </label>
                                            </span>
                                        </div>
                                    </td>
                                    <td><?php echo html::textarea('expects[]', $step->expect, "rows='1' class='form-control autosize step-expects'") ?></td>
                                    <td class='step-actions'>
                                        <div class='btn-group'>
                                            <button type='button' class='btn btn-step-add' tabindex='-1'><i class='icon icon-plus'></i></button>
                                            <button type='button' class='btn btn-step-move' tabindex='-1'><i class='icon icon-move'></i></button>
                                            <button type='button' class='btn btn-step-delete' tabindex='-1'><i class='icon icon-remove'></i></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </td>
                </tr>


                <tr>
                    <th></th>
                    <td class='text-center'>
                        <?php echo html::submitButton(); ?>
                        <?php echo html::backButton(); ?>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
