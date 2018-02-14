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
<?php include '../../common/view/kindeditor.html.php'; ?>

<div class='row-table'>
    <div class='col-main'>
        <div class='main'>

                <fieldset>

                    <legend><?php echo $article; echo $article->title ?></legend>

                    <div class='content'><?php echo $article->content;?></div>

                    <div class='content'>
                        <?php
                        $files = array_reverse($article->files);
                        foreach ($files as $file) {
                        echo html::image($this->createLink('file', 'read', "fileID=$file->id"),  "$imgAttr title='$file->title'");
                        }
                        ?>
                    </div>

                </fieldset>
        </div>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
