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
<?php include '../../common/view/kindeditor.html.php';?>
<?php include 'debug.html.php';?>

<div class='container'>
    <div class='panel'>
        <div class='panel-heading'>
            <strong> <?php echo $lang->blog->index; ?></strong>
            <div class='pull-right'> <?php echo html::a(inlink('create'), $lang->blog->add, "class='btn btn-primary btn-xs'"); ?></div>
        </div>
        <table class='table table-list table-hover'>
            <thead>
            <tr>
                <td class='text-center' width='150'><?php echo $lang->blog->date; ?></td>
                <td width='50'><?php echo $lang->project->manageProducts; ?></td>
                <td><?php echo $lang->blog->content; ?></td>
                <td class='text-center' width='120'><?php echo $lang->blog->action; ?></td>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($articles as $article): ?>
                <tr>
                    <td><?php echo formatTime("YYYY-MM-DD",$article->date); ?></td>
                    <td class='text-center'><?php echo $products[$article->product]; ?></td>
                    <td>
                        <fieldset>
                            <div class='content'>
                                <?php
                                $steps = $article->content;
                                //$steps = str_replace('<p></p>', '', $article->content);
                                echo "<font color=blue><b>raw:</b></font><dd>"; echo ($steps);
                                echo "<br><br>";
                                echo "<font color=blue><b>htmlspecialchars:</b></font><dd>"; echo htmlspecialchars($steps);
                                echo "<br><br>";
                                echo "<font color=blue><b>html_entity_decode:</b></font><dd>"; echo html_entity_decode($steps);
                                echo "<br><br>";
                                echo "<font color=blue><b>htmlentities:</b></font><dd>"; echo htmlentities($steps);
                                echo "<br><br>";
                                echo "<font color=blue><b>htmlspecialchars_decode:</b></font><dd>"; echo htmlspecialchars_decode($steps);
                                ?>
                            </div>
                        </fieldset>
                    </td>
                    <td>
                        <?php
                        //echo html::a($this->createLink('blog', 'view', "id=$article->id"), $lang->blog->view);
                        echo html::a($this->createLink('blog', 'edit', "id=$article->id"), $lang->blog->edit);
                        echo html::a($this->createLink('blog', 'delete', "id=$article->id"), $lang->blog->delete);
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
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
