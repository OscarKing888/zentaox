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
        <div class='panel-heading'><strong><?php echo $title; ?></strong></div>
        <form method='post'>
            <table class='table table-borderless table-form' align='center'>
                <tr>
                    <th><?php echo $lang->books->bookName;?><span class='required'></span></th>
                    <td><?php echo html::input('bookName', $book->bookName, "class='form-control'"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->books->type; ?></th>
                    <td><?php echo html::select('type', $bookTypes, $book->type, 'class="form-control chosen"'); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->books->desc; ?></th>
                    <td><?php echo html::textarea('desc', $book->desc, "rows='30' class='form-control'");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->books->price; ?><span class='required'></span></th>
                    <td><?php echo html::input('price', $book->price, "class='form-control'"); ?></td>
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
