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

<script  type='text/javascript'>
    function onCheckShowStat() {
        var checkbox = document.getElementsByName("showstat");
        alert("checked:" + checkbox.checked);
    }
</script>

<div class='row-table'>
    <div class='col-main'>
        <div class='main'>

            <form method='post'>
                <table class='table table-borderless table-form' align='center'>
                    <tr>
                        <th><?php echo $lang->product->name; ?></th>
                        <td>

                            <?php if ($this->config->blog->debug): ?>

                                =======
                                <?php
                                foreach ($allProducts as $prod) {
                                    echo $prod . "<br>";
                                }
                                ?>
                                =======<br>
                                <?php
                                echo $product;
                                echo $allProducts[$product];
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
                        <th><?php echo $lang->blog->showstat;?></th>
                        <td>
                            <input type='checkbox' name='checkbox[]' id='showstat' onclick="" <?php echo "value='$showstat'";?> <?php if($showstat){echo " checked='checked'";}?>/>
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
                //. $article->date
                . date('Y-m-d', strtotime($day))
                . "<br>";

            $deptArticles = array();
            $deptSubmittedUser = array();
            foreach (array_keys($depts) as $dept) {
                foreach ($articles as $article) {
                    //echo "$article->dept vs $dept<br>";
                    if ($article->dept == $dept) {
                        $deptArticles[$dept][$article->id] = $article;
                        //echo "dept:$dept artdept:$article->dept artid:$article->id = $article->content <br>";
                    }
                }
            }
            ?>

            <table class="table" cellspacing="8">

                <?php foreach (array_keys($deptArticles) as $dept): ?>

                    <tr>
                        <td bgcolor="#a9a9a9" align='left' colspan="2">
                            <b><?php echo $depts[$dept] ?></b>
                            <?php

                            $deptSubmittedUser = array();

                            if($showstat)
                            {
                                if(count($deptArticles[$dept]) == count($deptusers[$dept]))
                                {
                                    echo  "<font color='green'>" . count($deptArticles[$dept]) . "/" . count($deptusers[$dept]) . "</font>";
                                }
                                else
                                {
                                    echo  "<font color='red'>" . count($deptArticles[$dept]) . "/" . count($deptusers[$dept]) . "</font>";
                                }
                            }
                            ?>
                        </td>
                    </tr>

                    <?php foreach ($deptArticles[$dept] as $article): ?>
                        <?php
                        if($showstat) {
                            $cnt = str_replace("\n", "<br>", $article->content);
                        }
                        else
                        {
                            $cnt = str_replace("\n", "/", $article->content);
                        }
                        ?>

                        <tr>
                            <td align="right" width="10%">
                                <strong>
                                    <?php
                                    $deptSubmittedUser[$article->account] = $article->account;
                                    if($showstat)
                                    {
                                        echo $article->ownerrealname;
                                    }
                                    ?>
                                </strong>
                            </td>
                            <td><?php echo $cnt ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php
                        if($showstat)
                        {
                            foreach (array_keys($deptusers[$dept]) as $dpuaccount)
                            {
                                if(!array_key_exists($dpuaccount, $deptSubmittedUser))
                                {
                                    $deptusernames = $deptusers[$dept];

                                    //foreach (array_keys($deptusernames) as $ddt) {echo "<tr><td>$ddt</td><td>$dpuaccount</td></tr>";}

                                    echo " <tr><td align=\"right\" width=\"10%\"><font color='red'>$deptusernames[$dpuaccount]</font></td><td></td></tr>";
                                }
                            }
                        }
                    ?>

                <?php endforeach; ?>

                <tr>
                    <td bgcolor="#a9a9a9" align='left' colspan="2"><b>图片</b></td>
                </tr>

                <?php foreach (array_keys($deptArticles) as $dept): ?>

                    <?php foreach ($deptArticles[$dept] as $article): ?>
                        <?php if (!empty($article->contentimages)): ?>
                            <?php
                            $imgs = str_replace("<img", "<br><img", $article->contentimages);
                            $imgs = htmlspecialchars_decode($imgs);
                            ?>

                            <tr>
                                <td align="right" width="10%"><strong><?php if($showstat) {echo $article->ownerrealname;} ?></strong></td>
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
