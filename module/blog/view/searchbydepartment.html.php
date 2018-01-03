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
                        <th><?php echo $lang->dept->name; ?></th>
                        <td>

                            <?php
                            //echo $dept;
                            echo html::select("dept", $depts, $dept, "class='form-control chosen' onchange=''");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->product->name; ?></th>
                        <td>

                            <?php if($this->config->blog->debug):?>

                                =======
                                <?php
                                foreach ($allProducts as $prod)
                                {
                                    echo "$prod<br>";
                                }
                                ?>
                                =======<br>
                                <?php
                                echo $allProducts[$product];
                                ?>
                                <br>=======<br>

                            <?php endif;?>


                            <?php
                            //echo $product;
                            echo html::select("product", $allProducts, $product, "class='form-control chosen' onchange=''");
                            ?>
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

            <table width="100%" border="1" cellspacing="8" cellpadding="8" bordercolor="#dddddd">
                <thead>
                <tr>
                    <td colspan="2" bgcolor="#dddddd">
                        <strong>
                            <?php echo $allProducts[$product]
                                . "&nbsp;&nbsp;"
                                . $depts[$dept]
                                . "&nbsp;&nbsp;"
                                . date('Y-m-d', strtotime($day));
                            ?>
                        </strong>
                    </td>
                </tr>
                </thead>

            <?php
            //echo $day;


            foreach ($articles as $article) {

                //echo $article->content;
                echo "<tr><td width='20%' align='right'><strong>$article->ownerrealname</strong></td>";
                $cnt = str_replace("\n", "<br>", $article->content);
                echo"<td>$cnt</td></tr>";
                //echo "<br>";
            }

            foreach ($articles as $article) {
                if(!empty($article->contentimages))
                {
                    $imgs = str_replace("<img", "<br><img", $article->contentimages);
                    $imgs = htmlspecialchars_decode($imgs);
                    echo "<tr><td width='20%' align='right'><strong>$article->ownerrealname</strong></td>";
                    echo"<td>$imgs</td></tr>";
                }
                //$imgs = $imgs . "<br>";
                //$imgs = str_replace("<br>\n<br>", "<br>", $imgs);

                //echo ($imgs);
                //echo $article->contentimages;
                //echo "<br>";
            }

            ?>

            </table>

        </div>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
