<?php
/**
 * The browse view file of testtask module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     testtask
 * @version     $Id: browse.html.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>

<?php
$scope = $this->session->testTaskVersionScope;
$status = $this->session->testTaskVersionStatus;
?>
<?php js::set('status', $status); ?>

<div id="featurebar">
    <ul class="nav">
        <li>
            <?php $viewName = $scope == 'local' ? $productName : $lang->testtask->all; ?>
        </li>
        <li id='waitTab'><?php echo html::a(inlink('browse', "productID=$productID&type=$scope,wait"), $lang->testtask->wait); ?></li>
        <li id='doneTab'><?php echo html::a(inlink('browse', "productID=$productID&type=$scope,done"), $lang->testtask->done); ?></li>
    </ul>

</div>

<table class='table tablesorter' id='taskList'>
    <thead>
    <?php $vars = "productID=$productID&type=$scope,$status&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"; ?>
    <tr>
        <th class='w-id text-left'>   <?php common::printOrderLink('id', $orderBy, $vars, $lang->idAB); ?></th>
        <th class='w-400px text-left'><?php common::printOrderLink('name', $orderBy, $vars, $lang->testtask->name); ?></th>
        <th class='w-100px  text-left'>        <?php common::printOrderLink('product', $orderBy, $vars, $lang->testtask->product); ?></th>
        <th class='w-100px  text-left'>        <?php common::printOrderLink('createBy', $orderBy, $vars, $lang->testtask->createBy); ?></th>
        <th class='w-80px text-left'> <?php common::printOrderLink('status', $orderBy, $vars, $lang->statusAB); ?></th>
        <th class='w-400px text-center'><?php echo $lang->actions; ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($tasks as $task): ?>
        <tr class='text-left'>
            <td><?php echo html::a(inlink('viewTestTask', "productID=$productID&taskID=$task->id"), sprintf('%03d', $task->id)); ?></td>
            <td class='text-left'
                title="<?php echo $task->name ?>"><?php echo html::a(inlink('viewTestTask', "productID=$productID&taskID=$task->id"), $task->name); ?></td>
            <td title="<?php echo $task->productName ?>"><?php echo $products[$task->product] ?></td>
            <td><?php echo $users[$task->createBy] ?></td>

            <td class='status-<?php echo $task->status ?>'><?php echo $lang->testtask->statusList[$task->status]; ?></td>
            <td class='text-center'>
                <?php
                common::printIcon('testtask', 'editTestTask', "productID=$productID&testtaskID=" . $task->id, '', 'list', 'pencil');

                if (common::hasPriv('testtask', 'startTestTask', $task) && $task->status == 'wait') {
                    common::printIcon('testtask', 'startTestTask', "productID=$productID&testtaskID=" . $task->id, '', 'list', 'play');
                }

                if (common::hasPriv('testtask', 'finishTestTask', $task) && $task->status == 'doing') {
                    common::printIcon('testtask', 'finishTestTask', "productID=$productID&testtaskID=" . $task->id, '', 'list', 'ok-sign');
                }

                if (common::hasPriv('testtask', 'removeTestTask', $task)) {
                    common::printIcon('testtask', 'removeTestTask', "productID=$productID&testtaskID=" . $task->id, '', 'list', 'trash');
                }
                /*
                common::printIcon('testtask', 'cases',    "taskID=$task->id", $task, 'list', 'sitemap');
                common::printIcon('testtask', 'view',     "taskID=$task->id", '', 'list', 'file','','iframe',true);
                common::printIcon('testtask', 'linkCase', "taskID=$task->id", $task, 'list', 'link');
                common::printIcon('testtask', 'edit',     "taskID=$task->id", $task, 'list','','','iframe',true);
                common::printIcon('testreport', 'browse', "objectID=$task->product&objectType=product&extra=$task->id", $task, 'list','flag');

                if(common::hasPriv('testtask', 'delete', $task))
                {
                    $deleteURL = $this->createLink('testtask', 'delete', "taskID=$task->id&confirm=yes");
                    echo html::a("javascript:ajaxDelete(\"$deleteURL\",\"taskList\",confirmDelete)", '<i class="icon-remove"></i>', '', "title='{$lang->testtask->delete}' class='btn-icon'");
                }
                //*/
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan='5'><?php $pager->show(); ?></td>
    </tr>
    </tfoot>
</table>

<?php include '../../common/view/footer.html.php'; ?>
