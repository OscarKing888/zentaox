<?php
/**
 * The create view of testtask module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     testtask
 * @version     $Id: create.html.php 4728 2013-05-03 06:14:34Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php js::import($jsRoot . 'misc/date.js');?>
<div class='container mw-1400px'>
  <div id='titlebar'>
    <div class='heading'>
      <span class='prefix'><?php echo html::icon($lang->icons['testtask']);?></span>
      <strong><small class='text-muted'><?php echo html::icon($lang->icons['pencil']);?></small> <?php echo $lang->testtask->editTestTask;?></strong>
    </div>
  </div>
    <form class='form-condensed' method='post' id='dataform' data-type='iframe'>
    <table class='table table-form'> 

      <tr>
        <th><?php echo $lang->testtask->name;?></th>
        <td colspan='2'><?php echo html::input('name', $testtask->name, "class='form-control' autocomplete='off'");?></td>
      </tr>  

        <tr>
            <th>测试说明</th>
            <td>
                <?php echo html::textarea('testComments', $testtask->testComments, "rows='10' class='form-control'");?>
            </td>
        </tr>
        <tr>
            <th>需求列表</th>
            <td>
            <table>

                    <?php foreach($stories as $k => $v): ?>
                <tr>
                    <td>
                        <input type='checkbox' checked="checked" name='storyIDList[<?php echo $k ?>]' value='<?php echo $k; ?>'/>
                    </td>
                    <td>
                        <?php echo "#$v->id $v->title"; ?>
                    </td>
                </tr>
                    <?php endforeach; ?>

            </table>
            </td>
        </tr>
      <tr>
        <td></td><td colspan='2'><?php echo html::submitButton() . html::backButton();?> </td>
      </tr>
    </table>
  </form>
</div>
<?php include '../../common/view/footer.html.php';?>
