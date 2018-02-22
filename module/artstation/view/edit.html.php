<?php
/**
 * The html template file of add method of blog module of ZenTaoPHP.
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

<div class='container'>
    <div class='panel'>
        <div class='panel-heading'><strong><?php echo $lang->artstation->add; ?></strong></div>
        <form class='form-condensed' method='post' enctype='multipart/form-data' id='dataform'>
            <table class='table table-form'>
                <tr>
                    <th><?php echo $lang->product->name; ?></th>
                    <td class='text-left' id='productsBox'>
                        <div class='row'>
                            <div class='col-sm-3'>
                                <div class='input-group'>
                                    <?php echo html::select("product", $allProducts, $article->product, "class='form-control chosen' onchange=''"); ?>

                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th><?php echo $lang->artstation->type; ?></th>
                    <td class='text-left' id='productsBox'>
                        <div class='row'>
                            <div class='col-sm-3'>
                                <div class='input-group'>
                                    <?php echo html::select("type", (array)$lang->artstation->typeList, $article->type, "class='form-control chosen' onchange=''"); ?>

                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th><?php echo $lang->artstation->title; ?></th>
                    <td><?php echo html::input("title", $article->title, "class='form-control text-left' autocomplete='on'"); ?></td>
                </tr>

                <tr>
                    <th><?php echo $lang->artstation->tags; ?></th>
                    <td><?php echo html::input("tags", $article->tags, "placeholder='用逗号分割，如:兽王,部落,boss' class='form-control text-left' autocomplete='on' placement='用,分割'"); ?></td>
                </tr>

                <tr>
                    <th><?php echo $lang->artstation->requirement; ?></th>
                    <td colspan='5'>
                        <div class='input-group'>
                            <?php echo html::select('requirement', $stories, $article->requirement, "class='form-control chosen' onchange='setStoryRelated();'"); ?>
                            <span class='input-group-btn' id='preview'><a href='#'
                                                                          class='btn iframe'><?php echo $lang->preview; ?></a></span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->artstation->content; ?></th>

                    <td>
                        <?php echo html::textarea('content', $article->content, "rows='5' class='form-control'"); ?>
                    </td>
                </tr>

                <tr>
                    <th><?php echo $lang->artstation->files; ?></th>
                    <td>
                        <?php echo $this->fetch('file', 'buildformsingle', 'fileCount=1&percent=0.85'); ?>
                    </td>
                </tr>

                <tr>
                    <th></th>
                    <td>
                        <?php echo html::submitButton(); ?>
                        <?php echo html::backButton(); ?>
                    </td>
                </tr>
            </table>
        </form>

    </div>

    <fieldset>
        <legend>历史版本</legend>
        <?php
        $files = array_reverse($article->files);
        $v = count($files);
        ?>

        <?php foreach ($files as $file): ?>

            <legend><?php echo "版本 - V." . ($v);
                $v--; ?></legend>
            <div class='content'>
                <?php echo html::image($this->createLink('file', 'readthumb', "fileID=$file->id"), "$imgAttr title='$file->title'"); ?>
            </div>
        <?php endforeach; ?>
    </fieldset>
</div>
<?php include '../../common/view/footer.html.php'; ?>
