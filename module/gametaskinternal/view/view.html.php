<?php include '../../common/view/header.html.php'; ?>
<?php
include '../../common/view/chart.html.php';
include '../../common/view/datepicker.html.php';
include '../../common/view/datatable.fix.html.php';
?>
<div class='row-table'>
    <div class='col-main'>
        <div class='main'>
            <fieldset>
                <legend><?php echo $lang->gametaskinternal->version;?></legend>
                <div class='article-content'><?php echo $versions[$gameTask->version];?></div>
            </fieldset>

            <fieldset>
                <legend><?php echo $lang->gametaskinternal->title;?></legend>
                <div class='article-content'><?php echo $gameTask->title;?></div>
            </fieldset>

            <fieldset>
                <legend><?php echo $lang->gametaskinternal->desc;?></legend>
                <div class='article-content'><?php echo $gameTask->desc;?></div>
            </fieldset>

            <fieldset>
                <legend><?php echo $lang->gametaskinternal->srcResPath;?></legend>
                <div class='article-content'><?php echo $gameTask->srcResPath;?></div>
            </fieldset>

            <fieldset>
                <legend><?php echo $lang->gametaskinternal->gameResPath;?></legend>
                <div class='article-content'><?php echo $gameTask->gameResPath;?></div>
            </fieldset>

            <fieldset>
                <legend><?php echo $lang->gametaskinternal->gameResPath;?></legend>
                <div class='article-content'><?php echo $gameTask->gameResPath;?></div>
            </fieldset>

            <td colspan='10' class='text-center'><?php echo html::backButton(); ?></td>
        </div>
    </div>
</div>

<?php include '../../common/view/footer.html.php'; ?>
<?php
//echo js::alert("datatableId:" . $this->view->datatableId . "module:$module method:$method mode:$mode");
?>
