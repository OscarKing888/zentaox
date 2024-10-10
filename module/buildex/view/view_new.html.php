<?php
/**
 * The create view of build module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     build
 * @version     $Id: create.html.php 4728 2013-05-03 06:14:34Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php js::set('confirmUnlinkStory', $lang->build->confirmUnlinkStory)?>
<?php js::set('confirmUnlinkBug', $lang->build->confirmUnlinkBug)?>
<?php js::set('flow', $this->config->global->flow)?>
<?php if(isonlybody()):?>
    <style>
        #stories .action{display:none;}
        #bugs .action{display:none;}
        tbody tr td:last-child a{display:none;}
        tbody tr td:first-child input{display:none;}
        tfoot tr td .table-actions .btn{display:none;}
        #titlebar .actions{display:none}
        .row-table .col-side{display:none;}
    </style>
<?php endif;?>
<div class='container'>
    <div id='titlebar'>
        <div class='heading'>
            <span class='prefix'><?php echo html::icon($lang->icons['build']);?></span>
            <strong><small class='text-muted'><?php echo html::icon($lang->icons['create']);?></small> <?php echo $lang->buildex->view;?></strong>
        </div>
        <div class='actions'>
            <?php
            $browseLink = $this->session->buildList ? $this->session->buildList : $this->createLink('project', 'build', "projectID=$buildObj->project");
            if(!$buildObj->deleted)
            {
                if($this->config->global->flow != 'onlyTest')
                {
                    echo "<div class='btn-group'>";
                    if(common::hasPriv('buildex', 'linkStory')) echo html::a(inlink('view', "buildID=$buildObj->id&type=story&link=true"), '<i class="icon-link"></i> ' . $lang->buildex->linkStory, '', "class='btn'");
                    if(common::hasPriv('buildex', 'linkBug'))   echo html::a(inlink('view', "buildID=$buildObj->id&type=bug&link=true"), '<i class="icon-bug"></i> ' . $lang->buildex->linkBug, '', "class='btn'");
                    echo '</div>';
                }
                echo "<div class='btn-group'>";
                common::printIcon('buildex', 'edit',   "buildID=$buildObj->id", $build);
                common::printIcon('buildex', 'delete', "buildID=$buildObj->id", $build, 'button', '', 'hiddenwin');
                echo '</div>';
            }
            echo common::printRPN($browseLink);
            ?>
        </div>
    </div>
    <form class='form-condensed' method='post' target='hiddenwin' id='dataform' enctype='multipart/form-data'>
        <fieldset>
            <legend><?php echo $lang->buildex->basicInfo?></legend>
            <table class='table table-form'>
                <tr>
                    <th class='w-150px'><?php echo $lang->buildex->product;?></th>
                    <?php if($products):?>
                        <td>
                            <div class='input-group'>
                                <?php echo html::select('product', $products, $product->id, "onchange='loadBranches(this.value);' class='form-control chosen'");?>
                                <?php
                                if($product->type != 'normal')
                                {
                                    if($product->branch) $branches = array($product->branch => $branches[$product->branch]);
                                    echo "<span class='input-group-addon fix-padding fix-border'></span>" . html::select('branch', $branches, $product->branch, "class='form-control' style='width:100px; display:inline-block;'");
                                }
                                ?>
                            </div>
                        </td>
                        <td></td>
                    <?php else:?>
                        <td colspan='2'><?php if(empty($products)) printf($lang->buildex->noProduct, $this->createLink('project', 'manageproducts', "projectID=$projectID&from=buildCreate"));?></td>
                    <?php endif;?>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->name;?></th>
                    <td><?php echo $buildObj->name;?></td>
                </tr>

                <tr>
                    <th><?php echo $lang->buildex->shippingType;?></th>
                    <td><?php echo $lang->buildex->shippingTypeList[$buildObj->shippingType];?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->date;?></th>
                    <td><?php echo $buildObj->date;?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->svnTagOperator;?></th>
                    <td><?php echo $users[$buildObj->svnTagOperator];?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->srcSVNPath;?></th>
                    <td><?php echo $buildObj->srcSVNPath?></td>
                </tr>

                <tr>
                    <th><?php echo $lang->buildex->desc;?></th>
                    <td colspan='2'><?php echo $buildObj->desc;?></td>
                </tr>
            </table>
        </fieldset>


        <fieldset>
            <legend><?php echo $lang->buildex->buildServerOption; ?></legend>
            <table class='table table-form'>

                <tr>
                    <th><?php echo $lang->buildex->buildServerImageBuilder;?></th>
                    <td><?php echo $users[$buildObj->buildServerImageBuilder];?></td>
                </tr>
                <tr>
                    <th class='w-150px'><?php echo $lang->buildex->buildServerImage;?></th>
                    <td><input type='checkbox' id="buildServerImage" disabled="disabled" <?php echo $buildObj->buildServerImage ? "checked='checked'":"";?>></td>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->buildServerImagePath;?></th>
                    <td><?php echo $buildObj->buildServerImagePath?></td>
                </tr>
            </table>
        </fieldset>


        <fieldset>
            <legend><?php echo $lang->buildex->buildAndroidPackage;?></legend>
            <table class='table table-form'>
                <tr>
                    <th><?php echo $lang->buildex->buildApkBuilder;?></th>
                    <td><?php echo $users[$buildObj->buildApkBuilder];?></td>
                </tr>
                <tr>
                    <th class='w-150px'><?php echo $lang->buildex->buildApk;?></th>
                    <td><input type='checkbox' id="buildApk" disabled="disabled" <?php echo $buildObj->buildApk ? "checked='checked'":"";?>></td>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->buildApkPath;?></th>
                    <td><?php echo $buildObj->buildApkPath?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->buildApkPathReleasePath;?></th>
                    <td><?php echo $buildObj->buildApkPathReleasePath?></td>
                </tr>
            </table>
        </fieldset>

        <fieldset>
            <legend><?php echo $lang->buildex->buildiOSPackage;?></legend>
            <table class='table table-form'>
                <tr>
                    <th><?php echo $lang->buildex->buildIosBuilder;?></th>
                    <td><?php echo $users[$buildObj->buildIosBuilder];?></td>
                </tr>
                <tr>
                    <th class='w-150px'><?php echo $lang->buildex->buildIos;?></th>
                    <td><input type='checkbox' id="buildIos" disabled="disabled" <?php echo $buildObj->buildIos ? "checked='checked'":"";?>></td>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->buildIosPath;?></th>
                    <td><?php echo $buildObj->buildIosPath?></td>
                </tr>
            </table>
        </fieldset>


        <fieldset>
            <legend><?php echo $lang->buildex->buildHotpatch;?></legend>
            <table class='table table-form'>
                <tr>
                    <th><?php echo $lang->buildex->buildHotpatchBuilder;?></th>
                    <td><?php echo $users[$buildObj->buildHotpatchBuilder];?></td>
                </tr>
                <tr>
                    <th class='w-150px'><?php echo $lang->buildex->buildHotpatchAndroid;?></th>
                    <td><input type='checkbox' id="buildHotpatchAndroid" disabled="disabled" <?php echo $buildObj->buildHotpatchAndroid ? "checked='checked'":"";?>></td>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->buildHotpatchAndroidPath;?></th>
                    <td><?php echo $buildObj->buildHotpatchAndroidPath?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->buildHotpatchiOS;?></th>
                    <td><input type='checkbox' id="buildHotpatchiOS" disabled="disabled" <?php echo $buildObj->buildHotpatchiOS ? "checked='checked'":"";?>></td>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->buildHotpatchiOSPath;?></th>
                    <td><?php echo $buildObj->buildHotpatchiOSPath?></td>
                </tr>
            </table>
        </fieldset>

        <fieldset>
            <legend><?php echo $lang->buildex->qaOption; ?></legend>
            <table class='table table-form'>
                <tr>
                    <th class='w-150px'><?php echo $lang->buildex->qaSmoke;?></th>
                    <td><input type='checkbox' id="qaSmoke" disabled="disabled" <?php echo $buildObj->qaSmoke ? "checked='checked'":"";?>></td>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->qaSmokeFull;?></th>
                    <td><input type='checkbox' id="qaSmokeFull" disabled="disabled" <?php echo $buildObj->qaSmokeFull ? "checked='checked'":"";?>></td>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->qaFunction;?></th>
                    <td><input type='checkbox' id="qaFunction" disabled="disabled" <?php echo $buildObj->qaFunction ? "checked='checked'":"";?>></td>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->qaFull;?></th>
                    <td><input type='checkbox' id="qaFull" disabled="disabled" <?php echo $buildObj->qaFull ? "checked='checked'":"";?>></td>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->qaRegTesting;?></th>
                    <td><input type='checkbox' id="qaRegTesting" disabled="disabled" <?php echo $buildObj->qaRegTesting ? "checked='checked'":"";?>></td>
                </tr>
            </table>
        </fieldset>



    </form>
</div>
<?php js::set('productGroups', $productGroups)?>
<?php include '../../common/view/footer.html.php';?>
