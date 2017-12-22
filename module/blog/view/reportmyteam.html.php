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
<?php include '../../common/view/header.html.php'; ?>

<?php include 'debug.html.php'; ?>

<
<div class='row-table'>
    <div class='col-main'>
        <div class='main'>

            ================
            <?php
            foreach ($articles as $a)
            {
                echo $a;
            }
            ?>
            ================
            <?php
            $i = 0;
            foreach ($articles as $article): ?>
                <?php if ($i == 0) : ?>
                    <legend>
                        <?php echo $products[$article->product]
                            . "&nbsp;&nbsp;"
                            . $dept
                            . "&nbsp;&nbsp;"
                            . formatTime("YYYY-MM-DD", $article->date)
                            ;
                        $i++; ?>
                    </legend>
                <?php endif; ?>


                <?php
                echo $article->content;
                echo "<br>";
                ?>
            <?php endforeach; ?>

            <?php foreach ($articles as $article): ?>

                <?php
                $imgs = str_replace("<img", "<br><img", $article->contentimages);
                //$imgs = $imgs . "<br>";
                //$imgs = str_replace("<br>\n<br>", "<br>", $imgs);
                echo $imgs;
                //echo $article->contentimages;
                //echo "<br>";
                ?>
            <?php endforeach; ?>

        </div>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
