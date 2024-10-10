<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<div id='titlebar'>
    <div class='heading'>
        <span class='prefix'><?php echo html::icon($lang->icons['project']); ?>
            <strong><?php echo $project->id; ?></strong></span>
        <strong><?php echo $project->name; ?></strong>
        <?php if ($project->deleted): ?>
            <span class='label label-danger'><?php echo $lang->project->deleted; ?></span>
        <?php endif; ?>
    </div>
</div>

<form method='post'>
    <table class='table table-borderless mw-600px table-form' align='center'>
        <tr>
            <th align="right"><?php echo $lang->project->milestone; ?><span class='required'></span></th>
            <td>

                <?php
                //echo $dept;
                echo html::input("milestone", "", "class='form-control'");
                ?>
            </td>
        </tr>

        <tr>
            <th><?php echo $lang->project->deadlineRequirement; ?></th>
            <td>
                <?php echo html::input('deadlineRequirement', helper::nowafter(7), "class='form-control form-date' placeholder=''"); ?>
            </td>
        </tr>

        <tr>
            <th><?php echo $lang->project->deadline; ?></th>
            <td>
                <?php echo html::input('deadline', helper::nowafter(30), "class='form-control form-date' placeholder=''"); ?>
            </td>
        </tr>

        <tr>
            <th><?php echo $lang->project->deadlineQA; ?></th>
            <td>
                <?php echo html::input('deadlineQA', helper::nowafter(44), "class='form-control form-date' placeholder=''"); ?>
            </td>
        </tr>

        <tr>
            <th><?php echo $lang->project->opOnlineDate; ?></th>
            <td>
                <?php echo html::input('opOnlineDate', helper::nowafter(50), "class='form-control form-date' placeholder=''"); ?>
            </td>
        </tr>
        <tr>
            <th></th>
            <td>
                <?php echo html::submitButton('添加'); ?>
            </td>
        </tr>
        <tr>
            <th></th>
            <td>
                <font color="red"><?php echo $msg; ?></font>
            </td>
        </tr>
    </table>
</form>
<br>
<table class='table table-form table-fixed with-border mw-600px' align='center' id="buildList" role="grid">
    <thead>
    <tr class="colhead tablesorter-headerRow" role="row">
        <th class='w-30px'><?php echo $lang->idAB;?></th>
        <th class='w-100px'><?php echo $lang->project->milestone; ?></th>
        <th class='w-120px'><?php echo $lang->project->deadlineRequirement; ?></th>
        <th class='w-120px'><?php echo $lang->project->deadline; ?></th>
        <th class='w-120px'><?php echo $lang->project->deadlineQA; ?></th>
        <th class='w-120px'><?php echo $lang->project->opOnlineDate; ?></th>
        <th class='w-100px'><?php echo $lang->project->action; ?></th>
    </tr>
    </thead>
    <tbody aria-live="polite" aria-relevant="all">

    <?php foreach ($milestones as $v): ?>
        <tr class="text-center">
            <td><?php echo $v->id; ?></td>
            <td><?php echo $v->name; ?></td>
            <td><?php echo date('Y-m-d', strtotime($v->deadlineRequirement)); ?></td>
            <td><?php echo date('Y-m-d', strtotime($v->deadline)); ?>

                <div>
                <?php
                /*
                if($v->active)
                {
                    echo html::input("deadline$v->id", date('Y-m-d', strtotime($v->deadline)), "class='form-control form-date' placeholder=''");
                    echo html::commonButton($lang->project->update, "id='update_Milestone_deadline' onclick=\"on_updateMilestoneDeadline('$v->id')\"");
                }
                else
                {
                    echo date('Y-m-d', strtotime($v->deadline));
                }
                */
                ?>
                </div>
            </td>
            <td><?php echo date('Y-m-d', strtotime($v->deadlineQA)); ?></td>
            <td><?php echo date('Y-m-d', strtotime($v->opOnlineDate)); ?></td>
            <td>
                <?php
                $vars = "projectId={$project->id}&milestoneId={$v->id}";
                common::printIcon('project', 'editMilestone', $vars, $story, 'list', 'pencil', '_blank');
                ?>

            </td>
        </tr>
    <?php endforeach ?>

    </tbody>
</table>
<script>
    $('#modulemenu .nav li[data-id=<?php echo $browseType?>]').addClass('active');
</script>
<?php include '../../common/view/footer.html.php'; ?>
