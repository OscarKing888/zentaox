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
                        <th><?php echo $lang->project->manageProducts; ?></th>
                        <td>

                            <?php if($this->config->blog->debug):?>

                                =======<br>
                                <?php
                                foreach ($products as $prod)
                                {
                                    echo $prod;
                                }
                                ?>
                                <br>=======<br>
                                <?php
                                echo $products[$product];
                                ?>
                                <br>=======<br>

                            <?php endif;?>

                            <?php echo html::select("product", $products, $product, "class='form-control chosen' onchange=''"); ?>
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

            <table class='table table-data table-fixed' border="1">
            <?php
            //echo $day;

            $i = 0;
            foreach ($articles as $article) {
                if ($i == 0 && ($article != null)) {
                    echo "<legend>";

                    echo $products[$article->product]
                        . "&nbsp;&nbsp;"
                        . $dept
                        . "&nbsp;&nbsp;"
                        . date('Y-m-d', strtotime($article->date))
                        ;
                    $i++;

                    echo "</legend>";
                }
                //echo $article->content;
                echo "<tr><th>$article->ownerrealname</th>";
                $cnt = str_replace("\n", "<br>", $article->content);
                echo"<td>$cnt</td></tr>";
                //echo "<br>";
            }

            foreach ($articles as $article) {
                if(!empty($article->contentimages))
                {
                    $imgs = str_replace("<img", "<br><img", $article->contentimages);
                    $imgs = htmlspecialchars_decode($imgs);
                    echo "<tr><th>$article->ownerrealname</th>";
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
