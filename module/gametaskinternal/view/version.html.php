<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<form method='post'>
    <table class='table table-borderless mw-600px table-form' align='center'>
        <tr>
            <th align="right"><?php echo $lang->gametaskinternal->version; ?></th>
            <td>

                <?php
                //echo $dept;
                echo html::input("version", "", "class='form-control'");
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
        <th class='w-100px'><?php echo $lang->gametaskinternal->version; ?></th>
        <th class='w-80px'><?php echo $lang->gametaskinternal->deadline; ?></th>
        <th class='w-100px'><?php echo $lang->gametaskinternal->status; ?></th>
    </tr>
    </thead>
    <tbody aria-live="polite" aria-relevant="all">

    <?php foreach ($versions as $v): ?>
        <tr class="text-center">
            <td><?php echo $v->id; ?></td>
            <td><?php echo $v->name; ?></td>
            <td>
                <div>
                <?php
                if($v->active)
                {
                    echo html::input('deadline', date('Y-m-d', strtotime($v->deadline)), "class='form-control form-date' placeholder=''");
                    echo html::commonButton($lang->gametaskinternal->update, "id='update_version_deadline' onclick=\"on_updateVersionDeadline('$v->id')\"");
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
                    echo $lang->gametaskinternal->active;
                    echo html::commonButton($lang->gametaskinternal->close, "id='close_version' onclick=\"on_closeVersion('$v->id')\"");
                }
                else
                {
                    echo $lang->gametaskinternal->close;
                    echo html::commonButton($lang->gametaskinternal->active, "id='active_version' onclick=\"on_activeVersion('$v->id')\"");
                }
                ?>

            </td>
        </tr>
    <?php endforeach ?>

    </tbody>
</table>

<?php include '../../common/view/footer.html.php'; ?>
