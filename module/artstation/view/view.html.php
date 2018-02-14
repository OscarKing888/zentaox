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
            <form method='post'>
                <table class='table table-borderless table-form' align='center'>
                    <tr>
                        <th><?php echo $lang->user->account; ?></th>
                        <td>
                            <?php
                            /*
                            foreach ($allUsers as $u) {
                                echo $u;
                            }
                            echo $user;
                            //*/
                            ?>
                            <span id='assignedToBox'><?php echo html::select('user', $allUsers, $user, "class='form-control chosen'"); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <?php echo html::submitButton('查询'); ?>
                        </td>
                    </tr>
                </table>
            </form>

            <?php foreach ($articles as $article): ?>
                <fieldset>
                    <legend><?php echo formatTime("YYYY-MM-DD", $article->date) . "&nbsp;&nbsp;" . $products[$article->product]; ?></legend>

                    <div class='content'>
                        <?php
                        echo $article->content;
                        echo "<br>";
                        echo $article->contentimages;
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
