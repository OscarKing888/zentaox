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
            <strong> <?php echo $lang->blog->restore; ?></strong>

        </div>
        <table class='table table-list table-hover'>
            <thead>
            <tr>
                <td class='text-center' width='150'><?php echo $lang->blog->date; ?></td>
                <td width='50'><?php echo $lang->product->name; ?></td>
                <td><?php echo $lang->blog->content; ?></td>
                <td class='text-center' width='120'><?php echo $lang->blog->action; ?></td>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($articles as $article): ?>
                <tr>
                    <td><?php echo formatTime("YYYY-MM-DD",$article->date); ?></td>
                    <td class='text-center'><?php echo $products[$article->product]; ?></td>
                    <td>
                        <fieldset>
                            <div class='content'>
                                <?php
                                echo $article->content;
                                echo "<br>";
                                echo  $article->contentimages;
                                ?>
                            </div>
                        </fieldset>
                    </td>
                    <td>
                        <?php
                        echo html::a($this->createLink('blog', 'restoreblog', "id=$article->id"), $lang->blog->restore);
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
