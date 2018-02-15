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
<?php include 'debug.html.php'; ?>


<nav id="modulemenu">
    <ul class="nav">
        <li class="right" data-id="create">
            <?php
            $lnk = html::a(inlink('create'), "<i class='icon icon-plus'></i>" . $lang->blog->add);
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

                <fieldset>
                    <legend><?php echo formatTime("YYYY-MM-DD", $article->date) . "&nbsp;&nbsp;" . $products[$article->product]; ?></legend>

                    <div class='content'>
                        <?php
                        //$cnt = str_replace("\n", "<br>", $article->content);
                        $cnt = nl2br($article->content);
                        echo $cnt;
                        echo "<br>";
                        //$imgs = str_replace("<img", "<br><img", $article->contentimages);
                        $imgs = nl2br($article->contentimages);
                        $imgs = htmlspecialchars_decode($imgs);
                        echo $imgs;
                        //$steps = $article->contentimages;
                        //$steps = str_replace('<p></p>', '', $article->content);
                        //$test = $this->loadModel('file')->replaceImgURL($steps, 'contentimages');
                        //echo $test;
                        //echo "<br><br>";

                        /*
                        echo "<font color=blue><b>raw:</b></font><dd>"; echo ($steps);
                        echo "<br><br>";
                        echo "<font color=blue><b>htmlspecialchars:</b></font><dd>"; echo htmlspecialchars($steps);
                        echo "<br><br>";
                        echo "<font color=blue><b>html_entity_decode:</b></font><dd>"; echo html_entity_decode($steps);
                        echo "<br><br>";
                        echo "<font color=blue><b>htmlentities:</b></font><dd>"; echo htmlentities($steps);
                        echo "<br><br>";
                        echo "<font color=blue><b>htmlspecialchars_decode:</b></font><dd>"; echo htmlspecialchars_decode($steps);
                        //*/
                        ?>
                    </div>

                    <div align="right" class='content'>
                        <br>
                        <?php
                        //echo html::a($this->createLink('blog', 'view', "id=$article->id"), $lang->blog->view);
                        echo html::a($this->createLink('blog', 'edit', "id=$article->id"), $lang->blog->edit);
                        echo html::a($this->createLink('blog', 'delete', "id=$article->id"), $lang->blog->delete);
                        ?>
                    </div>
                </fieldset>


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
