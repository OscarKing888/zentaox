<?php
/**
 * The html template file of view method of artstation module of ZenTaoPHP.
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

<div class='row-table'>
    <div class='col-main'>
        <div class='main'>

            <form method='post'>
                <table class='table table-borderless table-form' align='center'>

                    <tr>
                        <th><?php echo $lang->product->name; ?></th>
                        <td>
                            <?php
                            echo html::select("product", $allProducts, $product, "class='form-control chosen' onchange=''");
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <th><?php echo $lang->user->account; ?></th>
                        <td>
                            <?php
                            /*
                            foreach ($allUsers as $u) {
                                echo $u;
                            }
                            echo $user;
                            //*/
                            ?>
                            <span id='assignedToBox'><?php echo html::select('user', $allUsers, $user, "class='form-control chosen'"); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->artstation->type; ?></th>
                        <td class='text-left' id='typeBox'>
                            <?php echo html::select("type", (array)$lang->artstation->typeList, $type, "class='form-control chosen' onchange=''"); ?>
                        </td>
                    </tr>

                    <tr>
                        <th><?php echo $lang->artstation->tags; ?></th>
                        <td class='text-left' id='tagsbox'>
                            <?php echo html::input("tags", $tags, "class='form-control'"); ?>
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

            <?php include 'imagethumbview.html.php'; ?>

        </div>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
