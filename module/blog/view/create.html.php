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
        <div class='panel-heading'><strong><?php echo $lang->blog->add; ?></strong></div>
        <form method='post'>
            <table class='table table-borderless table-form' align='center'>
                <tr>
                    <th><?php echo $lang->product->name; ?></th>
                    <td class='text-left' id='productsBox' colspan="2">
                        <div class='row'>
                            <div class='col-sm-3'>
                                <div class='input-group'>
                                    <?php echo html::select("product", $allProducts, 1, "class='form-control chosen' onchange=''"); ?>
                                    <span class='input-group-addon fix-border' style='padding:0px'></span>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->blog->date; ?></th>
                    <td>
                        <?php echo html::input('date', helper::now(), "class='form-control form-date' placeholder=''"); ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->blog->content; ?></th>

                    <td colspan='2'>
                        <?php echo html::textarea('content', '', "rows='5' class='form-control'");?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->blog->pic; ?></th>

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
