<?php
/**
 * The batch create view of meetings module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Congzhi Chen <congzhi@cnezsoft.com>
 * @package     meetings
 * @version     $Id: create.html.php 2741 2012-04-07 07:24:21Z areyou123456 $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<form class='form-condensed' method='post' target='hiddenwin' style='overflow:visible'>
    <div id='titlebar'>
        <div class='heading'>
            <span class='prefix pull-left'><?php echo html::icon($lang->icons['meetings']); ?></span>
            <strong class='pull-left'>
                <small class='text-muted'><?php echo html::icon($lang->icons['batchCreate']); ?></small> <?php echo $lang->meetings->batchCreate; ?>
            </strong>
        </div>
    </div>

    <?php
    $visibleFields = array();
    foreach (explode(',', $showFields) as $field) {
        if ($field) $visibleFields[$field] = '';
    }
    $columns = count($visibleFields) + 2;
    ?>

    <table class='table table-form table-fixed with-border'>
        <thead>
        <tr>
            <th class='w-30px'><?php echo $lang->idAB; ?></th>
            <th class='w-80px'><?php echo $lang->meeting->pri; ?></th>
            <th><?php echo $lang->meeting->description; ?><span class='required'></span></th>
            <th><?php echo $lang->meeting->assignedTo; ?><span class='required'></span></th>
            <th><?php echo $lang->meeting->deadline; ?><span class='required'></span></th>
        </tr>
        </thead>

        <?php $pri = 3; ?>


        <?php for ($i = 0; $i < $config->meeting->batchCreate; $i++): ?>
            <tr class='text-center'>
                <td><?php echo $i + 1; ?></td>
                <td><?php echo html::select("pris[$i]", $lang->meeting->priList, $pri, 'class=form-control'); ?></td>
                <td><?php echo html::textarea("descs[$i]", '', "rows='1' class='form-control'"); ?></td>
                <td><?php echo html::select("assignedTos[$i]", $users, 0, 'class=form-control'); ?></td>
                <td><?php echo html::input("deadlines[$i]", helper::nowafter(5), "class='form-control form-date'"); ?></td>
            </tr>
        <?php endfor; ?>

        <tfoot>
        <tr>
            <td colspan='5'><?php echo html::submitButton() . html::backButton(); ?></td>
        </tr>
        </tfoot>
    </table>
</form>
<?php $customLink = $this->createLink('custom', 'ajaxSaveCustomFields', 'module=meetings&section=custom&key=batchCreateFields') ?>
<?php include '../../common/view/customfield.html.php'; ?>
<?php include './footer.html.php'; ?>
<script language='Javascript'>
    var batchCreateNum = '<?php echo $config->meeting->batchCreate;?>';
    setBeginsAndEnds();
</script>
