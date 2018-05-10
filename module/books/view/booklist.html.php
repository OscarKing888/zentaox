<div class='row-table'>
    <div class='col-main'>
        <div class='main'>
            <?php foreach ($books as $book): ?>

                <fieldset>
                    <legend><?php echo $book->id; ?> - 《<?php echo $book->bookName; ?>》
                        - <?php echo $config->books->typeList[$book->type]; ?></legend>

                    <div class='content'>
                        <?php
                        $imgs = htmlspecialchars_decode($book->desc);
                        echo html::a(inlink('view', "id=$book->id"), $imgs);
                        ?>
                    </div>


                    <div align="right" class='content'>

                        <?php

                        if (!array_key_exists($book->id, $borrowLogs)) {
                            echo html::a($this->createLink('books', 'borrow', "id=$book->id"), $lang->books->borrow);
                        } else {
                            echo "<div>";
                            if (array_key_exists($book->id, $borrowLogs)) {
                                $borrowLog = $borrowLogs[$book->id];
                                $borrowUser = $borrowLog->reader;
                                $readerName = $allUsers[$borrowUser];

                                echo formatTime("YYYY-MM-DD", $borrowLog->borrowDate) . "&nbsp;&nbsp;&nbsp;";
                                echo "已被&nbsp;<span class='red'><strong>$readerName</strong>";
                                if ($borrowLog->borrowDays == 0) {
                                    echo $lang->books->borrowLongdays;
                                } else {
                                    echo "</span>&nbsp;借阅 " . sprintf($lang->books->borrowFixedDays, $borrowLog->borrowDays);
                                }
                            }
                            echo "</div>";
                        }

                        if (array_key_exists($user, $librarians)) {
                            if (array_key_exists($book->id, $borrowLogs)) {
                                echo html::a($this->createLink('books', 'returnBook', "id=$book->id"), $lang->books->return);
                            }
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
