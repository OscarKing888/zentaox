<?php
/**
 * The batch edit view of todo module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Congzhi Chen <congzhi@cnezsoft.com>
 * @package     todo
 * @version     $Id: create.html.php 2741 2012-04-07 07:24:21Z areyou123456 $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<div id='titlebar'>
    <div class='heading'>
        <span class='prefix'><?php echo html::icon($lang->icons['todo']); ?></span>
        <strong>
            <small class='text-muted'><?php echo html::icon($lang->icons['batchEdit']); ?></small> <?php echo $lang->meeting->common . $lang->colon . $lang->meeting->batchEdit; ?>
        </strong>
    </div>
</div>
<?php if (isset($suhosinInfo)): ?>
    <div class='alert alert-info'><?php echo $suhosinInfo; ?></div>
<?php else: ?>
    <?php
    $visibleFields = array();
    foreach (explode(',', $showFields) as $field) {
        if ($field) $visibleFields[$field] = '';
    }
    $columns = count($visibleFields) + 3;
    ?>
    <form class='form-condensed' method='post' target='hiddenwin'
          action='<?php echo $this->inlink('batchEdit', "from=updateBatchEdit"); ?>'>
        <table class='table table-form table-fixed with-border'>
            <thead>
            <tr>
                <th class='w-30px'><?php echo $lang->idAB; ?></th>
                <th class='w-80px'><?php echo $lang->meeting->pri; ?></th>
                <th><?php echo $lang->meeting->description; ?><span class='required'></span></th>
                <th><?php echo $lang->meeting->assignedTo; ?><span class='required'></span></th>
                <th><?php echo $lang->meeting->deadline; ?><span class='required'></span></th>
                <th class='w-100px'><?php echo $lang->meeting->status; ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($editedTodos as $todo): ?>
                <tr class='text-center'>
                    <td><?php echo $todo->id . html::hidden("meetingIDList[$todo->id]", $todo->id); ?></td>
                    <td><?php echo html::select("pris[$todo->id]", $lang->meeting->priList, $todo->pri, 'class=form-control'); ?></td>
                    <td><?php echo html::textarea("descs[$todo->id]", $todo->description, "rows='1' class='form-control'"); ?></td>
                    <td><?php echo html::select("assignedTos[$todo->id]", $users, $todo->assignedTo, 'class=form-control'); ?></td>
                    <td><?php echo html::input("deadlines[$todo->id]", $todo->deadline, "class='form-control form-date'"); ?></td>
                    <td><?php echo html::select("statuss[$todo->id]", $lang->meeting->statusList, $todo->status, "class='form-control'"); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan='<?php echo '6' ?>'><?php echo html::submitButton() . html::backButton(); ?></td>
            </tr>
            </tfoot>
        </table>
    </form>
<?php endif; ?>
<?php include './footer.html.php'; ?>
