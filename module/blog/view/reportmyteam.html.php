<?php
/**
 * The html template file of view method of blog module of ZenTaoPHP.
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

<?php include 'debug.html.php'; ?>

<div class='row-table'>
    <div class='col-main'>
        <div class='main'>

            <form method='post'>
                <table class='table table-borderless table-form' align='center'>
                    <tr>
                        <th><?php echo $lang->product->name; ?></th>
                        <td>

                            <?php if ($this->config->blog->debug): ?>

                                =======<br>
                                <?php
                                foreach ($allProducts as $prod) {
                                    echo $prod;
                                }
                                ?>
                                <br>=======<br>
                                <?php
                                echo "product:" . $product;
                                echo "<br>allProducts:" . $allProducts[$product];
                                foreach ($_POST as $p) {
                                    echo $p;
                                }
                                ?>
                                <br>=======<br>

                            <?php endif; ?>

                            <?php echo html::select("product", $allProducts, $product, "class='form-control chosen' onchange=''"); ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->blog->date; ?></th>
                        <td>
                            <?php echo html::input('day', $day, "class='form-control form-date' placeholder=''"); ?>
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

            <?php echo $allProducts[$product]
                . "&nbsp;&nbsp;"
                //. $mydeptName . "]"
                . date('Y-m-d', strtotime($day))
                . "<br>";

            $userOtherProject = array();
            $deptArticles = array();
            $deptSubmittedUser = array();
            $userInfo = array();

            //echo "mydept:$mydept<br>";
            //echo "articles:" . count($articles) ."<br>";

            foreach (array_keys($depts) as $tdept)
            {
              //  echo "dept:$depts[$tdept]<br>";
                foreach ($articles as $article) {
                    //echo "$article->dept vs $tdept<br>";
                    if ($article->dept == $tdept) {
                        if ($article->product == $product) {
                            $deptArticles[$tdept][$article->id] = $article;
                            //echo "$article->id    $depts[$tdept]   $article->owner   $article->content <br>";
                        } else {
                            $userOtherProject[$article->owner] = $article;
                        }
                    }
                }
            }

            //echo "deptArticles:" . count($deptArticles) ."<br>";

            foreach ($userAbsent as $user) {
                $userInfo[$user->owner] = $user;
                //echo "userAbsent:$user->owner absent:$user->absent<br>";
            }
            ?>

            <table class="table" cellspacing="8">

                <?php foreach (array_keys($deptArticles) as $deptr): ?>

                <?php
                    echo "$deptr->id $deptr->owner   $deptr->content <br>";
                    ?>

                    <tr>
                        <td bgcolor="#a9a9a9" align='left' colspan="2">
                            <b><?php echo $depts[$mydept] ?></b>
                            <?php

                            $deptSubmittedUser = array();

                            if ($showstat) {
                                if (count($deptArticles[$mydept]) == count($deptusers[$mydept])) {
                                    echo "<font color='green'>" . count($deptArticles[$mydept]) . "/" . count($deptusers[$mydept]) . "</font>";
                                } else {
                                    echo "<font color='red'>" . count($deptArticles[$mydept]) . "/" . count($deptusers[$mydept]) . "</font>";
                                }
                            }
                            ?>
                        </td>
                    </tr>

                    <?php foreach ($deptArticles[$deptr] as $article): ?>
                        <?php
                        if ($showstat) {
                            //$cnt = str_replace("\n", "<br>", $article->content);
                            $cnt = nl2br(htmlspecialchars_decode($article->content));
                        } else {
                            $cnt = str_replace("\n", "/", $article->content);
                        }
                        ?>

                        <tr>
                            <td align="right" width="10%">
                                <strong>
                                    <?php
                                    $deptSubmittedUser[$article->account] = $article->account;
                                    if ($showstat) {
                                        echo $article->ownerrealname;
                                    }
                                    ?>
                                </strong>
                            </td>
                            <td><?php echo $cnt ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php
                    if ($showstat) {
                        foreach (array_keys($deptusers[$deptr]) as $dpuaccount) {
                            if (!array_key_exists($dpuaccount, $deptSubmittedUser)) {
                                $deptusernames = $deptusers[$deptr];

                                //foreach (array_keys($deptusernames) as $ddt) {echo "<tr><td>$ddt</td><td>$dpuaccount</td></tr>";}

                                echo " <tr><td align=\"right\" width=\"10%\"><font color='red'>$deptusernames[$dpuaccount]</font></td><td><font color='red'>";

                                // if user has other project blog
                                if (array_key_exists($dpuaccount, $userOtherProject)) {
                                    echo "《" . $allProducts[$userOtherProject[$dpuaccount]->product] . "》" . $userOtherProject[$dpuaccount]->content;
                                } else if (array_key_exists($dpuaccount, $userInfo)) { // if user has absent mark
                                    if($userInfo[$dpuaccount]->absent == 1) {
                                        echo $lang->blog->setUserAbsent;
                                        echo html::commonButton($lang->blog->removeUserAbsent, "id='removeUserAbsent_$dpuaccount' onclick=\"on_removeUserAbsent('$dpuaccount', '$day')\"");
                                    }
                                    else
                                    {
                                        echo html::commonButton( $lang->blog->setUserAbsent, "id='setUserAbsent_$dpuaccount' onclick=\"on_setUserAbsent('$dpuaccount', '$day')\"");
                                    }
                                    //echo html::a($this->createLink('blog', 'removeUserAbsent', "userid=$dpuaccount&day=$day"), $lang->blog->removeUserAbsent);
                                } else { // show set absent
                                    echo html::commonButton( $lang->blog->setUserAbsent, "id='createUserAbsent_$dpuaccount' onclick=\"on_createUserAbsent('$dpuaccount', '$day')\"");
                                    //echo html::a($this->createLink('blog', 'setUserAbsent', array($dpuaccount, $day)), $lang->blog->setUserAbsent);
                                }

                                echo "</font></td></tr>";
                            }
                        }
                    }
                    ?>

                <?php endforeach; ?>

                <tr>
                    <td bgcolor="#a9a9a9" align='left' colspan="2"><b>图片</b></td>
                </tr>

                <?php foreach (array_keys($deptArticles) as $tdeptr): ?>

                    <?php foreach ($deptArticles[$tdeptr] as $article): ?>
                        <?php if (!empty($article->contentimages)): ?>
                            <?php
                            //$imgs = str_replace("<img", "<br><img", $article->contentimages);
                            $imgs = nl2br($article->contentimages);
                            $imgs = htmlspecialchars_decode($imgs);
                            ?>

                            <tr>
                                <td align="right" width="10%"><strong><?php if ($showstat) {
                                            echo $article->ownerrealname;
                                        } ?></strong></td>
                                <td><?php echo $imgs ?></td>
                            </tr>
                        <?php endif; ?>

                    <?php endforeach; ?>

                <?php endforeach; ?>

            </table>

        </div>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
