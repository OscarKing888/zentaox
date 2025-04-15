<?php
/**
 * The html template file of index method of pipeline module of ZenTaoPHP.
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
<?php include '../../common/view/kindeditor.html.php';?>

<?php include 'debug.html.php';?>

<div class='container'>
    <div class='panel'>
        <div class='panel-heading'>
            <strong> <?php echo $lang->pipeline->restore; ?></strong>

        </div>
        <table class='table table-list table-hover'>
            <thead>
            <tr class='text-center'>
                <td><strong> <?php echo $lang->pipeline->common; ?></strong></td>
                <td><strong> <?php echo $lang->pipeline->steps; ?></strong></td>
                <td class='text-center' width='120'><strong> <?php echo $lang->pipeline->action; ?></strong></td>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($articles as $article): ?>
                <tr class='text-center'>
                    <td class='text-center'><?php echo $article->pipename; ?></td>
                    <td>
                        <table class='table table-form'>
                            <thead>
                            <tr class='text-center'>
                                <th>ID</th><th>部门</th><th>预估工时（H）</th>
                            </tr>
                            </thead>

                            <?php
                            foreach($article->steps as $k => $val)
                            {
                                echo "<tr  class='text-center'><td>$val->desc </td><td>" . $depts[$val->dept] . "</td><td>" . $val->estimate . "(H)</td></tr>";
                            }
                            ?>
                        </table>
                    </td>
                    <td>
                        <?php
                        echo html::a($this->createLink('pipeline', 'restorepipeline', "id=$article->id"), $lang->pipeline->restore);
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tr>
                <td align="left" colspan="4">
                    <?php echo html::backButton();?>
                </td>
            </tr>
            <tfoot>
            <tr>
                <td colspan='4'>
                    <?php
                    $pager->show('right', 'short');
                    ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
