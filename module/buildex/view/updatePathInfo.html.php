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
<?php include '../../common/view/kindeditor.html.php';?>
<div class='container'>
    <div id='titlebar'>
        <div class='heading'>
            <span class='prefix'><?php echo html::icon($lang->icons['build']);?></span>
            <strong><small class='text-muted'><?php echo html::icon($lang->icons['create']);?></small> <?php echo $lang->buildex->updatePathInfo;?></strong>
        </div>
    </div>

    <form class='form-condensed' method='post' target='hiddenwin'>
        <table class='table table-form'>
            <tr>
                <th><?php echo $lang->buildex->$fieldName;?></th>
                <?php $placeHolder = $fieldName . 'PlaceHolder'?>
                <td colspan='2'><?php echo html::input($fieldName, $buildObj->$fieldName, "class='form-control' autocomplete='off' placeholder='{$lang->buildex->$placeHolder}'");?></td>
            </tr>
        </table>
        <div align="right"><?php echo html::submitButton() . html::linkButton($lang->goback, $this->server->http_referer);?></div>
    </form>
</div>
<?php js::set('productGroups', $productGroups)?>
<?php include '../../common/view/footer.html.php';?>
