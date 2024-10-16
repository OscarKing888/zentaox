<?php

/**
 * The control file of task module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     task
 * @version     $Id: control.php 5106 2013-07-12 01:28:54Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class task extends control
{
    /**
     * Construct function, load model of project and story modules.
     *
     * @access public
     * @return void
     */
    public function __construct($module = '', $method = '')
    {
        parent::__construct($module, $method);
        $this->loadModel('project');
        $this->loadModel('story');
        $this->loadModel('tree');
        $this->loadModel('dept');

        if ($this->config->global->flow == 'onlyTask') {
            $this->config->task->customCreateFields = str_replace(array('story,'), '', $this->config->task->customCreateFields);
            $this->config->task->customBatchCreateFields = str_replace(array('story,'), '', $this->config->task->customBatchCreateFields);
            $this->config->task->custom->batchCreateFields = str_replace(array('story,'), '', $this->config->task->custom->batchCreateFields);
        }
    }

    /**
     * Create a task.
     *
     * @param  int $projectID
     * @param  int $storyID
     * @param  int $moduleID
     * @param  int $taskID
     * @access public
     * @return void
     */
    public function create($projectID = 0, $storyID = 0, $moduleID = 0, $taskID = 0)
    {
        $task = new stdClass();
        $task->module = $moduleID;
        $task->assignedTo = '';
        $task->name = '';
        $task->story = $storyID;
        $task->type = '';
        $task->pri = '';
        $task->estimate = '';
        $task->desc = '';
        $task->estStarted = '';
        $task->deadline = '';
        $task->mailto = '';
        $task->color = '';
        $task->dept = 3; // 默认是原画组

        $story = $this->story->getByID($storyID);

        if ($story) {
            $task->name = $story->title;
            $task->desc = $story->spec;
        }

        if ($taskID > 0) {
            $task = $this->task->getByID($taskID);
            $projectID = $task->project;
        }

        $project = $this->project->getById($projectID);
        $taskLink = $this->createLink('project', 'browse', "projectID=$projectID&tab=task");
        $storyLink = $this->session->storyList ? $this->session->storyList : $this->createLink('project', 'story', "projectID=$projectID");

        /* Set menu. */
        $this->project->setMenu($this->project->getPairs(), $project->id);

        if (!empty($_POST)) {
            $response['result'] = 'success';
            $response['message'] = '';

            $tasksID = $this->task->create($projectID);
            if (dao::isError()) {
                $response['result'] = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            /* if the count of tasksID is 1 then check exists. */
            if (count($tasksID) == 1) {
                $taskID = current($tasksID);
                if ($taskID['status'] == 'exists') {
                    $response['locate'] = $this->createLink('task', 'view', "taskID={$taskID['id']}");
                    $response['message'] = sprintf($this->lang->duplicate, $this->lang->task->common);
                    $this->send($response);
                }
            }

            /* Create actions. */
            $this->loadModel('action');
            foreach ($tasksID as $taskID) {
                /* if status is exists then this task has exists not new create. */
                if ($taskID['status'] == 'exists') continue;

                $taskID = $taskID['id'];
                $actionID = $this->action->create('task', $taskID, 'Opened', '');
                $this->task->sendmail($taskID, $actionID);
            }

            /* If link from no head then reload*/
            if (isonlybody()) {
                $response['locate'] = 'reload';
                $response['target'] = 'parent';
                $this->send($response);
            }

            /* Locate the browser. */
            if ($this->post->after == 'continueAdding') {
                $response['message'] = $this->lang->task->successSaved . $this->lang->task->afterChoices['continueAdding'];
                $response['locate'] = $this->createLink('task', 'create', "projectID=$projectID&storyID={$this->post->story}&moduleID=$moduleID");
                $this->send($response);
            } elseif ($this->post->after == 'toTaskList') {
                $response['locate'] = $taskLink;
                $this->send($response);
            } elseif ($this->post->after == 'toStoryList') {
                $response['locate'] = $storyLink;
                $this->send($response);
            } else {
                $response['locate'] = $taskLink;
                $this->send($response);
            }
        }

        $users = $this->loadModel('user')->getPairs('noclosed|nodeleted');
        $moduleIdList = $this->tree->getAllChildID($moduleID);
        $stories = $this->story->getProjectStoryPairs($projectID, 0, 0, $moduleIdList);
        $members = $this->project->getTeamMemberPairs($projectID, 'nodeleted');
        $moduleOptionMenu = $this->tree->getTaskOptionMenu($projectID);

        $title = $project->name . $this->lang->colon . $this->lang->task->create;
        $position[] = html::a($taskLink, $project->name);
        $position[] = $this->lang->task->common;
        $position[] = $this->lang->task->create;

        /* Set Custom*/
        foreach (explode(',', $this->config->task->customCreateFields) as $field) $customFields[$field] = $this->lang->task->$field;
        if ($project->type == 'ops') unset($customFields['story']);

        $this->view->customFields = $customFields;
        $this->view->showFields = $this->config->task->custom->createFields;

        $this->view->title = $title;
        $this->view->position = $position;
        $this->view->project = $project;
        $this->view->task = $task;
        $this->view->users = $users;
        $this->view->stories = $stories;
        $this->view->members = $members;
        $this->view->moduleOptionMenu = $moduleOptionMenu;

        $this->view->depts = $this->dept->getOptionMenu(); //oscar:

        $this->display();
    }

    /**
     * Batch create task.
     *
     * @param int $projectID
     * @param int $storyID
     * @param int $iframe
     * @param int $taskID
     *
     * @access public
     * @return void
     */
    public function batchCreate($projectID = 0, $storyID = 0, $iframe = 0, $taskID = 0, $createType = 'manualBatchCreate')
    {
        $project = $this->project->getById($projectID);
        $taskLink = $this->createLink('project', 'browse', "projectID=$projectID&tab=task");
        $storyLink = $this->session->storyList ? $this->session->storyList : $this->createLink('project', 'story', "projectID=$projectID");

        /* Set menu. */
        $this->project->setMenu($this->project->getPairs(), $project->id);

        if (!empty($_POST)) {
            $mails = $this->task->batchCreate($projectID, $storyID, $iframe, $taskID, $createType); // oscar
            if (dao::isError()) die(js::error(dao::getError()));

            foreach ($mails as $mail) $this->task->sendmail($mail->taskID, $mail->actionID);

            /* Locate the browser. */
            if ($iframe) die(js::reload('parent.parent'));
            die(js::locate($storyLink, 'parent'));
        }

        /* Set Custom*/
        foreach (explode(',', $this->config->task->customBatchCreateFields) as $field) $customFields[$field] = $this->lang->task->$field;
        $this->view->customFields = $customFields;
        $this->view->showFields = $this->config->task->custom->batchCreateFields;

        $stories = $this->story->getProjectStoryPairs($projectID, 0, 0, 0, 'short');
        $members = $this->project->getTeamMemberPairs($projectID, 'nodeleted');
        // oscar: 创建任务时可以指派给任何人
	//$members = $this->view->users = $this->loadModel('user')->getPairs('noclosed|nodeleted');

        $modules = $this->loadModel('tree')->getTaskOptionMenu($projectID);
        $title = $project->name . $this->lang->colon . $this->lang->task->batchCreate;
        $position[] = html::a($taskLink, $project->name);
        $position[] = $this->lang->task->common;
        $position[] = $this->lang->task->batchCreate;

        $this->view->title = $title;
        $this->view->position = $position;
        $this->view->project = $project;
        $this->view->stories = $stories;
        $this->view->modules = $modules;
        $this->view->parent = $taskID;
        $this->view->parentTask = $this->task->getByID($taskID); // oscar
        $this->view->storyID = $storyID;
        $this->view->story = $this->story->getByID($storyID);
        $this->view->storyTasks = $this->task->getStoryTaskCounts(array_keys($stories), $projectID);
        $this->view->members = $members;

        //oscar:
        $this->loadModel('dept');
        $this->dept->setupDeptUsers($this->view, $this->app->user->account, $this->app->user->dept);
        //oscar:

        $this->display();
    }

    public function batchCreateRoot($projectID = 0, $storyID = 0, $iframe = 0, $taskID = 0)
    {
        $this->batchCreate($projectID, $storyID, $iframe, $taskID, 'batchCreateRoot');
    }

    /**
     * Common actions of task module.
     *
     * @param  int $taskID
     * @access public
     * @return void
     */
    public function commonAction($taskID)
    {
        $this->view->task = $this->loadModel('task')->getByID($taskID);
        $this->view->project = $this->project->getById($this->view->task->project);
        $this->view->members = $this->project->getTeamMemberPairs($this->view->project->id, 'nodeleted');
        $this->view->actions = $this->loadModel('action')->getList('task', $taskID);

        /* Set menu. */
        $this->project->setMenu($this->project->getPairs(), $this->view->project->id);
        $this->view->position[] = html::a($this->createLink('project', 'browse', "project={$this->view->task->project}"), $this->view->project->name);
    }

    /**
     * Edit a task.
     *
     * @param  int $taskID
     * @access public
     * @return void
     */
    public function edit($taskID, $comment = false)
    {
        $this->commonAction($taskID);

        $task = $this->task->getById($taskID);

        if (!empty($_POST)) {
            $this->loadModel('action');
            $changes = array();
            $files = array();
            if ($comment == false) {
                $changes = $this->task->update($taskID);
                if (dao::isError()) die(js::error(dao::getError()));
                $files = $this->loadModel('file')->saveUpload('task', $taskID);
            }

            //$task = $this->task->getById($taskID);
            if ($this->post->comment != '' or !empty($changes) or !empty($files)) {
                $action = !empty($changes) ? 'Edited' : 'Commented';
                $fileAction = !empty($files) ? $this->lang->addFiles . join(',', $files) . "\n" : '';
                $actionID = $this->action->create('task', $taskID, $action, $fileAction . $this->post->comment);
                if (!empty($changes)) $this->action->logHistory($actionID, $changes);
                $this->task->sendmail($taskID, $actionID);
            }

            if ($task->fromBug != 0) {
                foreach ($changes as $change) {
                    if ($change['field'] == 'status') {
                        $confirmURL = $this->createLink('bug', 'view', "id=$task->fromBug");
                        $cancelURL = $this->server->HTTP_REFERER;
                        die(js::confirm(sprintf($this->lang->task->remindBug, $task->fromBug), $confirmURL, $cancelURL, 'parent', 'parent'));
                    }
                }
            }
            die(js::locate($this->createLink('task', 'view', "taskID=$taskID"), 'parent'));
        }

        $noclosedProjects = $this->project->getPairs('noclosed,nocode');
        unset($noclosedProjects[$this->view->project->id]);
        $this->view->projects = array($this->view->project->id => $this->view->project->name) + $noclosedProjects;

        if (!isset($this->view->members[$this->view->task->assignedTo]))
        {
            $this->view->members[$this->view->task->assignedTo] = $this->view->task->assignedTo;
        }

        $this->view->title = $this->lang->task->edit . 'TASK' . $this->lang->colon . $this->view->task->name;
        $this->view->position[] = $this->lang->task->common;
        $this->view->position[] = $this->lang->task->edit;
        $this->view->stories = $this->story->getProjectStoryPairs($this->view->project->id);
        $this->view->users = $this->loadModel('user')->getPairs('nodeleted', "{$this->view->task->openedBy},{$this->view->task->canceledBy},{$this->view->task->closedBy}");
        $this->view->modules = $this->tree->getTaskOptionMenu($this->view->task->project);
        $this->view->depts = $this->dept->getOptionMenu(); //oscar:

        // oscar
        $milestones = $this->dao->select('id, name')->from(TABLE_TASKMILESTONE)
            ->where('project')->eq($this->view->project->id)
            ->orderBy('id desc')
            ->fetchPairs('id');
        $milestones[0] = '无';
        $this->view->milestones = $milestones;
        // oscar

        $this->display();
    }

    /**
     * Batch edit task.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function batchEdit($projectID = 0)
    {
        if ($this->post->names) {
            $allChanges = $this->task->batchUpdate();

            if (!empty($allChanges)) {
                foreach ($allChanges as $taskID => $changes) {
                    if (empty($changes)) continue;

                    $actionID = $this->loadModel('action')->create('task', $taskID, 'Edited');
                    $this->action->logHistory($actionID, $changes);
                    $this->task->sendmail($taskID, $actionID);

                    $task = $this->task->getById($taskID);
                    if ($task->fromBug != 0) {
                        foreach ($changes as $change) {
                            if ($change['field'] == 'status') {
                                $confirmURL = $this->createLink('bug', 'view', "id=$task->fromBug");
                                $cancelURL = $this->server->HTTP_REFERER;
                                die(js::confirm(sprintf($this->lang->task->remindBug, $task->fromBug), $confirmURL, $cancelURL, 'parent', 'parent'));
                            }
                        }
                    }
                }
            }
            $this->loadModel('score')->create('ajax', 'batchOther');
            die(js::locate($this->session->taskList, 'parent'));
        }

        $taskIDList = $this->post->taskIDList ? $this->post->taskIDList : die(js::locate($this->session->taskList, 'parent'));
        $taskIDList = array_unique($taskIDList);

        /* The tasks of project. */
        if ($projectID) {
            $project = $this->project->getById($projectID);
            $this->project->setMenu($this->project->getPairs(), $project->id);

            /* Set modules and members. */
            $modules = $this->tree->getTaskOptionMenu($projectID);
            $modules = array('ditto' => $this->lang->task->ditto) + $modules;
            $members = $this->project->getTeamMemberPairs($projectID, 'nodeleted');
            $members = array('' => '', 'ditto' => $this->lang->task->ditto) + $members;
            $members['closed'] = 'Closed';

            $this->view->title = $project->name . $this->lang->colon . $this->lang->task->batchEdit;
            $this->view->position[] = html::a($this->createLink('project', 'browse', "project=$project->id"), $project->name);
            $this->view->project = $project;
            $this->view->modules = $modules;
            $this->view->members = $members;
        } /* The tasks of my. */
        else {
            $this->lang->task->menu = $this->lang->my->menu;
            $this->lang->set('menugroup.task', 'my');
            $this->lang->task->menuOrder = $this->lang->my->menuOrder;
            $this->loadModel('my')->setMenu();
            $this->view->position[] = html::a($this->createLink('my', 'task'), $this->lang->my->task);
            $this->view->title = $this->lang->task->batchEdit;
            $this->view->users = $this->loadModel('user')->getPairs('noletter');
        }

        /* Get edited tasks. */
        $tasks = $this->dao->select('*')->from(TABLE_TASK)->where('id')->in($taskIDList)->fetchAll('id');

        /* Judge whether the editedTasks is too large and set session. */
        $countInputVars = count($tasks) * (count(explode(',', $this->config->task->custom->batchEditFields)) + 3);
        $showSuhosinInfo = common::judgeSuhosinSetting($countInputVars);
        if ($showSuhosinInfo) $this->view->suhosinInfo = extension_loaded('suhosin') ? sprintf($this->lang->suhosinInfo, $countInputVars) : sprintf($this->lang->maxVarsInfo, $countInputVars);

        /* Set Custom*/
        foreach (explode(',', $this->config->task->customBatchEditFields) as $field) $customFields[$field] = $this->lang->task->$field;
        $this->view->customFields = $customFields;
        $this->view->showFields = $this->config->task->custom->batchEditFields;

        /* Assign. */
        $this->view->position[] = $this->lang->task->common;
        $this->view->position[] = $this->lang->task->batchEdit;
        $this->view->projectID = $projectID;
        $this->view->priList = array('0' => '', 'ditto' => $this->lang->task->ditto) + $this->lang->task->priList;
        $this->view->statusList = array('' => '', 'ditto' => $this->lang->task->ditto) + $this->lang->task->statusList;
        $this->view->typeList = array('' => '', 'ditto' => $this->lang->task->ditto) + $this->lang->task->typeList;
        $this->view->taskIDList = $taskIDList;
        $this->view->tasks = $tasks;
        $this->view->projectName = isset($project) ? $project->name : '';

        $this->view->depts = $this->dept->getOptionMenu(); //oscar:

        $this->display();
    }

    /**
     * Update assign of task
     *
     * @param  int $requestID
     * @access public
     * @return void
     */
    public function assignTo($projectID, $taskID)
    {
        $this->commonAction($taskID);

        if (!empty($_POST)) {
            $this->loadModel('action');
            $changes = $this->task->assign($taskID);
            if (dao::isError()) die(js::error(dao::getError()));
            $actionID = $this->action->create('task', $taskID, 'Assigned', $this->post->comment, $this->post->assignedTo);
            $this->action->logHistory($actionID, $changes);
            $this->task->sendmail($taskID, $actionID);

            if (isonlybody()) die(js::closeModal('parent.parent', 'this'));
            die(js::locate($this->createLink('task', 'view', "taskID=$taskID"), 'parent'));
        }

        $task = $this->task->getByID($taskID);

        $members = $this->project->getTeamMemberPairs($projectID, 'nodeleted');

        /* Compute next assignedTo. */
        if (!empty($task->team)) {
            $task->assignedTo = $this->task->getNextUser(array_keys($task->team), $task->assignedTo);
            $members = $this->task->getMemberPairs($task);
        }

        $this->view->title = $this->view->project->name . $this->lang->colon . $this->lang->task->assign;
        $this->view->position[] = $this->lang->task->assign;
        $this->view->task = $task;
        $this->view->members = $members;
        $this->view->users = $this->loadModel('user')->getPairs();
        $this->display();
    }

    /**
     * Batch change the module of task.
     *
     * @param  int $moduleID
     * @access public
     * @return void
     */
    public function batchChangeModule($moduleID)
    {
        if ($this->post->taskIDList) {
            $taskIDList = $this->post->taskIDList;
            //$taskIDList = array_unique($taskIDList);
            unset($_POST['taskIDList']);
            $allChanges = $this->task->batchChangeModule($taskIDList, $moduleID);
            if (dao::isError()) die(js::error(dao::getError()));
            foreach ($allChanges as $taskID => $changes) {
                $this->loadModel('action');
                $actionID = $this->action->create('task', $taskID, 'Edited');
                $this->action->logHistory($actionID, $changes);
                $this->task->sendmail($taskID, $actionID);
            }
            if (!dao::isError()) $this->loadModel('score')->create('ajax', 'batchOther');
        }
        die(js::reload('parent'));
    }

    public function batchSetWorkhour()
    {
        /*
        $msg = " count:" . count($_POST) . "  ";
        $msg .= " workHour:" . $this->post->workHour . "  ";

        foreach ($_POST as $p) {
            if (is_array($p)) {
                foreach ($p as $pp) {
                    $msg .= "<br> _P:" . $pp;
                }
            } else {
                $msg .= "<br>   _PP:" . $p;
            }
        }
        //*/

        if (!empty($_POST)) {
            //error_log( var_export($_POST));

            $estimate = $this->post->workHour;
            $taskIDList = $this->post->taskIDList;
            //$taskIDList = array_unique($taskIDList);
            //unset($_POST['taskIDList']);
            if (!is_array($taskIDList)) die(js::locate($this->createLink('project', 'task', ""), 'parent'));

            //foreach ($taskIDList as $item) {$msg .= $item . "  ";}

            //echo js::alert("batchSetWorkhour: $msg");
            //echo $msg;

            $workDays = Math.ceil($estimate / 8);


            foreach ($taskIDList as $taskID) {


                $oldTask = $this->task->getById($taskID);

                $deadline = helper::dayafter($oldTask->estStarted, $workDays);
                //error_log("assign task:" . $oldTask->name . " newDeadline:" . $deadline. " deadline:" . $oldTask->deadline);

                $dat = new stdClass();
                $dat->estimate = $estimate;
                $dat->deadline = $deadline;

                $this->dao->update(TABLE_TASK)
                    ->data($dat)
                    //->set('estimate')->eq($assignedTo)
                    //->set('deadline')->eq($deadline)
                    ->where('id')->eq($taskID)->exec();

                //$this->loadModel('action');
                //$changes = $this->task->assign($taskID);
                if (dao::isError()) die(js::error(dao::getError()));
                //$actionID = $this->action->create('task', $taskID, 'Assigned', $this->post->comment, $this->post->assignedTo);
                //$this->action->logHistory($actionID, $changes);
                //$this->task->sendmail($taskID, $actionID);
            }
            //if(!dao::isError()) $this->loadModel('score')->create('ajax', 'batchOther');
            die(js::reload('parent'));
            //$this->locate(inlink('details'));
            //$this->display();
            //echo js::alert("assignTo: $assignedTo");
        }
    }

    public function batchAssignToDept($dept)
    {
        if ($this->post->taskIDList) {
            $taskIDList = $this->post->taskIDList;
            //$taskIDList = array_unique($taskIDList);
            unset($_POST['taskIDList']);
            $allChanges = $this->task->batchAssignToDept($taskIDList, $dept);
            if (dao::isError()) die(js::error(dao::getError()));
            foreach ($allChanges as $taskID => $changes) {
                $this->loadModel('action');
                $actionID = $this->action->create('task', $taskID, 'Edited');
                $this->action->logHistory($actionID, $changes);
                $this->task->sendmail($taskID, $actionID);
            }
            if (!dao::isError()) $this->loadModel('score')->create('ajax', 'batchOther');
        }
        die(js::reload('parent'));
    }

    public function batchChangePriority()
    {
        if ($this->post->taskIDList) {
            $taskIDList = $this->post->taskIDList;
            unset($_POST['taskIDList']);

            $allChanges  = $this->task->batchChangePriority($taskIDList, $this->post->pri);
            if(dao::isError()) die(js::error(dao::getError()));
            foreach ($allChanges as $taskID => $changes) {
                $this->loadModel('action');
                $actionID = $this->action->create('task', $taskID, 'Edited');
                $this->action->logHistory($actionID, $changes);
                $this->task->sendmail($taskID, $actionID);
            }
            if (!dao::isError()) $this->loadModel('score')->create('ajax', 'batchOther');
        }
        die(js::reload('parent'));
    }

    /**
     * Batch update assign of task.
     *
     * @param  int $project
     * @access public
     * @return void
     */
    public function batchAssignTo($project, $account)
    {
        if (!empty($_POST)) {
            /*
            error_log("batchAssignTo proj:$project acc:$account");
            foreach ($_POST as $k => $v) {
                error_log("==== post data k:$k v:$v");
            }
            //*/


            $taskIDList = $this->post->taskIDList;
            //$taskIDList = array_unique($taskIDList);
            unset($_POST['taskIDList']);
            if (!is_array($taskIDList)) die(js::locate($this->createLink('project', 'task', "projectID=$project"), 'parent'));
            //$taskIDList = array_unique($taskIDList);
            foreach ($taskIDList as $taskID) {
                $this->loadModel('action');
                $changes = $this->task->assign($taskID, $account);
                //error_log("batchAssignTo task:$taskID");
                if (dao::isError()) die(js::error(dao::getError()));
                $actionID = $this->action->create('task', $taskID, 'Assigned', $this->post->comment, $this->post->assignedTo);
                $this->action->logHistory($actionID, $changes);
                $this->task->sendmail($taskID, $actionID);
            }
            if (!dao::isError()) $this->loadModel('score')->create('ajax', 'batchOther');
            die(js::reload('parent'));
        }
    }

    public static function __callStatic($name, $arguments)
    {
        // TODO: Implement __callStatic() method.
    }


    // oscar[

    public function batchAssignToCheckByGD($project, $account)
    {
        if (!empty($_POST)) {
            /*
             error_log("batchAssignToCheckByGD proj:$project acc$account");
            foreach ($_POST as $k => $v) {
                error_log("==== k:$k v:$v");
            }
            //*/


            $taskIDList = $this->post->taskIDList;
            //$taskIDList = array_unique($taskIDList);
            unset($_POST['taskIDList']);
            if (!is_array($taskIDList)) die(js::locate($this->createLink('project', 'task', "projectID=$project"), 'parent'));
            //$taskIDList = array_unique($taskIDList);
            foreach ($taskIDList as $taskID) {
                $this->loadModel('action');
                $changes = $this->task->assignCheckByGD($taskID, $account);
                //error_log("batchAssignToCheckByGD task:$taskID");
                if (dao::isError()) die(js::error(dao::getError()));
                $actionID = $this->action->create('task', $taskID, 'Assigned To Check By GD', $this->post->comment, $this->post->assignToCheckByGD);
                $this->action->logHistory($actionID, $changes);
                $this->task->sendmail($taskID, $actionID);
            }
            if (!dao::isError()) $this->loadModel('score')->create('ajax', 'batchOther');
            die(js::reload('parent'));
        }
    }
    // oscar]

    /**
     * View a task.
     *
     * @param  int $taskID
     * @access public
     * @return void
     */
    public function view($taskID)
    {
        $task = $this->task->getById($taskID, true);
        if (!$task) die(js::error($this->lang->notFound) . js::locate('back'));

        if ($task->fromBug != 0) {
            $bug = $this->loadModel('bug')->getById($task->fromBug);
            $task->bugSteps = '';
            if ($bug) {
                $task->bugSteps = $this->loadModel('file')->setImgSize($bug->steps);
                foreach ($bug->files as $file) $task->files[] = $file;
            }
            $this->view->fromBug = $bug;
        } else {
            $story = $this->story->getById($task->story);
            //error_log("task story:$task->story title:$story->title");
            //$task->story = $story; //oscar:
            $task->storySpec = empty($story) ? '' : $this->loadModel('file')->setImgSize($story->spec);
            $task->storyVerify = empty($story) ? '' : $this->loadModel('file')->setImgSize($story->verify);
            $task->storyFiles = $this->loadModel('file')->getByObject('story', $task->story);

            $task->story = $story; //oscar:
        }

        if ($task->team) $this->lang->task->assign = $this->lang->task->transfer;

        /* Update action. */
        if ($task->assignedTo == $this->app->user->account) $this->loadModel('action')->read('task', $taskID);

        /* Set menu. */
        $project = $this->project->getById($task->project);
        $this->project->setMenu($this->project->getPairs(), $project->id);

        $title = "TASK#$task->id $task->name / $project->name";
        $position[] = html::a($this->createLink('project', 'browse', "projectID=$task->project"), $project->name);
        $position[] = $this->lang->task->common;
        $position[] = $this->lang->task->view;

        $this->view->title = $title;
        $this->view->position = $position;
        $this->view->project = $project;
        $this->view->task = $task;
        $this->view->actions = $this->loadModel('action')->getList('task', $taskID);
        $this->view->users = $this->loadModel('user')->getPairs('noletter');

        /*foreach ($this->view->users as $k => $v)
        {
            error_log("users $k = $v");
        }*/

        $this->view->preAndNext = $this->loadModel('common')->getPreAndNextObject('task', $taskID);
        $this->view->product = $this->tree->getProduct($task->module);
        $this->view->modulePath = $this->tree->getParents($task->module);
        $this->view->depts = $this->dept->getOptionMenu(); //oscar:

        // oscar
        $milestone = $this->dao->select('id, name')->from(TABLE_TASKMILESTONE)
            ->where('id')->eq($task->milestone)
            ->fetch('name');

        $milestone[0] = '无';
        $this->view->milestone = $milestone;
        // oscar

        $this->view->showFields = $this->config->task->custom->createFields;
        
        $this->display();
    }

    /**
     * Confirm story change
     *
     * @param  int $taskID
     * @access public
     * @return void
     */
    public function confirmStoryChange($taskID)
    {
        $task = $this->task->getById($taskID);
        $this->dao->update(TABLE_TASK)->set('storyVersion')->eq($task->latestStoryVersion)->where('id')->eq($taskID)->exec();
        $this->loadModel('action')->create('task', $taskID, 'confirmed', '', $task->latestStoryVersion);
        die(js::reload('parent'));
    }

    /**
     * Start a task.
     *
     * @param  int $taskID
     * @access public
     * @return void
     */
    public function start($taskID)
    {
        $this->commonAction($taskID);

        $this->loadModel('action');
        $changes = $this->task->startDirect($taskID);


        if (dao::isError()) die(js::error(dao::getError()));

        if ($this->post->comment != '' or !empty($changes)) {
            $act = 'Started';
            $actionID = $this->action->create('task', $taskID, $act, $this->post->comment);
            $this->action->logHistory($actionID, $changes);
            $this->task->sendmail($taskID, $actionID);
        }

        /* Remind whether to update status of the bug, if task which from that bug has been finished. */
        $task = $this->task->getById($taskID);
        if ($changes and $this->task->needUpdateBugStatus($task)) {
            foreach ($changes as $change) {
                if ($change['field'] == 'status' and $change['new'] == 'done') {
                    $confirmURL = $this->createLink('bug', 'view', "id=$task->fromBug");
                    unset($_GET['onlybody']);
                    $cancelURL = $this->createLink('task', 'view', "taskID=$taskID");
                    die(js::confirm(sprintf($this->lang->task->remindBug, $task->fromBug), $confirmURL, $cancelURL, 'parent', 'parent.parent'));
                }
            }

            if (isonlybody()) die(js::closeModal('parent.parent', 'this'));
            die(js::locate($this->createLink('task', 'view', "taskID=$taskID"), 'parent'));
        }
        die(js::reload('parent'));
    }

    public function start_old($taskID)
    {
        $this->commonAction($taskID);

        if (!empty($_POST)) {
            $this->loadModel('action');
            $changes = $this->task->start($taskID);
            if (dao::isError()) die(js::error(dao::getError()));

            if ($this->post->comment != '' or !empty($changes)) {
                $act = $this->post->left == 0 ? 'Finished' : 'Started';
                $actionID = $this->action->create('task', $taskID, $act, $this->post->comment);
                $this->action->logHistory($actionID, $changes);
                $this->task->sendmail($taskID, $actionID);
            }

            /* Remind whether to update status of the bug, if task which from that bug has been finished. */
            $task = $this->task->getById($taskID);
            if ($changes and $this->task->needUpdateBugStatus($task)) {
                foreach ($changes as $change) {
                    if ($change['field'] == 'status' and $change['new'] == 'done') {
                        $confirmURL = $this->createLink('bug', 'view', "id=$task->fromBug");
                        unset($_GET['onlybody']);
                        $cancelURL = $this->createLink('task', 'view', "taskID=$taskID");
                        die(js::confirm(sprintf($this->lang->task->remindBug, $task->fromBug), $confirmURL, $cancelURL, 'parent', 'parent.parent'));
                    }
                }
            }

            if (isonlybody()) die(js::closeModal('parent.parent', 'this'));
            die(js::locate($this->createLink('task', 'view', "taskID=$taskID"), 'parent'));
        }

        $this->view->title = $this->view->project->name . $this->lang->colon . $this->lang->task->start;
        $this->view->position[] = $this->lang->task->start;
        $this->view->users = $this->loadModel('user')->getPairs('noletter');
        $this->display();
    }

    /**
     * Record consumed and estimate.
     *
     * @param  int $taskID
     * @access public
     * @return void
     */
    public function recordEstimate($taskID)
    {
        $this->commonAction($taskID);

        if (!empty($_POST)) {
            $changes = $this->task->recordEstimate($taskID);

            /* Remind whether to update status of the bug, if task which from that bug has been finished. */
            $task = $this->task->getById($taskID);
            if ($changes and $this->task->needUpdateBugStatus($task)) {
                foreach ($changes as $change) {
                    if ($change['field'] == 'status' and $change['new'] == 'done') {
                        $confirmURL = $this->createLink('bug', 'view', "id=$task->fromBug");
                        unset($_GET['onlybody']);
                        $cancelURL = $this->createLink('task', 'view', "taskID=$taskID");
                        die(js::confirm(sprintf($this->lang->task->remindBug, $task->fromBug), $confirmURL, $cancelURL, 'parent', 'parent.parent'));
                    }
                }
            }

            if (isonlybody()) die(js::closeModal('parent.parent', 'this'));
            die(js::locate($this->createLink('task', 'view', "taskID=$taskID"), 'parent'));
        }

        $this->session->set('estimateList', $this->app->getURI(true));
        if (isonlybody() && $this->config->requestType != 'GET') $this->session->set('estimateList', $this->app->getURI(true) . '?onlybody=yes');

        $this->view->task = $this->task->getById($taskID);
        $this->view->estimates = $this->task->getTaskEstimate($taskID);
        $this->view->title = $this->lang->task->record;
        $this->display();
    }

    /**
     * Edit consumed and estimate.
     *
     * @param  int $estimateID
     * @access public
     * @return void
     */
    public function editEstimate($estimateID)
    {
        $estimate = $this->task->getEstimateById($estimateID);
        if (!empty($_POST)) {
            $changes = $this->task->updateEstimate($estimateID);
            if (dao::isError()) die(js::error(dao::getError()));

            $actionID = $this->loadModel('action')->create('task', $estimate->task, 'EditEstimate', $this->post->work);
            $this->action->logHistory($actionID, $changes);

            $url = $this->session->estimateList ? $this->session->estimateList : inlink('record', "taskID={$estimate->task}");
            die(js::locate($url, 'parent'));
        }

        $estimate = $this->task->getEstimateById($estimateID);

        $this->view->title = $this->lang->task->editEstimate;
        $this->view->position[] = $this->lang->task->editEstimate;
        $this->view->estimate = $estimate;
        $this->display();
    }

    /**
     * Delete estimate.
     *
     * @param  int $estimateID
     * @param  string $confirm
     * @access public
     * @return void
     */
    public function deleteEstimate($estimateID, $confirm = 'no')
    {
        if ($confirm == 'no') {
            die(js::confirm($this->lang->task->confirmDeleteEstimate, $this->createLink('task', 'deleteEstimate', "estimateID=$estimateID&confirm=yes")));
        } else {
            $estimate = $this->task->getEstimateById($estimateID);
            $changes = $this->task->deleteEstimate($estimateID);
            if (dao::isError()) die(js::error(dao::getError()));

            $actionID = $this->loadModel('action')->create('task', $estimate->task, 'DeleteEstimate');
            $this->action->logHistory($actionID, $changes);
            die(js::reload('parent'));
        }
    }

    /**
     * Finish a task.
     *
     * @param  int $taskID
     * @access public
     * @return void
     */
    public function finish($taskID)
    {
        $this->commonAction($taskID);

        //if (!empty($_POST))
        {
            $this->loadModel('action');
            $changes = $this->task->finishDirect($taskID);
            if (dao::isError()) die(js::error(dao::getError()));
            $files = $this->loadModel('file')->saveUpload('task', $taskID);

            $task = $this->task->getById($taskID);
            //if ($this->post->comment != '' or !empty($changes))
            if (!empty($changes))
            {
                $fileAction = !empty($files) ? $this->lang->addFiles . join(',', $files) . "\n" : '';
                $actionID = $this->action->create('task', $taskID, 'Finished', $fileAction);
                $this->action->logHistory($actionID, $changes);
                $this->task->sendmail($taskID, $actionID);
            }

            if ($this->task->needUpdateBugStatus($task)) {
                foreach ($changes as $change) {
                    if ($change['field'] == 'status') {
                        $confirmURL = $this->createLink('bug', 'view', "id=$task->fromBug");
                        unset($_GET['onlybody']);
                        $cancelURL = $this->createLink('task', 'view', "taskID=$taskID");
                        die(js::confirm(sprintf($this->lang->task->remindBug, $task->fromBug), $confirmURL, $cancelURL, 'parent', 'parent.parent'));
                    }
                }
            }
            if (isonlybody()) die(js::closeModal('parent.parent', 'this'));
            die(js::locate($this->createLink('task', 'view', "taskID=$taskID"), 'parent'));
        }

        $task = $this->view->task;
        $members = $this->loadModel('user')->getPairs('noletter');

        $this->view->users = $members;
        if (!empty($task->team)) {
            $teams = array_keys($task->team);

            $task->nextBy = $this->task->getNextUser($teams, $task->assignedTo);
            $task->myConsumed = $this->dao->select('consumed')->from(TABLE_TEAM)->where('task')->eq($taskID)->andWhere('account')->eq($task->assignedTo)->fetch('consumed');

            $lastAccount = end($teams);
            if ($lastAccount != $task->assignedTo) {
                $members = $this->task->getMemberPairs($task);
            } else {
                $task->nextBy = $task->openedBy;
            }
        }

        die(js::reload('parent'));
    }

    public function finish_old($taskID)
    {
        $this->commonAction($taskID);

        if (!empty($_POST)) {
            $this->loadModel('action');
            $changes = $this->task->finish($taskID);
            if (dao::isError()) die(js::error(dao::getError()));
            $files = $this->loadModel('file')->saveUpload('task', $taskID);

            $task = $this->task->getById($taskID);
            if ($this->post->comment != '' or !empty($changes)) {
                $fileAction = !empty($files) ? $this->lang->addFiles . join(',', $files) . "\n" : '';
                $actionID = $this->action->create('task', $taskID, 'Finished', $fileAction . $this->post->comment);
                $this->action->logHistory($actionID, $changes);
                $this->task->sendmail($taskID, $actionID);
            }

            if ($this->task->needUpdateBugStatus($task)) {
                foreach ($changes as $change) {
                    if ($change['field'] == 'status') {
                        $confirmURL = $this->createLink('bug', 'view', "id=$task->fromBug");
                        unset($_GET['onlybody']);
                        $cancelURL = $this->createLink('task', 'view', "taskID=$taskID");
                        die(js::confirm(sprintf($this->lang->task->remindBug, $task->fromBug), $confirmURL, $cancelURL, 'parent', 'parent.parent'));
                    }
                }
            }
            if (isonlybody()) die(js::closeModal('parent.parent', 'this'));
            die(js::locate($this->createLink('task', 'view', "taskID=$taskID"), 'parent'));
        }

        $task = $this->view->task;
        $members = $this->loadModel('user')->getPairs('noletter');

        $this->view->users = $members;
        if (!empty($task->team)) {
            $teams = array_keys($task->team);

            $task->nextBy = $this->task->getNextUser($teams, $task->assignedTo);
            $task->myConsumed = $this->dao->select('consumed')->from(TABLE_TEAM)->where('task')->eq($taskID)->andWhere('account')->eq($task->assignedTo)->fetch('consumed');

            $lastAccount = end($teams);
            if ($lastAccount != $task->assignedTo) {
                $members = $this->task->getMemberPairs($task);
            } else {
                $task->nextBy = $task->openedBy;
            }
        }

        $this->view->title = $this->view->project->name . $this->lang->colon . $this->lang->task->finish;
        $this->view->position[] = $this->lang->task->finish;
        $this->view->members = $members;

        $this->display();
    }

    /**
     * Pause task.
     *
     * @param  int $taskID
     * @access public
     * @return void
     */
    public function pause($taskID)
    {
        $this->commonAction($taskID);

        if (!empty($_POST)) {
            $this->loadModel('action');
            $changes = $this->task->pause($taskID);
            if (dao::isError()) die(js::error(dao::getError()));

            if ($this->post->comment != '' or !empty($changes)) {
                $actionID = $this->action->create('task', $taskID, 'Paused', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }
            if (isonlybody()) die(js::closeModal('parent.parent', 'this'));
            die(js::locate($this->createLink('task', 'view', "taskID=$taskID"), 'parent'));
        }

        $this->view->title = $this->view->project->name . $this->lang->colon . $this->lang->task->pause;
        $this->view->position[] = $this->lang->task->pause;

        $this->view->users = $this->loadModel('user')->getPairs('noletter');
        $this->display();
    }

    /**
     * Restart task
     *
     * @param  int $taskID
     * @access public
     * @return void
     */
    public function restart($taskID)
    {
        $this->commonAction($taskID);

        if (!empty($_POST)) {
            $this->loadModel('action');
            $changes = $this->task->start($taskID);
            if (dao::isError()) die(js::error(dao::getError()));

            if ($this->post->comment != '' or !empty($changes)) {
                $act = $this->post->left == 0 ? 'Finished' : 'Restarted';
                $actionID = $this->action->create('task', $taskID, $act, $this->post->comment);
                $this->action->logHistory($actionID, $changes);
                $this->task->sendmail($taskID, $actionID);
            }
            if (isonlybody()) die(js::closeModal('parent.parent', 'this'));
            die(js::locate($this->createLink('task', 'view', "taskID=$taskID"), 'parent'));
        }

        $this->view->title = $this->view->project->name . $this->lang->colon . $this->lang->task->restart;
        $this->view->position[] = $this->lang->task->restart;
        $this->view->users = $this->loadModel('user')->getPairs('noletter');
        $this->display();
    }

    /**
     * Close a task.
     *
     * @param  int $taskID
     * @access public
     * @return void
     */
    public function close($taskID)
    {
        $this->commonAction($taskID);

        if (!empty($_POST)) {
            $this->loadModel('action');
            $changes = $this->task->close($taskID);
            if (dao::isError()) die(js::error(dao::getError()));

            if ($this->post->comment != '' or !empty($changes)) {
                $actionID = $this->action->create('task', $taskID, 'Closed', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
                $this->task->sendmail($taskID, $actionID);
            }
            if (isonlybody()) die(js::closeModal('parent.parent', 'this'));
            die(js::locate($this->createLink('task', 'view', "taskID=$taskID"), 'parent'));
        }

        $this->view->title = $this->view->project->name . $this->lang->colon . $this->lang->task->finish;
        $this->view->position[] = $this->lang->task->finish;
        $this->view->users = $this->loadModel('user')->getPairs('noletter');

        $this->display();

    }

    /**
     * Batch cancel tasks.
     *
     * @param  string $skipTaskIdList
     * @access public
     * @return void
     */
    public function batchCancel()
    {
        if ($this->post->taskIDList) {
            $taskIDList = $this->post->taskIDList;
            //$taskIDList = array_unique($taskIDList);
            unset($_POST['taskIDList']);
            unset($_POST['assignedTo']);
            $this->loadModel('action');

            $tasks = $this->task->getByList($taskIDList);
            foreach ($tasks as $taskID => $task) {
                if ($task->status == 'done' or $task->status == 'closed' or $task->status == 'cancel') continue;

                $changes = $this->task->cancel($taskID);
                if ($changes) {
                    $actionID = $this->action->create('task', $taskID, 'Canceled', '');
                    $this->action->logHistory($actionID, $changes);
                    $this->task->sendmail($taskID, $actionID);
                }
            }
        }

        die(js::reload('parent'));
    }

    /**
     * Batch close tasks.
     *
     * @access public
     * @return void
     */
    public function batchClose($skipTaskIdList = '')
    {
        if ($this->post->taskIDList or $skipTaskIdList) {
            $taskIDList = $this->post->taskIDList;
            //$taskIDList = array_unique($taskIDList);
            if ($skipTaskIdList) $taskIDList = $skipTaskIdList;
            unset($_POST['taskIDList']);
            unset($_POST['assignedTo']);
            $this->loadModel('action');

            $tasks = $this->task->getByList($taskIDList);
            foreach ($tasks as $taskID => $task) {
                if (empty($skipTaskIdList) and ($task->status == 'wait' or $task->status == 'doing')) {
                    $skipTasks[$taskID] = $taskID;
                    continue;
                }

                if ($task->status == 'closed') continue;

                $changes = $this->task->close($taskID);
                if ($changes) {
                    $actionID = $this->action->create('task', $taskID, 'Closed', '');
                    $this->action->logHistory($actionID, $changes);
                    $this->task->sendmail($taskID, $actionID);
                }
            }
            if (isset($skipTasks) and empty($skipTaskIdList)) {
                $skipTasks = join(',', $skipTasks);
                $confirmURL = $this->createLink('task', 'batchClose', "skipTaskIdList=$skipTasks");
                $cancelURL = $this->server->HTTP_REFERER;
                die(js::confirm(sprintf($this->lang->task->error->skipClose, $skipTasks), $confirmURL, $cancelURL, 'self', 'parent'));
            }
            if (!dao::isError()) $this->loadModel('score')->create('ajax', 'batchOther');
        }
        die(js::reload('parent'));
    }

    /**
     * Cancel a task.
     *
     * @param  int $taskID
     * @access public
     * @return void
     */
    public function cancel($taskID)
    {
        $this->commonAction($taskID);

        if (!empty($_POST)) {
            $this->loadModel('action');
            $changes = $this->task->cancel($taskID);
            if (dao::isError()) die(js::error(dao::getError()));

            if ($this->post->comment != '' or !empty($changes)) {
                $actionID = $this->action->create('task', $taskID, 'Canceled', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
                $this->task->sendmail($taskID, $actionID);
            }
            if (isonlybody()) die(js::closeModal('parent.parent', 'this'));
            die(js::locate($this->createLink('task', 'view', "taskID=$taskID"), 'parent'));
        }

        $this->view->title = $this->view->project->name . $this->lang->colon . $this->lang->task->cancel;
        $this->view->position[] = $this->lang->task->cancel;
        $this->view->users = $this->loadModel('user')->getPairs('noletter');

        $this->display();
    }

    /**
     * Activate a task.
     *
     * @param  int $taskID
     * @access public
     * @return void
     */
    public function activate($taskID)
    {
        $this->commonAction($taskID);

        if (!empty($_POST)) {
            $this->loadModel('action');
            $changes = $this->task->activate($taskID);
            if (dao::isError()) die(js::error(dao::getError()));

            if ($this->post->comment != '' or !empty($changes)) {
                $actionID = $this->action->create('task', $taskID, 'Activated', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
                $this->task->sendmail($taskID, $actionID);
            }
            if (isonlybody()) die(js::closeModal('parent.parent', 'this'));
            die(js::locate($this->createLink('task', 'view', "taskID=$taskID"), 'parent'));
        }

        if (!isset($this->view->members[$this->view->task->finishedBy])) $this->view->members[$this->view->task->finishedBy] = $this->view->task->finishedBy;
        $this->view->title = $this->view->project->name . $this->lang->colon . $this->lang->task->activate;
        $this->view->position[] = $this->lang->task->activate;
        $this->view->users = $this->loadModel('user')->getPairs('noletter');
        $this->display();
    }

    /**
     * Delete a task.
     *
     * @param  int $projectID
     * @param  int $taskID
     * @param  string $confirm yes|no
     * @access public
     * @return void
     */
    public function delete($projectID, $taskID, $confirm = 'no')
    {
        $task = $this->task->getById($taskID);
        if ($confirm == 'no') {
            die(js::confirm($this->lang->task->confirmDelete, inlink('delete', "projectID=$projectID&taskID=$taskID&confirm=yes")));
        } else {
            $story = $this->dao->select('story')->from(TABLE_TASK)->where('id')->eq($taskID)->fetch('story');
            $this->task->delete(TABLE_TASK, $taskID);
            $this->task->computeWorkingHours($task->parent);
            if ($task->fromBug != 0) $this->dao->update(TABLE_BUG)->set('toTask')->eq(0)->where('id')->eq($task->fromBug)->exec();
            if ($story) $this->loadModel('story')->setStage($story);
            die(js::locate($this->session->taskList, 'parent'));
        }
    }



    public function batchDelete()
    {
        if ($this->post->taskIDList) {
            $taskIDList = $this->post->taskIDList;

            foreach ($taskIDList as $taskID) {
                //error_log("oscar: batchdeleteTask:$taskID");
            }

            //$taskIDList = array_unique($taskIDList);
            unset($_POST['taskIDList']);
            if (!is_array($taskIDList)) die(js::locate($this->createLink('task', 'index', ""), 'parent'));

            foreach ($taskIDList as $taskID) {
                //error_log("oscar: batchdeleteTask:$taskID");
                $task = $this->task->getById($taskID);
                $story = $this->dao->select('story')->from(TABLE_TASK)->where('id')->eq($taskID)->fetch('story');
                $this->task->delete(TABLE_TASK, $taskID);
                $this->task->computeWorkingHours($task->parent);
                if ($task->fromBug != 0) $this->dao->update(TABLE_BUG)->set('toTask')->eq(0)->where('id')->eq($task->fromBug)->exec();
                if ($story) $this->loadModel('story')->setStage($story);
                //die(js::locate($this->session->taskList, 'parent'));
            }
            if (!dao::isError()) $this->loadModel('score')->create('ajax', 'batchOther');
        }
        die(js::reload('parent'));
    }

    public function batchSetEstStartFromRealStart()
    {
        //error_log("===== batchSetEstStartFromRealStart");

        if ($this->post->taskIDList) {
            //error_log("===== batchSetEstStartFromRealStart");
            $taskIDList = $this->post->taskIDList;
            unset($_POST['taskIDList']);
            if (!is_array($taskIDList)) die(js::locate($this->createLink('task', 'index', ""), 'parent'));

            foreach ($taskIDList as $taskID) {
                $task = $this->task->getById($taskID);
                if($task->estStarted == '0000-00-00') {
                    $toDateTime = $task->realStarted;
                    if ($toDateTime == '0000-00-00') {
                        $toDateTime = helper::now();
                    }

                    $this->dao->update(TABLE_TASK)
                        ->set('estStarted')->eq($toDateTime)
                        ->where('id')->eq($taskID)->exec();
                }
            }
            if (!dao::isError()) $this->loadModel('score')->create('ajax', 'batchOther');
        }
        die(js::reload('parent'));
    }


    /**
     * AJAX: return tasks of a user in html select.
     *
     * @param  string $account
     * @param  string $id
     * @param  string $status
     * @access public
     * @return string
     */
    public function ajaxGetUserTasks($account = '', $id = '', $status = 'wait,doing')
    {
        if ($account == '') $account = $this->app->user->account;
        $tasks = $this->task->getUserTaskPairs($account, $status);

        if ($id) die(html::select("tasks[$id]", $tasks, '', 'class="form-control"'));
        die(html::select('task', $tasks, '', 'class=form-control'));
    }

    /**
     * AJAX: return project tasks in html select.
     *
     * @param  int $projectID
     * @param  int $taskID
     * @access public
     * @return string
     */
    public function ajaxGetProjectTasks($projectID, $taskID = 0)
    {
        $tasks = $this->task->getProjectTaskPairs((int)$projectID);
        die(html::select('task', empty($tasks) ? array('' => '') : $tasks, $taskID));
    }

    /**
     * AJAX: get the actions of a task. for web app.
     *
     * @param  int $taskID
     * @access public
     * @return void
     */
    public function ajaxGetDetail($taskID)
    {
        $this->view->actions = $this->loadModel('action')->getList('task', $taskID);
        $this->display();
    }

    /**
     * The report page.
     *
     * @param  int $projectID
     * @param  string $browseType
     * @access public
     * @return void
     */
    public function report($projectID, $browseType = 'all')
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
                //error_log("==== chart:" . $chart);
            }
        }

        $this->project->setMenu($this->project->getPairs(), $projectID);
        $this->projects = $this->project->getPairs();
        $this->view->title = $this->projects[$projectID] . $this->lang->colon . $this->lang->task->report->common;
        $this->view->position[] = $this->projects[$projectID];
        $this->view->position[] = $this->lang->task->report->common;
        $this->view->projectID = $projectID;
        $this->view->browseType = $browseType;
        $this->view->checkedCharts = $this->post->charts ? join(',', $this->post->charts) : '';

        $this->display();
    }

    /**
     * get data to export
     *
     * @param  int $projectID
     * @param  string $orderBy
     * @access public
     * @return void
     */
    public function export($projectID, $orderBy, $type)
    {
        $project = $this->project->getById($projectID);
        $allExportFields = $this->config->task->exportFields;
        if ($project->type == 'ops') $allExportFields = str_replace(' story,', '', $allExportFields);

        if ($_POST) {
            $this->loadModel('file');
            $taskLang = $this->lang->task;

            /* Create field lists. */
            $sort = $this->loadModel('common')->appendOrder($orderBy);
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $allExportFields);
            foreach ($fields as $key => $fieldName) {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($taskLang->$fieldName) ? $taskLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get tasks. */
            $tasks = array();
            if ($this->session->taskOnlyCondition) {
                $tasks = $this->dao->select('*')->from(TABLE_TASK)->alias('t1')->where($this->session->taskQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('t1.id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($sort)->fetchAll('id');

                foreach ($tasks as $key => $task) {
                    /* Compute task progress. */
                    if ($task->consumed == 0 and $task->left == 0) {
                        $task->progress = 0;
                    } elseif ($task->consumed != 0 and $task->left == 0) {
                        $task->progress = 100;
                    } else {
                        $task->progress = round($task->consumed / ($task->consumed + $task->left), 2) * 100;
                    }
                    $task->progress .= '%';
                }
            } else {
                $stmt = $this->dbh->query($this->session->taskQueryCondition . ($this->post->exportType == 'selected' ? " AND t1.id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while ($row = $stmt->fetch()) $tasks[$row->id] = $row;
            }

            /* Get users and projects. */
            $users = $this->loadModel('user')->getPairs('noletter');
            $projects = $this->loadModel('project')->getPairs('all|nocode');

            /* Get related objects id lists. */
            $relatedStoryIdList = array();
            foreach ($tasks as $task) $relatedStoryIdList[$task->story] = $task->story;

            /* Get team for multiple task. */
            $taskTeam = $this->dao->select('*')->from(TABLE_TEAM)->where('task')->in(array_keys($tasks))->fetchGroup('task');
            if (!empty($taskTeam)) {
                foreach ($taskTeam as $taskID => $team) $tasks[$taskID]->team = $team;
            }

            /* Get related objects title or names. */
            $relatedStories = $this->dao->select('id,title')->from(TABLE_STORY)->where('id')->in($relatedStoryIdList)->fetchPairs();
            $relatedFiles = $this->dao->select('id, objectID, pathname, title')->from(TABLE_FILE)->where('objectType')->eq('task')->andWhere('objectID')->in(@array_keys($tasks))->andWhere('extra')->ne('editor')->fetchGroup('objectID');
            $relatedModules = $this->loadModel('tree')->getTaskOptionMenu($projectID);

            $depts =  $this->loadModel('dept')->getOptionMenu();

            $children = $this->dao->select('*')->from(TABLE_TASK)->where('deleted')->eq(0)
                ->andWhere('parent')->in(array_keys($tasks))
                ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                ->orderBy($sort)
                ->fetchGroup('parent', 'id');
            if (!empty($children)) {
                foreach ($children as $parent => $childTasks) {
                    foreach ($childTasks as $task) {
                        /* Compute task progress. */
                        if ($task->consumed == 0 and $task->left == 0) {
                            $task->progress = 0;
                        } elseif ($task->consumed != 0 and $task->left == 0) {
                            $task->progress = 100;
                        } else {
                            $task->progress = round($task->consumed / ($task->consumed + $task->left), 2) * 100;
                        }
                        $task->progress .= '%';
                    }
                }

                $position = 0;
                foreach ($tasks as $task) {
                    $position++;
                    if (isset($children[$task->id])) {
                        array_splice($tasks, $position, 0, $children[$task->id]);
                        $position += count($children[$task->id]);
                    }
                }
            }

            if ($type == 'group') {
                $groupTasks = array();
                foreach ($tasks as $task) $groupTasks[$task->$orderBy][] = $task;
                $tasks = array();
                foreach ($groupTasks as $groupTask) {
                    foreach ($groupTask as $task) $tasks[] = $task;
                }
            }

            $newTasks = array();
            foreach ($tasks as $k => $task) {
                $hasChildren = false;
                foreach ($children as $p => $id) {
                    //error_log("oscar: hasChildren parent:$p = $task->id : $task->name");
                    if($p == $task->id)
                    {
                        $hasChildren = true;
                        break;
                    }
                }

                if(!$hasChildren)
                {
                    $newTasks[$task->id] = $task;
                }
            }

            $tasks = $newTasks;
            foreach ($tasks as $k => $task) {

                //error_log("oscar: ==== $k -> $task->name : $task->id");
                if ($this->post->fileType == 'csv') {
                    $task->desc = htmlspecialchars_decode($task->desc);
                    $task->desc = str_replace("<br />", "\n", $task->desc);
                    $task->desc = str_replace('"', '""', $task->desc);
                }

                /* fill some field with useful value. */
                //$task->story = isset($relatedStories[$task->story]) ? $relatedStories[$task->story] . "(#$task->story)" : '';
                //$task->dept = $depts[$task->dept] . "(#$task->dept)"; // oscar

                $task->story = isset($relatedStories[$task->story]) ? $relatedStories[$task->story] . "" : ''; // oscar
                $task->dept = $depts[$task->dept]; // oscar

                //if (isset($projects[$task->project])) $task->project = $projects[$task->project] . "(#$task->project)";
                if (isset($projects[$task->project])) $task->project = $projects[$task->project] . ""; // oscar

                if (isset($taskLang->typeList[$task->type])) $task->type = $taskLang->typeList[$task->type];
                if (isset($taskLang->priList[$task->pri])) $task->pri = $taskLang->priList[$task->pri];
                if (isset($taskLang->statusList[$task->status])) $task->status = $taskLang->statusList[$task->status];
                if (isset($taskLang->reasonList[$task->closedReason])) $task->closedReason = $taskLang->reasonList[$task->closedReason];
                //if (isset($relatedModules[$task->module])) $task->module = $relatedModules[$task->module] . "(#$task->module)";
                if (isset($relatedModules[$task->module])) $task->module = $relatedModules[$task->module] . ""; // oscar

                if (isset($users[$task->openedBy])) $task->openedBy = $users[$task->openedBy];
                if (isset($users[$task->assignedTo])) $task->assignedTo = $users[$task->assignedTo];
                if (isset($users[$task->checkBy])) $task->checkBy = $users[$task->checkBy]; // oscar
                if (isset($users[$task->finishedBy])) $task->finishedBy = $users[$task->finishedBy];
                if (isset($users[$task->canceledBy])) $task->canceledBy = $users[$task->canceledBy];
                if (isset($users[$task->closedBy])) $task->closedBy = $users[$task->closedBy];
                if (isset($users[$task->lastEditedBy])) $task->lastEditedBy = $users[$task->lastEditedBy];



                if (!empty($task->parent)) $task->name = '[' . $taskLang->childrenAB . '] ' . $task->name; // oscar

                if (!empty($task->team)) $task->name = '[' . $taskLang->multipleAB . '] ' . $task->name;

                $task->openedDate = substr($task->openedDate, 0, 10);
                $task->assignedDate = substr($task->assignedDate, 0, 10);
                $task->finishedDate = substr($task->finishedDate, 0, 10);
                $task->canceledDate = substr($task->canceledDate, 0, 10);
                $task->closedDate = substr($task->closedDate, 0, 10);
                $task->lastEditedDate = substr($task->lastEditedDate, 0, 10);

                // oscar[ pri is 0 should export 0 not null
                if($task->pri == 0)
                {
                    $task->pri = '0';
                }
                // oscar[

                /* Set related files. */
                if (isset($relatedFiles[$task->id])) {
                    $task->files = '';
                    foreach ($relatedFiles[$task->id] as $file) {
                        $fileURL = common::getSysURL() . $this->file->webPath . $this->file->getRealPathName($file->pathname);
                        $task->files .= html::a($fileURL, $file->title, '_blank') . '<br />';
                    }
                }
            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $tasks);
            $this->post->set('kind', 'task');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->allExportFields = $allExportFields;
        $this->view->customExport = true;
        $this->display();
    }

    /**
     * Ajax get task by ID.
     *
     * @param  int $taskID
     * @access public
     * @return void
     */
    public function ajaxGetByID($taskID)
    {
        $task = $this->dao->select('*')->from(TABLE_TASK)->where('id')->eq($taskID)->fetch();
        $realname = $this->dao->select('*')->from(TABLE_USER)->where('account')->eq($task->assignedTo)->fetch('realname');
        $task->assignedTo = $realname ? $realname : ($task->assignedTo == 'closed' ? 'Closed' : $task->assignedTo);
        if ($task->story) {
            $this->app->loadLang('story');
            $stage = $this->dao->select('*')->from(TABLE_STORY)->where('id')->eq($task->story)->andWhere('version')->eq($task->storyVersion)->fetch('stage');
            $task->storyStage = zget($this->lang->story->stageList, $stage);
        }
        die(json_encode($task));
    }

    public function ajaxGetBlueprintTasks($dept = 0, $milestone = 0)
    {
        $tasks = $this->task->ajaxGetBlueprintTasks($dept, $milestone);
        $depts = $this->dept->getOptionMenu(); //oscar:
        foreach($tasks as $dat)
        {
            if($milestone == 0) // return tasks
            {
                $dat->deptName = $depts[$dat->dept];
            }
            else {
                $dat->story->deptName = $depts[$dat->story->dept];

                foreach ($dat->tasks as $task) {
                    $task->deptName = $depts[$task->dept];
                }
            }
        }
        //$retTasks = new stdClass();
        //$retTasks->tasks = $tasks;
        //$retTasks->users = $this->user->getPairs('nodeleted|noclosed|noletter');
        die(json_encode($tasks));
    }

    // oscar[
    public function importTaskFromMSProject($projectID = 0, $storyID = 0, $iframe = 0, $taskID = 0, $createType = 'manualBatchCreate')
    {
        $project = $this->project->getById($projectID);
        $taskLink = $this->createLink('project', 'browse', "projectID=$projectID&tab=task");
        $storyLink = $this->session->storyList ? $this->session->storyList : $this->createLink('project', 'story', "projectID=$projectID");

        /* Set menu. */
        $this->project->setMenu($this->project->getPairs(), $project->id);

        if (!empty($_POST)) {

            //$this->dao->begin();

            $tasks = fixer::input('post')->get();
            $batchNum = count(reset($tasks));

            for($i = 0; $i < $batchNum; ++$i)
            {
                $taskId      = $tasks->id[$i];

                if($tasks->estimate[$i] == '0000-00-00'
                || $tasks->estimate[$i] == '')
                {
                    //$this->dao->rollBack();
                    die(js::error("任务[$taskId]预计开始日期不正确，请修正后再提交！"));
                }

                if($tasks->deadline[$i] == '0000-00-00'
                || $tasks->deadline[$i] == '')
                {
                    //$this->dao->rollBack();
                    die(js::error("任务[$taskId]截止日期不正确，请修正后再提交！"));
                }
            }

            for($i = 0; $i < $batchNum; ++$i)
            {
                $taskId      = $tasks->id[$i];

                $task             = new stdclass();
                $task->project      = $tasks->project[$i];
                $task->module      = $tasks->module[$i];
                $task->story      = $tasks->story[$i];
                $task->dept      = $tasks->dept[$i];
                $task->name      = str_replace('[' . $this->lang->task->childrenAB . '] ', '', $tasks->name[$i]);
                $task->pri      = $tasks->pri[$i];
                $task->estimate      = $tasks->estimate[$i];
                $task->estStarted      = $tasks->estStarted[$i];
                $task->deadline      = $tasks->deadline[$i];
                $task->assignedTo      = $tasks->assignedTo[$i];
                $task->checkBy      = $tasks->checkBy[$i];


                // oscar: find dept by assigned to account name
                $task->dept = $this->dept->getDeptIDFromAccount($task->assignedTo);

                $oldTask = $this->dao->select('*')->from(TABLE_TASK)->where('id')->eq((int)$taskId)->fetch();

                if($task->estStarted == '0000-00-00')
                {
                    //$this->dao->rollBack();
                    die(js::error("任务[$taskId]预计开始日期不正确，注意:这个任务之前的任务都已经成功更新或添加，如果是新建任务请删除此任务前的ID为0的行再重新粘贴,否则会重复创建！"));
                }

                if($task->deadline == '0000-00-00')
                {
                    //$this->dao->rollBack();
                    die(js::error("任务[$taskId]截止日期不正确，注意:这个任务之前的任务都已经成功更新或添加，如果是新建任务请删除此任务前的ID为0的行再重新粘贴,否则会重复创建！"));
                }

                //error_log("oscar: id:$taskId start:$task->estStarted deadline:$task->deadline");

                if(!empty($oldTask))
                {
                    $this->dao->update(TABLE_TASK)->data($task)
                        ->autoCheck()
                        ->batchCheckIF($task->status != 'cancel', $this->config->task->edit->requiredFields, 'notempty')
                        //->checkIF($task->estStarted != '0000-00-00', 'estStarted')
                        //->checkIF($task->deadline != '0000-00-00', 'deadline', 'ge', $task->estStarted)

                        ->checkIF($task->estimate != false, 'estimate', 'float')
                        ->checkIF($task->left     != false, 'left',     'float')
                        ->checkIF($task->consumed != false, 'consumed', 'float')
                        ->checkIF($task->status   != 'wait' and $task->left == 0 and $task->status != 'cancel' and $task->status != 'closed', 'status', 'equal', 'done')

                        ->batchCheckIF($task->status == 'wait' or $task->status == 'doing', 'finishedBy, finishedDate,canceledBy, canceledDate, closedBy, closedDate, closedReason', 'empty')

                        ->checkIF($task->status == 'done', 'consumed', 'notempty')
                        ->checkIF($task->status == 'done' and $task->closedReason, 'closedReason', 'equal', 'done')
                        ->batchCheckIF($task->status == 'done', 'canceledBy, canceledDate', 'empty')

                        ->checkIF($task->status == 'closed', 'closedReason', 'notempty')
                        ->batchCheckIF($task->closedReason == 'cancel', 'finishedBy, finishedDate', 'empty')
                        ->where('id')->eq((int)$taskId)
                        ->exec();

                    $this->task->computeWorkingHours($oldTask->parent);
                }
                else
                {
                    $task->openedBy = $this->app->user->account;
                    $task->openedDate = helper::now();

                    $this->dao->insert(TABLE_TASK)->data($task)
                        ->autoCheck()
                        ->batchCheck($this->config->task->create->requiredFields, 'notempty')
                        //->checkIF($task->estimate != '', 'estimate', 'float')
                        //->checkIF($task->estStarted != '0000-00-00', 'estStarted')
                        //->checkIF($task->deadline != '0000-00-00', 'deadline', 'ge', $task->estStarted)
                        //->check($task->deadline != '0000-00-00', 'deadline')
                        ->exec();
                }

                if (dao::isError()){
                    //$this->dao->rollBack();
                    die(js::error(dao::getError()));
                }
            }


            //$this->dao->commit();

            /* Locate the browser. */
            if ($iframe) die(js::reload('parent.parent'));
            die(js::locate($storyLink, 'parent'));
        }

        /* Set Custom*/
        foreach (explode(',', $this->config->task->importFields) as $field) {
            $customFields[$field] = $this->lang->task->$field;
        }

        $this->view->customFields = $customFields;
        $this->view->showFields = $this->config->task->exportFields;

        $stories = $this->story->getProjectStoryPairs($projectID, 0, 0, 0, 'titleonly');
        $members = $this->project->getTeamMemberPairs($projectID, 'nodeleted');
        $modules = $this->loadModel('tree')->getTaskOptionMenu($projectID);
        $title = $project->name . $this->lang->colon . $this->lang->task->batchCreate;
        $position[] = html::a($taskLink, $project->name);
        $position[] = $this->lang->task->common;
        $position[] = $this->lang->task->batchCreate;

        $this->view->title = $title;
        $this->view->position = $position;
        $this->view->project = $project;
        $this->view->stories = $stories;
        $this->view->modules = $modules;
        $this->view->parent = $taskID;
        $this->view->parentTask = $this->task->getByID($taskID); // oscar
        $this->view->storyID = $storyID;
        $this->view->story = $this->story->getByID($storyID);
        $this->view->storyTasks = $this->task->getStoryTaskCounts(array_keys($stories), $projectID);
        $this->view->members = $members;

        $projects = $this->loadModel('project')->getPairs('nocode');
        $this->view->projects = $projects;

        $this->dept->setupDeptUsers($this->view, $this->app->user->account, $this->app->user->dept);
        $this->loadModel('user');
        $this->view->deptUsers = $this->user->getPairs('nodeleted|noclosed|noletter');

        $this->display();
    }

    public function checkByGD($taskID)
    {
        $this->commonAction($taskID);

        //if (!empty($_POST))
        {
            $this->loadModel('action');
            $changes = $this->task->checkBy($taskID);
            if (dao::isError()) die(js::error(dao::getError()));
            //$files = $this->loadModel('file')->saveUpload('task', $taskID);

            $task = $this->task->getById($taskID);
            //if ($this->post->comment != '' or !empty($changes))
            if (!empty($changes))
            {
                $fileAction = !empty($files) ? $this->lang->addFiles . join(',', $files) . "\n" : '';
                $actionID = $this->action->create('task', $taskID, 'Finished', $fileAction);
                $this->action->logHistory($actionID, $changes);
                $this->task->sendmail($taskID, $actionID);
            }

//            if ($this->task->needUpdateBugStatus($task)) {
//                foreach ($changes as $change) {
//                    if ($change['field'] == 'status') {
//                        $confirmURL = $this->createLink('bug', 'view', "id=$task->fromBug");
//                        unset($_GET['onlybody']);
//                        $cancelURL = $this->createLink('task', 'view', "taskID=$taskID");
//                        die(js::confirm(sprintf($this->lang->task->remindBug, $task->fromBug), $confirmURL, $cancelURL, 'parent', 'parent.parent'));
//                    }
//                }
//            }
            if (isonlybody()) die(js::closeModal('parent.parent', 'this'));
            die(js::locate($this->createLink('task', 'view', "taskID=$taskID"), 'parent'));
        }

        $task = $this->view->task;
        $members = $this->loadModel('user')->getPairs('noletter');

        $this->view->users = $members;
//        if (!empty($task->team)) {
//            $teams = array_keys($task->team);
//
//            $task->nextBy = $this->task->getNextUser($teams, $task->assignedTo);
//            $task->myConsumed = $this->dao->select('consumed')->from(TABLE_TEAM)->where('task')->eq($taskID)->andWhere('account')->eq($task->assignedTo)->fetch('consumed');
//
//            $lastAccount = end($teams);
//            if ($lastAccount != $task->assignedTo) {
//                $members = $this->task->getMemberPairs($task);
//            } else {
//                $task->nextBy = $task->openedBy;
//            }
//        }

        die(js::reload('parent'));
    }

    public function uncheckByGD($taskID)
    {
        $this->commonAction($taskID);

        //if (!empty($_POST))
        {
            $this->loadModel('action');
            $changes = $this->task->uncheckBy($taskID);
            if (dao::isError()) die(js::error(dao::getError()));
            //$files = $this->loadModel('file')->saveUpload('task', $taskID);

            $task = $this->task->getById($taskID);
            //if ($this->post->comment != '' or !empty($changes))
            if (!empty($changes))
            {
                $fileAction = !empty($files) ? $this->lang->addFiles . join(',', $files) . "\n" : '';
                $actionID = $this->action->create('task', $taskID, 'Finished', $fileAction);
                $this->action->logHistory($actionID, $changes);
                $this->task->sendmail($taskID, $actionID);
            }

            if ($this->task->needUpdateBugStatus($task)) {
                foreach ($changes as $change) {
                    if ($change['field'] == 'status') {
                        $confirmURL = $this->createLink('bug', 'view', "id=$task->fromBug");
                        unset($_GET['onlybody']);
                        $cancelURL = $this->createLink('task', 'view', "taskID=$taskID");
                        die(js::confirm(sprintf($this->lang->task->remindBug, $task->fromBug), $confirmURL, $cancelURL, 'parent', 'parent.parent'));
                    }
                }
            }
            if (isonlybody()) die(js::closeModal('parent.parent', 'this'));
            die(js::locate($this->createLink('task', 'view', "taskID=$taskID"), 'parent'));
        }

        $task = $this->view->task;
        $members = $this->loadModel('user')->getPairs('noletter');

        $this->view->users = $members;
//        if (!empty($task->team)) {
//            $teams = array_keys($task->team);
//
//            $task->nextBy = $this->task->getNextUser($teams, $task->assignedTo);
//            $task->myConsumed = $this->dao->select('consumed')->from(TABLE_TEAM)->where('task')->eq($taskID)->andWhere('account')->eq($task->assignedTo)->fetch('consumed');
//
//            $lastAccount = end($teams);
//            if ($lastAccount != $task->assignedTo) {
//                $members = $this->task->getMemberPairs($task);
//            } else {
//                $task->nextBy = $task->openedBy;
//            }
//        }

        die(js::reload('parent'));
    }

    /*
    public function ajaxShowTask($taskId)
    {
        //error_log("task.control.ajaxShowTask:" . $taskId);
        $lnk = helper::createLink('task', 'view', "taskID=$taskId");
        die($lnk);
    }
    //*/
    // oscar]
}
