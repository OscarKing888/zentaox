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
            <th align="right"><?php echo $lang->project->milestone; ?></th>
            <td>

                <?php
                //echo $dept;
                echo html::input("milestone", "", "class='form-control'");
                ?>
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
        <th class='w-120px'><?php echo $lang->project->deadline; ?></th>
        <th class='w-100px'><?php echo $lang->project->status; ?></th>
    </tr>
    </thead>
    <tbody aria-live="polite" aria-relevant="all">

    <?php foreach ($milestones as $v): ?>
        <tr class="text-center">
            <td><?php echo $v->id; ?></td>
            <td><?php echo $v->name; ?></td>
            <td>
                <div>
                <?php
                if($v->active)
                {
                    echo html::input("deadline$v->id", date('Y-m-d', strtotime($v->deadline)), "class='form-control form-date' placeholder=''");
                    echo html::commonButton($lang->project->update, "id='update_Milestone_deadline' onclick=\"on_updateMilestoneDeadline('$v->id')\"");
                }
                else
                {
                    echo date('Y-m-d', strtotime($v->deadline));
                }
                ?>
                </div>
            </td>
            <td>
                <?php
                if($v->active)
                {
                    echo $lang->project->active;
                    echo html::commonButton($lang->project->versionend, "id='close_Milestone' onclick=\"on_closeMilestone('$v->id')\"");
                }
                else
                {
                    echo $lang->project->close;
                    echo html::commonButton($lang->project->active, "id='active_Milestone' onclick=\"on_activeMilestone('$v->id')\"");
                }
                ?>

            </td>
        </tr>
    <?php endforeach ?>

    </tbody>
</table>

<?php include '../../common/view/footer.html.php'; ?>
