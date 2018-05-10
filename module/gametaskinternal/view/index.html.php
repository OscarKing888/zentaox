<?php
/**
 * The html template file of index method of index module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     ZenTaoPMS
 * @version     $Id: index.html.php 5094 2013-07-10 08:46:15Z chencongzhi520@gmail.com $
 */
?>
<?php include '../../common/view/header.html.php'; ?>


<?php

/*
common::printIcon('gametaskinternal', 'music',   "", $task, 'list');
common::printIcon('gametaskinternal', 'start',   "", $task, 'list');
common::printIcon('gametaskinternal', 'star-empty',   "", $task, 'list');
common::printIcon('gametaskinternal', 'user',   "", $task, 'list');
common::printIcon('gametaskinternal', 'film',   "", $task, 'list');
common::printIcon('gametaskinternal', 'off',   "", $task, 'list');
//*/

$verTasks = array();
$verTasksCompleted = array();
$verTasksIncompleted = array();
$verTasksClosed = array();
$verTasksTotalWorkHours = array();

foreach ($gameTasks as $t) {
    if (!array_key_exists($t->version, $verTasks)) {
        $verTasks[$t->version] = array();

        $verTasksCompleted[$t->version] = 0;
        $verTasksIncompleted[$t->version] = 0;
        $verTasksClosed[$t->version] = 0;
        $verTasksTotalWorkHours[$t->version] = 0;
    }

    $verTasks[$t->version][$t->id] = $t;

    $verTasksCompleted[$t->version] += $t->completed ? 1 : 0;
    $verTasksIncompleted[$t->version] += $t->completed ? 0 : 1;
    $verTasksClosed[$t->version] += $t->closed ? 1 : 0;
    $verTasksTotalWorkHours[$t->version] += $t->workhour;
}

?>

<table class='table tablesorter table-data table-hover table-striped table-fixed block-project'>
    <thead>
    <tr class='text-center'>
        <th class='text-left'><?php echo $lang->gametaskinternal->version; ?></th>
        <th width='150'><?php echo $lang->gametaskinternal->deadline; ?></th>
        <th width='50'><?php echo $lang->gametaskinternal->incomplete; ?></th>
        <th width='80'><?php echo $lang->gametaskinternal->completed; ?></th>
        <th width='60'><?php echo $lang->gametaskinternal->closed; ?></th>
        <th width='115'><?php echo $lang->gametaskinternal->totalworkhours; ?></th>
        <th width='115'><?php echo $lang->gametaskinternal->progress; ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($verTasks as $ver => $tasks): ?>
        <tr>
            <td class='text-left'>
                <?php
                $vars = "matchVer=$ver";
                $indexVerLink = helper::createLink('gametaskinternal', 'statbydept', $vars);
                echo html::a($indexVerLink, $versions[$ver]);
                ?>
            </td>
            <td class='text-center'>
                <?php echo date("Y-m-d", strtotime($verDeadlines[$ver]));?>
                <?php echo date("m", strtotime($verDeadlines[$ver]));?>
            </td>
            <td class='text-center'>
                <?php echo ($verTasksIncompleted[$ver]);?>
            </td>
            <td class='text-center'>
                <?php echo ($verTasksCompleted[$ver]);?>
            </td>
            <td class='text-center'>
                <?php echo ($verTasksClosed[$ver]);?>
            </td>
            <td class='text-center'>
                <?php echo ($verTasksTotalWorkHours[$ver]);?>
            </td>
            <td class='text-left'>
                <img class='progressbar'
                     src='<?php echo $this->app->getWebRoot(); ?>theme/default/images/main/green.png' alt='' height='16'
                     width='<?php echo $verTasksCompleted[$ver]/count($verTasks[$ver]) * 0.7 * 115; ?>'>
                <small><?php echo round($verTasksCompleted[$ver]/count($verTasks[$ver]) * 100); ?>%</small>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>


<?php include '../../common/view/footer.html.php'; ?>
