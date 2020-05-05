<?php
/**
 * The view method view file of project module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     project
 * @version     $Id: view.html.php 4594 2013-03-13 06:16:02Z wyd621@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php'; ?>
<?php

js::set('moduleID', $moduleID);
js::set('productID', $productID);
js::set('projectID', $projectID);
js::set('browseType', $browseType);
//echo "status:$status browseType:$browseType moduleID:$moduleID";
?>

<div id="featurebar">

    <ul class="nav">
        <li id="dept">
            <?php
            echo html::select("dept", $depts, 0, 'class=form-control chosen');
            ?>
        </li>
        <li id="sep1">&nbsp;</li>
        <li id="milestonelst">
            <?php
            echo html::select("milestone", $milestones, 0, 'class=form-control chosen');
            ?>
        </li>
        <li id="sep1">&nbsp;</li>

        <li id="delay">
            <a id="delayLabel" href="javascript:void(0)" onclick="onShowDelayOnly()">已延期</a></input>
        </li>

        <li id="zoom_day">
            <a href="javascript:void(0)" onclick="onZoomDay()">日单位</a>
        </li>
        <li id="zoom_week">
            <a href="javascript:void(0)" onclick="onZoomWeek()">周单位</a>
        </li>
        <li id="zoom_month">
            <a href="javascript:void(0)" onclick="onZoomMonth()">月单位</a>
        </li>
        <li id="zoom_origi">
            <a href="javascript:void(0)" onclick="onOrigi()">重置</a>
        </li>

    </ul>
</div>

<div class='canvas-wrapper'>
    <div class='chart-canvas'>
    </div>
</div>

<canvas id="projectCanvas"></canvas>

<script>
    $('#modulemenu .nav li[data-id=<?php echo $browseType?>]').addClass('active');
</script>

<?php include '../../common/view/footer.html.php'; ?>
