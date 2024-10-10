<?php
/**
 * The view file of view method of todo module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     todo
 * @version     $Id: view.html.php 4955 2013-07-02 01:47:21Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php if(!$todo->private or ($todo->private and $todo->account == $app->user->account)):?>
<div class='container mw-700px'>
  <div id='titlebar'>
    <div class='heading'>
      <span class='prefix' title='TODO'><?php echo html::icon($lang->icons['todo']);?> <strong><?php echo $todo->id;?></strong></span>
      <strong><?php echo $todo->name;?></strong>
    </div>
  </div>
  <div class='row-table'>
    <div class='col-main'>
      <div class='main'>
        <fieldset>
          <legend>
            <?php 
            echo $lang->meeting->description;
            ?>
          </legend>
          <div><?php echo $todo->description;?></div>
        </fieldset>
        <?php $actionTheme = 'fieldset'; include '../../common/view/action.html.php';?>
      </div>
    </div>
    <div class='col-side'>
      <div class='main main-side'>
        <fieldset>
        <legend><?php echo $lang->meeting->legendBasic;?></legend>
          <table class='table table-data table-condensed table-borderless'> 
            <tr>
              <th><?php echo $lang->meeting->pri;?></th>
              <td><?php echo $lang->meeting->priList[$todo->pri];?></td>
            </tr>
            <tr>
              <th><?php echo $lang->meeting->status;?></th>
              <td class='todo-<?php echo $todo->status?>'><?php echo $lang->meeting->statusList[$todo->status];?></td>
            </tr>
            <tr>
              <th class='w-80px'><?php echo $lang->meeting->assignedTo;?></th>
              <td><?php echo $users[$todo->assignedTo];?></td>
            </tr>
            <tr>
              <th class='w-80px'><?php echo $lang->meeting->createDate;?></th>
              <td><?php echo date(DT_DATE1, strtotime($todo->createDate));?></td>
            </tr>
              <tr>
                  <th class='w-80px'><?php echo $lang->meeting->deadline;?></th>
                  <td><?php echo date(DT_DATE1, strtotime($todo->deadline));?></td>
              </tr>
          </table>
      </div>
    </div>
  </div>
  <div class='panel-footer text-center'>
    <?php
    if($this->session->todoList)
    {
        $browseLink = $this->session->todoList;
    }
    elseif($todo->account == $app->user->account)
    {
        $browseLink = $this->createLink('my', 'todo');
    }
    else
    {
        $browseLink = $this->createLink('user', 'todo', "account=$todo->account");
    }

    common::printIcon('meeting', 'finish', "id=$todo->id", $todo, 'button', '', 'hiddenwin', 'showinonlybody btn-success');
    if($todo->account == $app->user->account)
    {
        common::printIcon('meeting', 'edit',   "todoID=$todo->id");
        common::printIcon('meeting', 'delete', "todoID=$todo->id", '', 'button', '', 'hiddenwin');
    }
    common::printRPN($browseLink);
    ?>
  </div>
</div>
<?php else:?>
<?php echo $lang->meeting->thisIsPrivate;?>
<?php endif;?>
<?php include '../../common/view/footer.html.php';?>
