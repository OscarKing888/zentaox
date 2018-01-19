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
            <small class='text-muted'><?php echo html::icon($lang->icons['batchCreate']); ?></small> <?php echo $lang->gametaskinternal->add; ?>
        </strong>

        <div class='actions'>
            <?php echo html::commonButton($lang->pasteText, "data-toggle='myModal'", 'btn btn-primary') ?>
        </div>
    </div>
</div>

<form method='post'>
    <table class='table table-form with-border' align='left'>
        <tr>
            <th></th>
            <td>
                <font color="red"><?php if(!empty($msg)){echo js::alert($msg);} ?></font>
            </td>
        </tr>
        <tr>
            <th class='w-100px '>
                <strong><?php echo $lang->product->name; ?></strong><span class='required'></span>
            </th>
            <td >
                <?php echo html::select("product", $allProducts, $config->gametaskinternal->defaultProject, "class='form-control chosen' onchange=''"); ?>
            </td>
        </tr>
    </table>
    <br>
    <table class='table table-form table-fixed with-border' id="tableBody">
        <thead>
        <tr class='text-center'>
            <th class='w-30px'><?php echo $lang->idAB; ?></th>
            <th class='w-100px'><?php echo $lang->gametaskinternal->version; ?><span class='required'></span></th>
            <th class='w-80px'><?php echo $lang->gametaskinternal->dept; ?><span class='required'></span></th>
            <th class='w-150px'><?php echo $lang->gametaskinternal->owner; ?><span class='required'></span></th>
            <th class='w-p20'><?php echo $lang->gametaskinternal->title; ?><span class='required'></span></th>
            <th class='w-50px'><?php echo $lang->gametaskinternal->count; ?></th>
            <th class='w-p20'><?php echo $lang->gametaskinternal->desc; ?></th>
            <th class='w-100px'><?php echo $lang->gametaskinternal->srcResPath; ?></th>
            <th class='w-100px'><?php echo $lang->gametaskinternal->gameResPath; ?></th>
            <th class='w-70px'><?php echo $lang->gametaskinternal->pri; ?></th>
        </tr>
        </thead>
        <?php
        $depts['ditto'] = $lang->task->ditto;
        $versions['ditto'] = $lang->task->ditto;
        $allOwners['ditto'] = $lang->task->ditto;

        //echo $owner;

        /*
        foreach(array_keys($$allOwners) as $u)
        {
            echo $u . " -> " . $$allOwners[$u] . "<br>";
        }
        //*/
        ?>

        <?php for ($i = 0; $i < $config->gametaskinternal->batchCreate; $i++): ?>
            <?php
            if ($i == 0) {
                $version = "";
                $dept = 1;
                $owner = $user;
            } else {
                $version = $dept = $owner = 'ditto';
            }
            ?>
            <?php $pri = 3; ?>
            <tr>
                <td class='text-center'><?php echo $i + 1; ?></td>
                <td><?php echo html::select("version[$i]", $versions, $version, 'class=form-control chosen'); ?></td>
                <td><?php echo html::select("dept[$i]", $depts, $dept, 'class=form-control chosen'); ?></td>
                <td><?php echo html::select("owner[$i]", $allOwners, $owner, "class='form-control'"); ?></td>

                <td><?php echo html::input("title[$i]", '', "class='form-control text-center' autocomplete='on'"); ?></td>
                <td><?php echo html::input("count[$i]", 1, "class='form-control text-center' autocomplete='on'"); ?></td>
                <td><?php echo html::textarea("desc[$i]", '', "rows='1' class='form-control autosize'"); ?></td>
                <td><?php echo html::textarea("srcResPath[$i]", '', "rows='1' class='form-control autosize'"); ?></td>
                <td><?php echo html::textarea("gameResPath[$i]", '', "rows='1' class='form-control autosize'"); ?></td>
                <td><?php echo html::select("pri[$i]", (array)$lang->gametaskinternal->priList, $pri, 'class=form-control chosen'); ?></td>
            </tr>
        <?php endfor; ?>
        <tr>
            <td colspan='10' class='text-center'><?php echo html::submitButton() . html::backButton(); ?></td>
        </tr>
    </table>
</form>

<?php include '../../common/view/pastetext.html.php'; ?>
<?php include '../../common/view/footer.html.php'; ?>
