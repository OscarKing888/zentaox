<?php
/**
 * The control file of build module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     build
 * @version     $Id: control.php 4992 2013-07-03 07:21:59Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class burnex extends control
{
    public function index($projectID, $browseType = 'all')
    {

        $this->loadModel('report');
        $this->view->charts = array();

        if (!empty($_POST)) {
            foreach ($this->post->charts as $chart) {
                $chartFunc = 'getDataOf' . $chart;
                $chartData = $this->task->$chartFunc();
                $chartOption = $this->lang->task->report->$chart;
                $this->task->mergeChartOption($chart);

                $this->view->charts[$chart] = $chartOption;
                $this->view->datas[$chart] = $this->report->computePercent($chartData);
            }
        }

        //$this->project->setMenu($this->project->getPairs(), $projectID);
        $this->projects = $this->project->getPairs();
        $this->view->title = $this->projects[$projectID] . $this->lang->colon . $this->lang->task->report->common;
        $this->view->position[] = $this->projects[$projectID];
        $this->view->position[] = $this->lang->task->report->common;
        $this->view->projectID = $projectID;
        $this->view->browseType = $browseType;
        $this->view->checkedCharts = $this->post->charts ? join(',', $this->post->charts) : '';

        $this->display();
    }
}
