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
                                    <?php echo html::select("product", $allProducts, 1, "class='form-control chosen' onchange='reloadStoriesByProduct();'"); ?>

                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th><?php echo $lang->artstation->author; ?></th>
                    <td class='text-left' id='ownerBox'>
                        <?php echo html::select("owner", $deptUsers, $user, "class='form-control chosen' onchange=''"); ?>
                    </td>
                </tr>

                <tr>
                    <th><?php echo $lang->artstation->type; ?></th>
                    <td class='text-left' id='typeBox'>
                        <?php echo html::select("type", (array)$lang->artstation->typeList, 0, "class='form-control chosen' onchange=''"); ?>
                    </td>
                </tr>

                <tr>
                    <th><?php echo $lang->artstation->title; ?></th>
                    <td><?php echo html::input("title", '', "class='form-control text-left' autocomplete='on'"); ?></td>
                </tr>

                <tr>
                    <th><?php echo $lang->artstation->tags; ?></th>
                    <td><?php echo html::input("tags", '', "placeholder='用英文逗号分割，如:“兽王,部落,boss”，只能用字母、汉字、数字！' class='form-control text-left' autocomplete='on' placement='用,分割'"); ?></td>
                </tr>

                <tr>
                    <th><?php echo $lang->artstation->requirement; ?></th>
                    <td colspan='1'>
                        <div class='row-table'>
                            <div class='col-table'>
                                <div class='input-group w-p100'>
                                    <?php echo html::select('story', $stories, 0, "class='form-control chosen' onchange='setStoryRelated();'"); ?>

                                    <span class='input-group-btn' id='preview'><a href='#'
                                                                                  class='btn iframe'><?php echo $lang->preview; ?></a></span>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th><?php echo $lang->artstation->content; ?></th>

                    <td>
                        <?php echo html::textarea('content', '', "rows='5' class='form-control'"); ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->artstation->files; ?></th>
                    <td><?php echo $this->fetch('file', 'buildformsingle', 'fileCount=1&percent=0.85'); ?></td>
                </tr>
                <tr>
                    <th></th>
                    <td align="left">
                        <div align="left">建议的画布尺寸,坚版长宽互换即可，16:9或9:16</div>
                        <div class="red" align="left">7680 x 4320（8K）</div>
                        <div class="red" align="left">3840 x 2160（4K）</div>
                        <div class="red" align="left">2560 x 1440（2K）</div>
                        <div class="red" align="left">1920 x 1080（1K）</div>
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
</div>
<?php $customLink = $this->createLink('custom', 'ajaxSaveCustomFields', 'module=task&section=custom&key=createFields') ?>
<?php include '../../common/view/customfield.html.php'; ?>
<?php include '../../common/view/footer.html.php'; ?>
