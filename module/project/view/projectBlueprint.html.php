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
        <?php
        $params = "project=$project->id";
        $browseLink = $this->session->projectList ? $this->session->projectList : inlink('browse', "projectID=$project->id");
        if (!$project->deleted) {
            ob_start();
            echo "<div class='btn-group'>";
            common::printIcon('project', 'start', "projectID=$project->id", $project, 'button', '', '', 'iframe', true);
            common::printIcon('project', 'activate', "projectID=$project->id", $project, 'button', '', '', 'iframe', true);
            common::printIcon('project', 'putoff', "projectID=$project->id", $project, 'button', '', '', 'iframe', true);
            common::printIcon('project', 'suspend', "projectID=$project->id", $project, 'button', '', '', 'iframe', true);
            common::printIcon('project', 'close', "projectID=$project->id", $project, 'button', '', '', 'iframe', true);
            echo '</div>';

            echo "<div class='btn-group'>";
            common::printIcon('project', 'edit', $params, $project);
            common::printIcon('project', 'delete', $params, $project, 'button', '', 'hiddenwin');
            echo '</div>';
            common::printRPN($browseLink);

            $actionLinks = ob_get_contents();
            ob_end_clean();
            echo $actionLinks;
        } else {
            common::printRPN($browseLink);
        }
        ?>
    </div>
</div>

<div class='canvas-wrapper'>
    <div class='chart-canvas'>
        <canvas id="projectCanvas" width="1024" height="600"></canvas>
    </div>
</div>

<?php include '../../common/view/footer.html.php'; ?>
