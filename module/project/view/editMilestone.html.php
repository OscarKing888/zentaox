<?php
/**
 * The html template file of add method of blog module of ZenTaoPHP.
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

<div class='container'>
    <div class='panel'>
        <div class='panel-heading'><strong><?php echo $lang->project->edit; ?></strong></div>
        <form method='post'>
            <table class='table table-borderless table-form' align='center'>
                <tr>
                    <th><?php echo $lang->project->milestone; ?></th>

                    <td colspan='2'>
                        <?php echo html::textarea('name', $milestone->name, "rows='1' class='form-control'");?>
                    </td>
                </tr>

                <tr>
                    <th><?php echo $lang->project->deadlineRequirement; ?></th>
                    <td>
                        <?php echo html::input('deadlineRequirement', $milestone->deadlineRequirement, "class='form-control form-date' placeholder=''"); ?>
                    </td>
                </tr>

                <tr>
                    <th><?php echo $lang->project->deadline; ?></th>
                    <td>
                        <?php echo html::input('deadline', $milestone->deadline, "class='form-control form-date' placeholder=''"); ?>
                    </td>
                </tr>

                <tr>
                    <th><?php echo $lang->project->deadlineQA; ?></th>
                    <td>
                        <?php echo html::input('deadlineQA', $milestone->deadlineQA, "class='form-control form-date' placeholder=''"); ?>
                    </td>
                </tr>

                <tr>
                    <th><?php echo $lang->project->opOnlineDate; ?></th>
                    <td>
                        <?php echo html::input('opOnlineDate', $milestone->opOnlineDate, "class='form-control form-date' placeholder=''"); ?>
                    </td>
                </tr>

                <tr>
                    <th></th>
                    <td>
                        <?php echo html::submitButton(); ?>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
