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

$deptTasks = array();
$verTasksCompleted = array();
$verTasksIncompleted = array();
$verTasksClosed = array();
$verTasksTotalWorkHours = array();
$worksHoursByMonth = array();

$personalTasks = array();
$worksHoursPersionalByMonth = array();

foreach ($gameTasks as $t) {
    if (!array_key_exists($t->dept, $deptTasks)) {
        $deptTasks[$t->dept] = array();

        $verTasksCompleted[$t->dept] = 0;
        $verTasksIncompleted[$t->dept] = 0;
        $verTasksClosed[$t->dept] = 0;
        $verTasksTotalWorkHours[$t->dept] = 0;
    }

    if (!array_key_exists($t->assignedTo, $personalTasks)) {
        $personalTasks[$t->assignedTo] = array();
    }

    $deptTasks[$t->dept][$t->id] = $t;
    $personalTasks[$t->assignedTo][$t->id] = $t;

    //echo "$t->assignedTo $t->title $t->dept <br>";

    $verTasksCompleted[$t->dept] += $t->completed ? 1 : 0;
    $verTasksIncompleted[$t->dept] += $t->completed ? 0 : 1;
    $verTasksClosed[$t->dept] += $t->closed ? 1 : 0;
    $verTasksTotalWorkHours[$t->dept] += $t->workhour;

    $completeInMonth = date("m", $t->completeDate);

    if(!array_key_exists($completeInMonth, $worksHoursByMonth))
    {
        $worksHoursByMonth[$completeInMonth] = array();
        $worksHoursByMonth[$completeInMonth][$t->dept] = 0;
    }

    if(!array_key_exists($completeInMonth, $worksHoursPersionalByMonth))
    {
        $worksHoursPersionalByMonth[$completeInMonth] = array();
        $worksHoursPersionalByMonth[$completeInMonth][$t->owner] = 0;
    }

    $worksHoursByMonth[$completeInMonth][$t->dept] += $t->workhour;
    $worksHoursPersionalByMonth[$completeInMonth][$t->assignedTo] += $t->workhour;
}

?>

<table class='table tablesorter table-data table-hover table-striped table-fixed block-project'>
    <thead>
    <tr class='text-center'>
        <th class='text-left'><?php echo $lang->gametaskinternal->dept; ?></th>
        <th width='50'><?php echo $lang->gametaskinternal->incomplete; ?></th>
        <th width='80'><?php echo $lang->gametaskinternal->completed; ?></th>
        <th width='60'><?php echo $lang->gametaskinternal->closed; ?></th>
        <th width='115'><?php echo $lang->gametaskinternal->totalworkhours; ?></th>
        <th width='115'><?php echo $lang->gametaskinternal->progress; ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($deptTasks as $d => $tasks): ?>
        <tr>
            <td class='text-left'>
                <?php echo $depts[$d]; ?>
            </td>
            <td class='text-center'>
                <?php echo($verTasksIncompleted[$d]); ?>
            </td>
            <td class='text-center'>
                <?php echo($verTasksCompleted[$d]); ?>
            </td>
            <td class='text-center'>
                <?php echo($verTasksClosed[$d]); ?>
            </td>
            <td class='text-center'>
                <?php echo($verTasksTotalWorkHours[$d]); ?>
            </td>
            <td class='text-left'>
                <img class='progressbar'
                     src='<?php echo $this->app->getWebRoot(); ?>theme/default/images/main/green.png' alt='' height='16'
                     width='<?php echo $verTasksCompleted[$d] / count($deptTasks[$d]) * 0.7 * 115; ?>'>
                <small><?php echo round($verTasksCompleted[$d] / count($deptTasks[$d]) * 100); ?>%</small>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<br>
<table class='table tablesorter table-data table-hover table-striped table-fixed block-project'>
    <thead>
    <tr class='text-center'>
        <th class='text-left'><?php echo $lang->gametaskinternal->dept; ?> > 工时</th>
        <?php foreach ($worksHoursByMonth as $m => $tasks): ?>
            <th  width='50'><?php echo $m; ?>月(H)</th>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($deptTasks as $d => $tasks): ?>
    <tr>
        <td class='text-left'>
            <?php echo $depts[$d]; ?>
        </td>
        <?php foreach ($worksHoursByMonth as $m => $tasks): ?>
            <td  width='50' class='text-center'>
                <?php echo $tasks[$d]; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>

    </tbody>
</table>

<br>
<table class='table tablesorter table-data table-hover table-striped table-fixed block-project'>
    <thead>
    <tr class='text-center'>
        <th class='text-left'>个人 > 工时比</th>
        <?php foreach ($worksHoursPersionalByMonth as $m => $tasks): ?>
            <th  width='100'><?php echo $m; ?>月(%)</th>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($personalTasks as $d => $tasks): ?>
        <tr>
            <td class='text-left'>
                <?php echo $allUsers[$d]; ?>
            </td>
            <?php foreach ($worksHoursPersionalByMonth as $m => $tasks): ?>
                <td  width='100' class='text-center'>
                    <?php echo "" . round($tasks[$d] / (22 * 8) * 100) . "%" ; ?>
                </td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>

    </tbody>
</table>


<div align="center"><?php echo html::backButton(); ?></div>

<?php include '../../common/view/footer.html.php'; ?>
