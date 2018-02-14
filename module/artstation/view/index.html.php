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


<nav id="modulemenu">
    <ul class="nav">
        <li class="right" data-id="create">
            <?php
            $lnk = html::a(inlink('create'), "<i class='icon icon-plus'></i>" . $lang->artstation->add);
            echo $lnk;
            ?>
        </li>
    </ul>
</nav>
<br>
<div class='row-table'>
    <div class='col-main'>
        <div class='main'>

            <?php foreach ($articles as $article): ?>
                <?php
                foreach ($article->files as $file)
                {
                echo html::image($this->createLink('file', 'read', "fileID=$file->id"), "width='256' title='$file->title'");
                }
                ?>

            <?php endforeach; ?>

            <div align="right" class='content'>
                <?php
                $pager->show();
                ?>
            </div>
        </div>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
