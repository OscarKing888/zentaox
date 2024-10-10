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
<div class='container'>
    <div id='titlebar'>
        <div class='heading'>
            <span class='prefix'><?php echo html::icon($lang->icons['build']);?></span>
            <strong><small class='text-muted'><?php echo html::icon($lang->icons['create']);?></small> <?php echo $lang->buildex->edit;?></strong>
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
                    <td class='w-p25-f'>
                        <?php echo html::input('name', $buildObj->name, "class='form-control' autocomplete='off'");?>
                    </td>
                    <td>
                        <?php if($lastBuild):?>
                            <div class='help-block'> &nbsp; <?php echo $lang->buildex->last . ': <strong>' . $lastBuild->name . '</strong>';?></div>
                        <?php endif;?>
                    </td>
                </tr>

                <tr>
                    <th><?php echo $lang->buildex->shippingType;?></th>
                    <td colspan='1'><?php echo html::select('shippingType', $lang->buildex->shippingTypeList,  $buildObj->shippingType, "class='form-control onchange=onChangeShippingType(this.value)'");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->date;?></th>
                    <td><?php echo html::input('date', $buildObj->date, "class='form-control form-date'");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->svnTagOperator;?></th>
                    <td><?php echo html::select('svnTagOperator', $users, $buildObj->svnTagOperator, 'class="form-control chosen"');?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->srcSVNPath;?></th>
                    <td colspan='2'><?php echo html::input('srcSVNPath', $buildObj->srcSVNPath, "class='form-control' autocomplete='off' placeholder='svn://172.20.1.248/thewar/branches/Release/V#.#.#'");?></td>
                </tr>

                <tr>
                    <th><?php echo $lang->buildex->desc;?></th>
                    <td colspan='2'><?php echo html::textarea('desc', $buildObj->desc, "rows='10' class='form-control'");?></td>
                </tr>
            </table>
        </fieldset>


        <fieldset>
            <legend><?php echo $lang->buildex->buildServerOption; ?></legend>
            <table class='table table-form'>
                <tr>
                    <th class='w-150px'><?php echo $lang->buildex->buildServerImage;?></th>
                    <td><?php echo html::checkboxBool('buildServerImage', $buildObj->buildServerImage) ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->buildServerImageBuilder;?></th>
                    <td><?php echo html::select('buildServerImageBuilder', $users, $buildObj->buildServerImageBuilder, 'class="form-control chosen"');?></td>
                </tr>
            </table>
        </fieldset>


        <fieldset>
            <legend><?php echo $lang->buildex->buildAndroidPackage;?></legend>
            <table class='table table-form'>
                <tr>
                    <th class='w-150px'><?php echo $lang->buildex->buildApk;?></th>
                    <td><?php echo html::checkboxBool('buildApk', $buildObj->buildApk) ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->buildApkBuilder;?></th>
                    <td><?php echo html::select('buildApkBuilder', $users, $buildObj->buildApkBuilder, 'class="form-control chosen"');?></td>
                </tr>
            </table>
        </fieldset>

        <fieldset>
            <legend><?php echo $lang->buildex->buildiOSPackage;?></legend>
            <table class='table table-form'>
                <tr>
                    <th class='w-150px'><?php echo $lang->buildex->buildIos;?></th>
                    <td><?php echo html::checkboxBool('buildIos', $buildObj->buildIos) ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->buildIosBuilder;?></th>
                    <td><?php echo html::select('buildIosBuilder', $users, $buildObj->buildIosBuilder, 'class="form-control chosen"');?></td>
                </tr>
            </table>
        </fieldset>


        <fieldset>
            <legend><?php echo $lang->buildex->buildHotpatch;?></legend>
            <table class='table table-form'>
                <tr>
                    <th><?php echo $lang->buildex->buildHotpatchBuilder;?></th>
                    <td><?php echo html::select('buildHotpatchBuilder', $users, $buildObj->buildHotpatchBuilder, 'class="form-control chosen"');?></td>
                </tr>
                <tr>
                    <th class='w-150px'><?php echo $lang->buildex->buildHotpatchAndroid;?></th>
                    <td><?php echo html::checkboxBool('buildHotpatchAndroid', $buildObj->buildHotpatchAndroid) ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->buildHotpatchiOS;?></th>
                    <td><?php echo html::checkboxBool('buildHotpatchiOS', $buildObj->buildHotpatchiOS) ?></td>
                </tr>
            </table>
        </fieldset>

        <fieldset>
            <legend><?php echo $lang->buildex->qaOption; ?></legend>
            <table class='table table-form'>
                <tr>
                    <th class='w-150px'><?php echo $lang->buildex->qaSmoke;?></th>
                    <td><?php echo html::checkboxBool('qaSmoke', $buildObj->qaSmoke) ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->qaSmokeFull;?></th>
                    <td><?php echo html::checkboxBool('qaSmokeFull', $buildObj->qaSmokeFull) ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->qaFunction;?></th>
                    <td><?php echo html::checkboxBool('qaFunction', $buildObj->qaFunction) ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->qaFull;?></th>
                    <td><?php echo html::checkboxBool('qaFull', $buildObj->qaFull) ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->buildex->qaRegTesting;?></th>
                    <td><?php echo html::checkboxBool('qaRegTesting', $buildObj->qaRegTesting) ?></td>
                </tr>
            </table>
        </fieldset>


        <div align="right"><?php echo html::submitButton() . html::backButton();?></div>
    </form>
</div>
<?php js::set('productGroups', $productGroups)?>
<?php include '../../common/view/footer.html.php';?>
