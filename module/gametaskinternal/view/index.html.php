<?php
/**
 * The html template file of index method of pipeline module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
?>
<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<?php include 'debug.html.php'; ?>


<div class='row-table'>
    <div class='col-main'>
        <div class='main'>

            <table class='table table-form table-fixed with-border' id="tableBody">
                <thead>
                <tr class='text-center'>
                    <th class='w-30px'><?php echo $lang->idAB;?></th>
                    <th class='w-100px'><?php echo $lang->gametaskinternal->version;?></th>
                    <th class='w-80px'><?php echo $lang->gametaskinternal->dept;?></th>
                    <th class='w-150px'><?php echo $lang->gametaskinternal->owner;?></th>
                    <th class='w-70px'><?php echo $lang->gametaskinternal->assignedTo;?></th>
                    <th class='w-70px'><?php echo $lang->gametaskinternal->workhour;?></th>
                    <th class='w-70px'><?php echo $lang->gametaskinternal->completestat;?></th>
                    <th class='w-70px'><?php echo $lang->gametaskinternal->closestat;?></th>
                    <th class='w-p20'><?php echo $lang->gametaskinternal->title;?></th>
                    <th class='w-50px'><?php echo $lang->gametaskinternal->count;?></th>
                    <th class='w-p20'><?php echo $lang->gametaskinternal->desc;?></th>
                    <th class='w-100px'><?php echo $lang->gametaskinternal->srcResPath;?></th>
                    <th class='w-100px'><?php echo $lang->gametaskinternal->gameResPath;?></th>
                    <th class='w-70px'><?php echo $lang->gametaskinternal->pri;?></th>

                </tr>
                </thead>

                <?php foreach($gameTasks as $task): ?>
                    <tr>
                        <td class='text-center'><?php echo $task->id;?></td>
                        <td class='text-center'><?php echo $versions[$task->version];?></td>
                        <td class='text-center'><?php echo $depts[$task->dept];?></td>
                        <td class='text-center'><?php echo $allOwners[$task->owner];?></td>
                        <td class='text-center'><?php echo $task->assignedTo ? $allUsers[$task->assignedTo] : $lang->gametaskinternal->assignedToNull;?></td>
                        <td class='text-center'><?php echo $task->workhour;?></td>
                        <td class='text-center'><?php echo $task->complete ? $lang->gametaskinternal->completed : $lang->gametaskinternal->incomplete;?></td>
                        <td class='text-center'><?php echo $task->closed ? $lang->gametaskinternal->closed : $lang->gametaskinternal->unclose;?></td>

                        <td class='text-left'><?php echo $task->title;?></td>
                        <td class='text-center'><?php echo $task->count;?></td>
                        <td class='text-left'><?php echo $task->desc;?></td>
                        <td class='text-left'><?php echo $task->srcResPath;?></td>
                        <td class='text-left'><?php echo $task->gameResPath;?></td>
                        <td class='text-center'><?php echo $task->pri;?></td>
                    </tr>
                <?php endforeach;?>
            </table>
            <div align="right" class='content'>
                <?php
                $pager->show('right', 'short');
                ?>
            </div>
        </div>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
