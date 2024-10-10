<?php
/**
 * The report view file of task module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      wenjie<wenjie@cnezsoft.com>
 * @package     project
 * @version     $Id: report.html.php 1594 2011-04-10 11:00:00Z wj $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/chart.html.php';?>


<div id='titlebar'>
    <div class='heading'>
        <?php echo html::icon($lang->icons['build']);?> <?php echo $lang->project->buildex;?>
    </div>
    <div class='actions'>
        <?php common::printIcon('buildex', 'create', "project=$project->id");?>
    </div>
</div>


<div class='row'>
    <div class='col-md-3 col-lg-2'>
        <div class='panel panel-sm'>
            <div class='panel-heading'>
                <strong><?php echo $lang->project->report->select;?></strong>
            </div>
            <div class='panel-body' style='padding-top:0'>
                <form method='post'>
                    <?php echo html::radio('milestoneID', $milestones, $lastMilestoneID, '', 'block')?>
                    <?php echo html::submitButton($lang->project->report->create, "", 'btn btn-sm btn-primary');?>
                </form>
            </div>
        </div>
    </div>
    <div class='col-md-9 col-lg-10'>
        <div class='panel panel-sm'>
            <div class='panel-heading'>
                <strong>
                    <?php
                    echo $lang->project->report->common;
                    echo "<div>本里程碑总人天：<span class='red'>{$milestoneDays} (人天)</span> (不包含周末)  从 需求截止<span class='red'>{$milestoneStartDate}</span> 到 开发截止<span class='red'>{$milestoneEndDate}</span></div>"
                    ?>
                </strong>
            </div>
            <div>
                <?php
                //echo count($charts);
                ?>
            </div>
            <table class='table active-disabled'>
                <?php foreach($charts as $chartType => $chartOption):?>
                    <tr class='text-top'>
                        <td>
                            <div class='chart-wrapper text-center'>
                                <h5><?php echo $chartOption->title;?></h5>
                                <div class='chart-canvas'><canvas id='chart-<?php echo $chartType ?>' width='<?php echo $chartOption->width;?>' height='<?php echo $chartOption->height;?>' data-responsive='true'></canvas></div>
                            </div>
                        </td>
                        <td style='width: 320px'>
                            <div style="overflow:auto;" class='table-wrapper'>
                                <table class='table table-condensed table-hover table-striped table-bordered table-chart' data-chart='<?php echo $chartOption->type; ?>' data-target='#chart-<?php echo $chartType ?>' data-animation='false'>
                                    <thead>
                                    <tr>
                                        <th class='chart-label' colspan='2'><?php echo "用户名";?></th>
                                        <th><?php echo $lang->project->burnexReport;?></th>
                                        <th><?php echo $lang->project->percent;?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $idx = 1;?>
                                    <?php foreach($datas[$chartType] as $key => $data):?>
                                        <tr class='text-center'>
                                            <td class='chart-label w-20px'><?php echo $idx; ++$idx; ?></td>
                                            <td class='chart-label'><?php echo $data->name;?></td>
                                            <td class='chart-value'><?php echo $data->value;?></td>
                                            <td><?php echo ($data->percent * 100) . '%';?></td>
                                        </tr>
                                    <?php endforeach;?>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                <?php endforeach;?>
            </table>
        </div>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
