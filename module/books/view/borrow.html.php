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

        <legend>
            <div class="red" align="center">损坏或遗失需要补偿同样书籍一本，如为绝版书赔偿5倍原价！</div>
        </legend>

        <form method='post'>
            <?php echo html::input('bookid', $book->id, "class='form-control hidden'"); ?>
            <?php echo html::input('reader', $user, "class='form-control hidden'"); ?>

            <table class='table table-borderless table-form' align='center'>
                <tr>
                    <th><?php echo $lang->books->bookName;?></th>
                    <td>《<?php echo $book->bookName; ?>》</td>
                </tr>
                <tr>
                    <th><?php echo $lang->books->type; ?></th>
                    <td><?php echo $bookTypes[$book->type]; ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->books->desc; ?></th>
                    <td><?php echo htmlspecialchars_decode($book->desc);?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->books->price; ?></th>
                    <td><?php echo $book->price; ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->books->borrowdays; ?><span class='required'></span></th>
                    <td><?php echo html::input('borrowDays', "", "class='form-control'"); ?></td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <?php echo html::submitButton("借阅"); ?>
                        <?php echo html::backButton();?>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
