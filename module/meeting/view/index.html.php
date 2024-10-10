<?php
/**
 * The meeting view file of dashboard module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     dashboard
 * @version     $Id: meeting.html.php 4735 2013-05-03 08:30:02Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php js::set('confirmDelete', $lang->meeting->confirmDelete)?>
<div id='featurebar'>
  <ul class='nav'>
    <?php
    foreach($lang->meeting->periods as $period => $label)
    {
        $vars = "date=$period";
        if($period == 'before') $vars .= "&account={$app->user->account}&status=undone";
        echo "<li id='$period'>" . html::a(inlink('index', $vars), $label) . '</li>';
    }
    echo "<li id='byDate'>" . html::input('date', $date,"class='form-control form-date' onchange='changeDate(this.value)' autocomplete='off'") . '</li>';

    if(is_numeric($type))
    {
        if($date == date('Y-m-d'))
        {
            $type = 'today';
        }
        else if($date == date('Y-m-d', strtotime('-1 day')))
        {
            $type = 'yesterday';
        }
    }
    ?>
    <script>$('#<?php echo $type;?>').addClass('active')</script>
  </ul>
  <div class='actions'>
    <?php echo html::a(helper::createLink('meeting', 'batchCreate', "date=" . str_replace('-', '', $date)), "<i class='icon-plus-sign'></i> " . $lang->meeting->batchCreate, '', "class='btn'") ?>
  </div>
</div>
<form method='post' id='meetingform'>
  <table class='table table-condensed table-hover table-striped tablesorter table-fixed table-selectable' id='meetingList'>
    <?php $vars = "type=$type&account=$account&status=$status&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID"; ?>
    <thead>
      <tr class='text-center'>
          <th class='w-id'>    <?php common::printOrderLink('id',     $orderBy, $vars, $lang->idAB);?></th>
          <th class='w-pri'>   <?php common::printOrderLink('pri',    $orderBy, $vars, $lang->priAB);?></th>
          <th class='w-user'>  <?php common::printOrderLink('assignedTo',  $orderBy, $vars, $lang->meeting->assignedTo);?></th>
          <th class='w-date'>  <?php common::printOrderLink('createDate',   $orderBy, $vars, $lang->meeting->createDate);?></th>
          <th class='w-date'>  <?php common::printOrderLink('deadline',   $orderBy, $vars, $lang->meeting->deadline);?></th>
          <th>                 <?php common::printOrderLink('description',   $orderBy, $vars, $lang->meeting->description);?></th>
          <th class='w-status'><?php common::printOrderLink('status', $orderBy, $vars, $lang->meeting->status);?></th>
          <th class='w-100px {sorter:false}'><?php echo $lang->actions;?></th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($meetings as $meeting):?>
    <tr class='text-center'>
      <td class='cell-id'>
        <input type='checkbox' name='meetingIDList[<?php echo $meeting->id;?>]' value='<?php echo $meeting->id;?>' />
        <?php echo $meeting->id; ?>
      </td>
      <td><span class='<?php echo 'pri' . zget($lang->meeting->priList, $meeting->pri, $meeting->pri);?>'><?php echo zget($lang->meeting->priList, $meeting->pri, $meeting->pri)?></span></td>

        <td><?php echo $users[$meeting->assignedTo];?></td>

        <td><?php echo $meeting->createDate == '2030-01-01' ? $lang->meeting->periods['future'] : $meeting->createDate;?></td>
        <td><?php echo $meeting->deadline;?></td>

      <td class='text-left'><?php echo html::a($this->createLink('meeting', 'view', "id=$meeting->id&from=meeting", '', true), $meeting->description, '', "data-toggle='modal' data-type='iframe' data-title='" . $lang->meeting->view . "' data-icon='check'");?></td>

      <td class='<?php echo $meeting->status;?>'><?php echo $lang->meeting->statusList[$meeting->status];?></td>
      <td class='text-right'>
        <?php
        //if($meeting->status != 'done') common::printIcon('meeting', 'assignto', "meetingID=$meeting->id", $meeting, 'list', 'hand-right', '', "btn-icon", '', "data-toggle='assigntoModal'", $lang->meeting->assignTo);
        if($meeting->status == 'done') common::printIcon('meeting', 'activate', "id=$meeting->id", $meeting, 'list', 'magic', 'hiddenwin');
        if($meeting->status != 'done') common::printIcon('meeting', 'finish', "id=$meeting->id", $meeting, 'list', 'ok-sign', 'hiddenwin');
        //if($meeting->status == 'done') common::printIcon('meeting', 'close', "id=$meeting->id", $meeting, 'list', 'off', 'hiddenwin');
        common::printIcon('meeting', 'edit',   "id=$meeting->id", '', 'list', 'pencil', '', 'iframe', true);

        if(common::hasPriv('meeting', 'delete'))
        {
            $deleteURL = $this->createLink('meeting', 'delete', "meetingID=$meeting->id&confirm=yes");
            //echo html::a("javascript:ajaxDelete(\"$deleteURL\",\"meetingList\",confirmDelete)", '<i class="icon-remove"></i>', '', "class='btn-icon' title='{$lang->meeting->delete}'");
        }
        ?>
      </td>
    </tr>
    <?php endforeach;?>
    </tbody>
    <?php if(count($meetings)):?>
    <tfoot>
      <tr>
        <td colspan='8' align='left'>
          <?php if($type != 'cycle'):?>
          <div class='table-actions clearfix'>
          <?php
           echo html::selectButton();
          echo "<div class='btn-group'>";
          if(common::hasPriv('meeting', 'batchEdit'))
          {
              $actionLink = $this->createLink('meeting', 'batchEdit', "from=edit");
              echo html::commonButton($lang->edit, "onclick=\"setFormAction('$actionLink')\"");

          }
          if(common::hasPriv('meeting', 'batchFinish'))
          {
              $actionLink = $this->createLink('meeting', 'batchFinish');
              echo html::commonButton($lang->meeting->finish, "onclick=\"setFormAction('$actionLink','hiddenwin')\"");
          }
          echo '</div>';
          ?>
          </div>
          <?php endif;?>
          <?php $pager->show();?>
        </td>
      </tr>
    </tfoot>
    <?php endif;?>
  </table>
</form>
<?php include '../../meeting/view/assignto.html.php';?>
<?php js::set('listName', 'meetingList')?>
<?php include '../../common/view/footer.html.php';?>
