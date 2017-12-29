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
                        <th><?php echo $lang->blog->date; ?></th>
                        <td>
                            <?php echo html::input('day', $day, "class='form-control form-date' placeholder=''"); ?>
                            <?php echo html::submitButton('查询'); ?>
                        </td>
                    </tr>
                </table>
            </form>

            <?php
            //echo $day;

            $i = 0;
            foreach ($articles as $article) {
                if ($i == 0 && ($article != null)) {
                    echo "<legend>";

                    echo $products[$article->product]
                        . "&nbsp;&nbsp;"
                        //. $article->date
                        . date('Y-m-d', strtotime($article->date))
                        //. date_format($article->date,'Y-m-d')
                    ;
                    $i++;

                    echo "</legend>";
                }
                //echo $article->content;
                echo str_replace("\n", "<br>", $article->content);
                echo "<br>";
            }

            foreach ($articles as $article) {
                $imgs = str_replace("<img", "<br><img", $article->contentimages);
                //$imgs = $imgs . "<br>";
                //$imgs = str_replace("<br>\n<br>", "<br>", $imgs);
                echo htmlspecialchars_decode($imgs);
                //echo ($imgs);
                //echo $article->contentimages;
                //echo "<br>";
            }

            ?>

        </div>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
