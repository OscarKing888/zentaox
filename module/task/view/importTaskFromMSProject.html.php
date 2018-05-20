<?php
/**
 * The batch create view of task module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yangyang Shi <shiyangyang@cnezsoft.com>
 * @package     task
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<div id='titlebar'>
    <div class='heading'>
        <span class='prefix'><?php echo html::icon($lang->icons['task']); ?></span>
        <strong>
            <small class='text-muted'><?php echo html::icon($lang->icons['batchCreate']); ?></small> <?php echo $lang->task->importTaskFromMSProject; ?>
        </strong>
        <div class='actions'>
            <?php echo html::commonButton($lang->pasteText, "data-toggle='myModal'", 'btn btn-primary') ?>
        </div>
    </div>
</div>
<?php
$visibleFields = array();
foreach (explode(',', $showFields) as $field) {
    if ($field) $visibleFields[$field] = '';
}
$colspan = count($visibleFields);
$hiddenStory = ((isonlybody() and $storyID) || $this->config->global->flow == 'onlyTask') ? ' hidden' : '';
if ($hiddenStory and isset($visibleFields['story'])) $colspan -= 1;
?>
<form class='form-condensed' method='post' target='hiddenwin'>
    <table class='table table-form table-fixed with-border' id="tableBody">
        <thead>
        <tr class='text-center'>
            <th class='w-60px'><?php echo $lang->idAB; ?><span class='required'></span></th>
            <th class='w-150px'><?php echo $lang->task->project ?><span class='required'></span></th>
            <th class='w-150px'><?php echo $lang->task->module ?><span class='required'></span></th>
            <th class='w-200px'><?php echo $lang->task->story; ?><span class='required'></span></th>
            <th><?php echo $lang->task->dept; ?> <span class='required'></span></th>
            <th><?php echo $lang->task->name; ?> <span class='required'></span></th>
            <th class='w-70px'><?php echo $lang->task->pri; ?><span class='required'></span></th>
            <th class='w-50px'><?php echo $lang->task->estimateAB; ?><span class='required'></span></th>
            <th class='w-100px'><?php echo $lang->task->estStarted; ?><span class='required'></span></th>
            <th class='w-100px'><?php echo $lang->task->deadline; ?><span class='required'></span></th>
            <th class='w-80px'><?php echo $lang->task->assignedTo; ?> <span class='required'></span></th>
        </tr>
        </thead>

        <?php

        function array_remove($data, $key)
        {
            if (!array_key_exists($key, $data)) {
                return $data;
            }
            $keys = array_keys($data);
            $index = array_search($key, $keys);
            if ($index !== FALSE) {
                array_splice($data, $index, 1);
            }
            return $data;
        }

        $stories['ditto'] = $lang->task->ditto;
        $lang->task->typeList['ditto'] = $lang->task->ditto;
        //$members['ditto'] = $lang->task->ditto;
        $modules['ditto'] = $lang->task->ditto;

        $deptUsers['ditto'] = $lang->task->ditto; //oscar:
        $projects['ditto'] = $lang->task->ditto; //oscar:
        //$leaders = array_remove('admin', $leaders);

        if ($project->type == 'ops') $colspan = $colspan - 1;
        ?>
        <?php for ($i = 0; $i < 0; $i++): ?>
            <?php
            if ($i == 0) {
                $currentStory = $storyID;
                $type = '';
                $dept = $project = $assignedTo = 0;
                $member = '';
                $module = $story ? $story->module : '';
            } else {
                $dept = $project = $assignedTo = $currentStory = $type = $member = $module = 'ditto';
            }
            ?>
            <?php $pri = $parentTask->pri; ?>
            <tr>
                <td class='text-center'><?php echo html::input("id[$i]", '', "class='form-control text-center' autocomplete='off'"); ?></td>
                <td><?php echo html::select("project[$i]", $projects, $project, "class='form-control'") ?></td>
                <td><?php echo html::select("module[$i]", $modules, $module, "class='form-control'") ?></td>
                <td><?php echo html::select("story[$i]", $stories, $currentStory, "class='form-control'"); ?></td>
                <td><?php echo html::select("dept[$i]", $depts, $dept, 'class=form-control'); ?></td>
                <td><?php echo html::input("name[$i]", '', "class='form-control' autocomplete='off'"); ?></td>
                <td><?php echo html::select("pri[$i]", (array)$lang->task->priList, $pri, 'class=form-control'); ?></td>
                <td><?php echo html::input("estimate[$i]", '24', "class='form-control text-center' autocomplete='off'"); ?></td>
                <td><?php echo html::input("estStarted[$i]", '', "class='form-control text-center form-date'"); ?></td>
                <td><?php echo html::input("deadline[$i]", helper::nowafter(5), "class='form-control text-center form-date'"); ?></td>
                <td><?php $userList = $deptUsers; echo html::select("assignedTo[$i]", $userList, $assignedTo, 'class=form-control'); ?></td>
            </tr>
        <?php endfor; ?>
        <tr>
            <td colspan='<?php echo $colspan ?>'
                class='text-center'><?php echo html::submitButton() . html::backButton(); ?></td>
        </tr>
    </table>
</form>

<table class='hide' id='trTemp'>
    <tbody>
    <tr>
        <td class='text-center'><?php echo html::input("id[%s]", '', "class='form-control text-center' autocomplete='off'"); ?></td>
        <td><?php echo html::select("project[%s]", $projects, $project, "class='form-control'") ?></td>
        <td><?php echo html::select("module[%s]", $modules, $module, "class='form-control'") ?></td>
        <td><?php echo html::select("story[%s]", $stories, $currentStory, "class='form-control'"); ?></td>
        <td><?php echo html::select("dept[%s]", $depts, $dept, 'class=form-control'); ?></td>
        <td><?php echo html::input("name[%s]", '', "class='form-control' autocomplete='off'"); ?></td>
        <td><?php echo html::select("pri[%s]", (array)$lang->task->priList, $pri, 'class=form-control'); ?></td>
        <td><?php echo html::input("estimate[%s]", '', "class='form-control text-center' autocomplete='off'"); ?></td>
        <td><?php echo html::input("estStarted[%s]", '', "class='form-control text-center form-date'"); ?></td>
        <td><?php echo html::input("deadline[%s]", '', "class='form-control text-center form-date'"); ?></td>
        <td><?php $userList = $deptUsers;echo html::select("assignedTo[%s]", $userList, $assignedTo, 'class=form-control'); ?></td>
    </tr>
    </tbody>
</table>

<?php js::set('projectType', $project->type); ?>
<?php js::set('storyTasks', $storyTasks); ?>
<?php js::set('mainField', 'name'); ?>
<?php js::set('ditto', $lang->task->ditto); ?>
<?php js::set('storyID', $storyID); ?>

<?php include '../../common/view/pastemsproject.html.php'; ?>
<?php include '../../common/view/footer.html.php'; ?>
