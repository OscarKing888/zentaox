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
            $lnk = html::a(inlink('create'), "<i class='icon icon-plus'></i>" . $lang->books->add);
            echo $lnk;
            ?>
        </li>
    </ul>
</nav>
<br>
<div class='row-table'>
    <div class='col-main'>
        <div class='main'>
            <?php foreach ($books as $book): ?>

                <fieldset>
                    <legend><?php echo $book->id; ?> - 《<?php echo $book->bookName; ?>》 - <?php echo $config->books->typeList[$book->type]; ?></legend>

                    <div class='content'>
                        <?php
                        $imgs = htmlspecialchars_decode($book->desc);
                        echo $imgs;
                        ?>
                    </div>


                    <div align="right" class='content'>
                        <br>
                        <?php
                        if(!array_key_exists($book->id, $borrowLogs))
                        {
                            echo html::a($this->createLink('books', 'borrow', "id=$book->id"), $lang->books->borrow);
                        }
                        else if(array_key_exists($user, $librarians)) {

                            echo "<div class='red'>";
                            if(array_key_exists($book->id, $borrowLogs))
                            {
                                $borrowLog = $borrowLogs[$book->id];
                                $borrowUser = $borrowLog->reader;
                                $readerName = $allUsers[$borrowUser];

                                echo formatTime("YYYY-MM-DD", $borrowLog->borrowDate) . "&nbsp;&nbsp;&nbsp;";
                                echo "已被<strong>$readerName</strong>";
                                if($borrowLog->borrowDays == 0)
                                {
                                    echo $lang->books->borrowLongdays;
                                }
                                else
                                {
                                    echo "借阅 " . sprintf($lang->books->borrowFixedDays, $borrowLog->borrowDays);
                                }
                            }
                            echo "</div>";

                            echo html::a($this->createLink('books', 'returnBook', "id=$book->id"), $lang->books->return);
                        }

                        if(array_key_exists($user, $librarians)) {
                            echo html::a($this->createLink('books', 'edit', "id=$book->id"), $lang->books->edit);
                            echo html::a($this->createLink('books', 'delete', "id=$book->id"), $lang->books->delete);
                        }

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
