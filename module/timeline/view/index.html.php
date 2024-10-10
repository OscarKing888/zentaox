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

            <button onclick="onZoomDay()">D</button>
            <button onclick="onZoomMonth()">M</button>
            <button onclick="onZoomYear()">Y</button>
            <button onclick="onZoom10Years()">10Y</button>
            <button onclick="onZoom100Years()">100Y</button>
            <button onclick="onZoom1000Years()">1KY</button>

            <button onclick="onOrigi()">O</button>
            <button onclick="<?php echo html::a(inlink('create')); ?>">+</button>

            <?php
            $lnk = html::a(inlink('create'),
                "<i class='icon icon-plus'></i>" . $lang->timeline->add,
                "", "class='btn'");
            echo $lnk;
            ?>
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
    </div>
</div>

<canvas id="projectCanvas"></canvas>

<?php include '../../common/view/footer.html.php'; ?>
