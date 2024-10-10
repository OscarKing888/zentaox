<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<form method='post'>
    <table class='table table-borderless table-form mw-500px' align='center'>
        <thead>
        <tr class="colhead tablesorter-headerRow" role="row">
            <th class='w-30px'><?php echo $lang->idAB; ?></th>
            <th class='w-100px'><?php echo $lang->dept->name; ?></th>
            <th class='w-100px'><?php echo $lang->user->account; ?></th>
        </tr>
        </thead>
        <tbody aria-live="polite" aria-relevant="all">

        <?php $i = 0; foreach($depts as $k => $v): ?>
            <tr class="text-center">
                <td><?php echo $k; ?></td>
                <td><?php echo $v; echo html::select("dept[$i]", $depts, $k, "class='form-control hidden'");?></td>
                <td><?php $account = $leaders[$k]; $user = $account; echo html::select("username[$i]", $allUsers, $user, "class='form-control chosen'"); ?></td>
            </tr>
        <?php endforeach ?>
        </tbody>

        <tfoot>
        <tr>
            <td colspan="3" align="center">
                <?php echo html::submitButton('更新'); ?>
            </td>
        </tr>
        </tfoot>
    </table>
</form>

<?php include '../../common/view/footer.html.php'; ?>
