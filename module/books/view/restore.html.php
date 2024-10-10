<?php
/**
 * The html template file of index method of blog module of ZenTaoPHP.
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
<?php include '../../common/view/kindeditor.html.php';?>

<div class='container'>
    <div class='panel'>
        <div class='panel-heading'>
            <strong> <?php echo $lang->books->restore; ?></strong>

        </div>
        <table class='table table-list table-hover'>
            <thead>
            <tr>
                <td width='200'><strong><?php echo $lang->books->bookName; ?></strong></td>
                <td><strong><?php echo $lang->books->desc; ?></strong></td>
                <td width='120'><strong><?php echo $lang->books->action; ?></strong></td>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($books as $book): ?>
                <tr>
                    <td>《<?php echo "$book->bookName"; ?>》</td>
                    <td>
                        <?php
                                $imgs = htmlspecialchars_decode($book->desc);
                                echo $imgs;
                                ?>
                    </td>
                    <td>
                        <?php
                        echo html::a($this->createLink('books', 'restorebook', "id=$book->id"), $lang->books->restore);
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tr>
                <td align="left" colspan="4">
                    <?php echo html::backButton();?>
                </td>
            </tr>
            <tfoot>
            <tr>
                <td colspan='4'>
                    <?php
                    $pager->show();
                    ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
