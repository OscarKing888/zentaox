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
<div id='titlebar'>
    <div class='heading'>
        <span class='prefix'><?php echo html::icon($lang->icons['project']); ?>
            <strong><?php echo $project->id; ?></strong></span>
        <strong><?php echo $project->name; ?></strong>
        <?php if ($project->deleted): ?>
            <span class='label label-danger'><?php echo $lang->project->deleted; ?></span>
        <?php endif; ?>
    </div>
    <div class='actions'>
        <div class='btn-group'>
            
            <button type="select" onclick="onShowDelayOnly()">延</button>
            <button onclick="onZoomDay()">日</button>
            <button onclick="onZoomWeek()">周</button>
            <button onclick="onZoomMonth()">月</button>
            <button onclick="onZoomSeason()">季</button>

            <button onclick="onZoomOut()">-</button>
            <button onclick="onZoomIn()">+</button>
        </div>
        <?php
        echo "<div class='btn-group'>";
        //common::printIcon('project', 'close', "projectID=$project->id", $project, 'button', '', '', 'iframe', true);
        echo '</div>';

        ?>
    </div>
</div>

<div class='canvas-wrapper'>
    <div class='chart-canvas'>
        <canvas id="projectCanvas" width="1024" height="8192"></canvas>
    </div>
</div>

<?php include '../../common/view/footer.html.php'; ?>
