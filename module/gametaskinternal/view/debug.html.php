<?php
/**
 * The html template file of index method of blog module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
?>

<?php if($this->config->gametask->debug):?>
===========
<?php echo $this->post->uid; ?>
<br>
<br>
<?php echo $this->moduleName; ?>
<br>
<?php echo $this->app->user->account; ?>
<br>

    <table class='hide' id='trTemp'>
        <tbody>
        <tr>
            <td class='text-center'>%s</td>
            <td><?php echo html::select("version[%s]", $versions, $version, 'class=form-control');?></td>
            <td><?php echo html::select("dept[%s]", $depts, $dept, 'class=form-control');?></td>
            <td><?php echo html::select("owner[%s]", $allOwners, $owner, 'class=form-control');?></td>
            <td><?php echo html::input("title[%s]", '', "class='form-control text-center' autocomplete='on'");?></td>
            <td><?php echo html::input("count[%s]", 1, "class='form-control text-center' autocomplete='on'");?></td>
            <td><?php echo html::textarea("desc[%s]", '', "rows='1' class='form-control autosize'");?></td>
            <td><?php echo html::textarea("srcResPath[%s]", '', "rows='1' class='form-control autosize'");?></td>
            <td><?php echo html::textarea("gameResPath[%s]", '', "rows='1' class='form-control autosize'");?></td>
            <td><?php echo html::select("pri[%s]", (array)$config->gametaskinternal->priList, $pri, 'class=form-control');?></td>
        </tr>
        </tbody>
    </table>


    <tr class="text-center">
        <td><?php echo $newID; ?></td>
        <td><?php echo html::select("dept[$newID]", $depts, 0, "class='form-control chosen'"); ?></td>
        <td><?php echo html::select("username[$newID]", $allUsers, 0, "class='form-control chosen'"); ?></td>
    </tr>

    <tr class='text-center'>
        <th class='w-id'><?php common::printOrderLink('id', $orderBy, $vars, $lang->idAB); ?></th>
        <th class='w-100px'><?php echo $lang->gametaskinternal->version; ?></th>
        <th class='w-80px'><?php echo $lang->gametaskinternal->dept; ?></th>
        <th class='w-150px'><?php echo $lang->gametaskinternal->owner; ?></th>
        <th class='w-70px'><?php echo $lang->gametaskinternal->assignedTo; ?></th>
        <th class='w-70px'><?php echo $lang->gametaskinternal->workhour; ?></th>
        <th class='w-70px'><?php echo $lang->gametaskinternal->completeStat; ?></th>
        <th class='w-70px'><?php echo $lang->gametaskinternal->closeStat; ?></th>
        <th class='w-p20'><?php echo $lang->gametaskinternal->title; ?></th>
        <th class='w-50px'><?php echo $lang->gametaskinternal->count; ?></th>
        <th class='w-p20'><?php echo $lang->gametaskinternal->desc; ?></th>
        <th class='w-100px'><?php echo $lang->gametaskinternal->srcResPath; ?></th>
        <th class='w-100px'><?php echo $lang->gametaskinternal->gameResPath; ?></th>
        <th class='w-70px'><?php echo $lang->gametaskinternal->pri; ?></th>

    </tr>



    <tr class='text-center' data-id='<?php echo $task->id; ?>'>
        <td class='cell-id'>
            <?php
            //echo "<input type='checkbox' name='taskIDList[{$task->id}]' value='{$task->id}'/>";
            echo $canView ? html::a($taskLink, sprintf('%03d', $task->id)) : sprintf('%03d', $task->id);
            ?>
        </td>

        <td ><?php echo $versions[$task->version]; ?></td>
        <td ><?php echo $depts[$task->dept]; ?></td>
        <td ><?php echo $allOwners[$task->owner]; ?></td>
        <td ><?php echo $task->assignedTo ? $allUsers[$task->assignedTo] : $lang->gametaskinternal->assignedToNull; ?></td>
        <td ><?php echo $task->workhour; ?></td>
        <td ><?php echo $task->completed ? $lang->gametaskinternal->completed : $lang->gametaskinternal->incomplete; ?></td>
        <td ><?php echo $task->closed ? $lang->gametaskinternal->closed : $lang->gametaskinternal->unclose; ?></td>

        <td class='text-left'><?php echo $task->title; ?></td>
        <td ><?php echo $task->count; ?></td>
        <td class='text-left'><?php echo $task->desc; ?></td>
        <td class='text-left'><?php echo $task->srcResPath; ?></td>
        <td class='text-left'><?php echo $task->gameResPath; ?></td>
        <td ><?php echo $task->pri; ?></td>
    </tr>
===========
<?php endif;?>
