<?php
/**
 * The html template file of add method of timeline module of ZenTaoPHP.
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
        <div class='panel-heading'><strong><?php echo $title; ?></strong></div>
        <form method='post'>
            <table class='table table-borderless table-form' align='center'>
                <tr>
                    <th><?php echo $lang->timeline->title; ?></th>
                    <td><?php echo html::input("title", '', "class='form-control text-left' autocomplete='on'"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->timeline->datebegin; ?></th>
                    <td>
                        <?php echo html::input('datebegin', helper::today(), "class='form-control form-date' placeholder=''"); ?>
                    </td>
                </tr>


                <tr>
                    <th><?php echo $lang->timeline->dateend; ?></th>
                    <td>
                        <?php echo html::input('dateend', helper::today(), "class='form-control form-date' placeholder=''"); ?>
                    </td>
                </tr>

                <tr>
                    <th><?php echo $lang->timeline->type; ?></th>
                    <td class='text-left' id='typeBox'>
                        <?php echo html::select("type", (array)$lang->timeline->typeList, 0, "class='form-control chosen' onchange=''"); ?>
                    </td>
                </tr>

                <tr>
                    <th><?php echo $lang->timeline->tags; ?></th>
                    <td><?php echo html::input("tags", '', "placeholder='用英文逗号分割，如:“兽王,部落,boss”，只能用字母、汉字、数字！' class='form-control text-left' autocomplete='on' placement='用,分割'"); ?></td>
                </tr>



                <tr>
                    <th><?php echo $lang->timeline->content; ?></th>

                    <td colspan='2'>
                        <?php echo html::textarea('content', '', "rows='5' class='form-control'");?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->timeline->pic; ?></th>

                    <td colspan='2'>
                        <?php echo html::textarea('contentimages', '', "rows='30' class='form-control'");?>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <?php echo html::submitButton(); ?>
                        <?php echo html::backButton();?>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
