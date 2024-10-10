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
<?php include '../../common/view/kindeditor.html.php'; ?>
<?php include 'debug.html.php'; ?>

<div id='titlebar'>
    <div class='heading' align='right'>

        <?php
        $lnk = html::a(inlink('create'),
            "<i class='icon icon-plus'></i>" . $lang->pipeline->create,
            "", "class='btn'");
        echo $lnk;
        ?>
    </div>
</div>

<div class='row-table'>
    <div class='col-main'>
        <div class='main'>
            <?php foreach ($articles as $article): ?>

                <fieldset>
                    <legend><?php echo $article->id . " - " . $article->pipename; ?></legend>

                        <table class='table table-form'>
                            <thead>
                            <tr class='text-center'>
                                <th>ID</th><th>部门</th><th>预估工时（H）</th>
                            </tr>
                            </thead>

                        <?php
                        foreach($article->steps as $k => $val)
                        {
                            if($val->type == 'group')
                            {
                                echo "<tr  class='text-center'><td>$val->desc </td><td>" . $depts[$val->dept] . "</td><td>" . $val->estimate . "(H)</td></tr>";
                            }
                            else
                            {
                                echo "<tr  class='text-center'><td>$val->desc </td><td>" . $val->stepname . "</td><td>" . $val->estimate . "(H)</td></tr>";
                            }
                        }
                        ?>

                    </table>

                    <div align="right" class='content'>
                        <br>
                        <?php
                        //echo html::a($this->createLink('pipeline', 'view', "id=$article->id"), $lang->pipeline->view);
                        echo html::a($this->createLink('pipeline', 'edit', "id=$article->id"), $lang->pipeline->edit);
                        echo html::a($this->createLink('pipeline', 'delete', "id=$article->id"), $lang->pipeline->delete);
                        ?>
                    </div>
                </fieldset>


            <?php endforeach; ?>

            <div align="right" class='content'>
                <?php
                $pager->show('right', 'short');
                ?>
            </div>
        </div>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
