<?php
/**
 * The create view of todo module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     todo
 * @version     $Id: edit.html.php 4728 2013-05-03 06:14:34Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div class='container mw-700px'>
  <div id='titlebar'>
    <div class='heading'>
      <span class='prefix'><?php echo html::icon($lang->icons['todo']);?> <strong><?php echo $todo->id;?></strong></span>
      <strong><?php echo html::a($this->createLink('meeting', 'view', 'todo=' . $todo->id), $todo->description);?></strong>
      <small class='text-muted'> <?php echo $lang->meeting->edit;?></small>
    </div>
  </div>

  <form class='form-condensed' method='post' target='hiddenwin' id='dataform'>
    <table class='table table-form'> 
      <tr>
        <th class='w-80px'><?php echo $lang->meeting->deadline;?></th>
        <td class='w-p25-f'>
          <div class='input-group'>
            <?php echo html::input('deadline', $todo->deadline, "class='form-control form-date'");?>
          </div>
        </td><td></td>
      </tr>
      <tr>
        <th><?php echo $lang->meeting->pri;?></th>
        <td><?php echo html::select('pri', $lang->meeting->priList, $todo->pri, "class='form-control chosen'");?></td>
      </tr>
      <tr>
            <th><?php echo $lang->meeting->assignedTo;?></th>
            <td><?php echo html::select('assignedTo', $users, $todo->assignedTo, "class='form-control chosen'");?></td>
      </tr>

        <tr>
        <th><?php echo $lang->meeting->description;?></th>
        <td colspan='2'><?php echo html::textarea('description', htmlspecialchars($todo->description), "rows=8 class=area-1");?></td>
      </tr>
      <tr>
        <th><?php echo $lang->meeting->status;?></th>
        <td><?php echo html::select('status', $lang->meeting->statusList, $todo->status, "class='form-control'");?></td>
      </tr>  

      <tr>
        <td></td>
        <td>
          <?php echo html::submitButton() . html::backButton();?>
        </td>
      </tr>
    </table>
  </form>
</div>
<?php include './footer.html.php';?>
