<?php
/**
 * The html template file of index method of artstation module of ZenTaoPHP.
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


<div class='container'>
    <div class='panel'>
        <div class='panel-heading'>
            <strong> <?php echo $lang->artstation->restore; ?></strong>

        </div>
        <table class='table table-list table-hover'>
            <thead>
            <tr>
                <td width='50'><?php echo $lang->product->name; ?></td>
                <td><?php echo $lang->artstation->content; ?></td>
                <td class='text-center' width='120'><?php echo $lang->artstation->action; ?></td>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($articles as $article): ?>
                <tr>
                    <td class='text-center'><?php echo $products[$article->product]; ?></td>
                    <td >
                        <fieldset>
                            <div class='content'>
                                <?php
                                echo $article->title;
                                echo $article->content;
                                ?>
                                <br>

                                <?php foreach ($articles as $article): ?>
                                    <?php
                                    foreach ($article->files as $file) {
                                        $imageSize  = getimagesize($file->realPath);
                                        $imageWidth = $imageSize ? $imageSize[0] : 256;
                                        $imageHeight = $imageSize ? $imageSize[1] : 256;
                                        $imgAttr = "";
                                        if($imageWidth > $imageHeight)
                                        {
                                            $imgAttr = " width='256' ";
                                        }
                                        else {
                                            $imgAttr = " height='256' ";
                                        }

                                        //echo "w:$imageWidth h:$imageHeight";
                                        echo html::image($this->createLink('file', 'readthumb', "fileID=$file->id"),  "$imgAttr title='$file->title'");
                                    }
                                    ?>

                                <?php endforeach; ?>
                            </div>
                        </fieldset>
                    </td>
                    <td>
                        <?php
                        echo html::a($this->createLink('artstation', 'restoreartstation', "id=$article->id"), $lang->artstation->restore);
                        echo html::a($this->createLink('artstation', 'view', "id=$article->id"), $lang->artstation->view);

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
                    $pager->show();
                    ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
