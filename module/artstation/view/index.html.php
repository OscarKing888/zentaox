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

<div id='titlebar'>
    <div class='heading' align='right'>

        <?php
        $lnk = html::a(inlink('create'),
            "<i class='icon icon-plus'></i>" . $lang->artstation->add,
            "", "class='btn'");
        echo $lnk;
        ?>
    </div>
</div>

<?php include 'imagethumbview.html.php'; ?>

<?php include '../../common/view/footer.html.php'; ?>
