<?php

/**
 * The control file of project module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     project
 * @version     $Id: control.php 5094 2013-07-10 08:46:15Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class project extends control
{
    public $projects;

    /**
     * Construct function, Set projects.
     *
     * @access public
     * @return void
     */
    public function __construct($moduleName = '', $methodName = '')
    {
        parent::__construct($moduleName, $methodName);
        if ($this->methodName != 'computeburn') {
            $this->projects = $this->project->getPairs('nocode');
            if (!$this->projects and $this->methodName != 'index' and $this->methodName != 'create' and $this->app->getViewType() != 'mhtml') $this->locate($this->createLink('project', 'create'));
        }

        //unset($this->lang->task->deptList);

        /*
        error_log("task model construct get depts =======================");
        $deptsTemp =$this->dao->select("id,name")->from(TABLE_DEPT)->fetchPairs('id');

        error_log("task model construct =======================");
        foreach ($this->lang->task->deptList as $k => $v)
        {
            error_log("deptList OLD: $k = $v");
        }

        error_log("task model construct setup depts from DB ======================= $deptsTemp");
        foreach ($deptsTemp as $k2 => $v2) {
            $this->lang->task->deptList[$k2] = $v2;
            error_log("deptList NEW: $k2 = $v2");
        }

        error_log("task model construct =======================");
        foreach ($this->lang->task->deptList as $k => $v)
        {
            error_log("deptList OLD: $k = $v");
        }
        //*/
    }

    /**
     * The index page.
     *
     * @param  string $locate yes|no locate to the browse page or not.
     * @param  string $status the projects status, if locate is no, then get projects by the $status.
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function index($locate = 'auto', $projectID = 0)
    {
        if ($this->app->user->account == 'guest' or commonModel::isTutorialMode()) $this->config->project->homepage = 'index';
        if (!isset($this->config->project->homepage)) {
            if ($this->projects and $this->app->viewType != 'mhtml') die($this->fetch('custom', 'ajaxSetHomepage', "module=project"));

            $this->config->project->homepage = 'index';
            $this->fetch('custom', 'ajaxSetHomepage', "module=project&page=index");
        }

        $homepage = $this->config->project->homepage;
        if ($homepage == 'browse' and $locate == 'auto') $locate = 'yes';
        if ($locate == 'yes') $this->locate($this->createLink('project', 'task'));

        if ($this->app->viewType != 'mhtml') unset($this->lang->project->menu->index);
        $this->commonAction($projectID);
        //$this->project->setMenu($this->projects, key($this->projects));

        $this->view->title = $this->lang->project->index;
        $this->view->position[] = $this->lang->project->index;

        $this->display();
    }

    /**
     * Browse a project.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function browse($projectID = 0)
    {
        $this->locate($this->createLink($this->moduleName, 'task', "projectID=$projectID"));
    }

    /**
     * Common actions.
     *
     * @param  int $projectID
     * @access public
     * @return object current object
     */
    public function commonAction($projectID = 0, $extra = '')
    {
        $this->loadModel('product');

        /* Get projects and products info. */
        $projectID = $this->project->saveState($projectID, $this->projects);
        $project = $this->project->getById($projectID);
        $products = $this->project->getProducts($projectID);
        $childProjects = $this->project->getChildProjects($projectID);
        $teamMembers = $this->project->getTeamMembers($projectID);
        $actions = $this->loadModel('action')->getList('project', $projectID);

        /* Set menu. */
        $this->project->setMenu($this->projects, $projectID, $extra);

        /* Assign. */
        $this->view->projects = $this->projects;
        $this->view->project = $project;
        $this->view->childProjects = $childProjects;
        $this->view->products = $products;
        $this->view->teamMembers = $teamMembers;

        return $project;
    }

    /**
     * Tasks of a project.
     *
     * @param  int $projectID
     * @param  string $status
     * @param  string $orderBy
     * @param  int $recTotal
     * @param  int $recPerPage
     * @param  int $pageID
     * @access public
     * @return void
     */
    public function task($projectID = 0, $status = 'unclosed', $param = 0, $moduleType='byMilestone', $orderBy = '', $recTotal = 0, $recPerPage = 100, $pageID = 1)
    {
        //error_log("------------------: project-task: status:$status param:$param moduleType:$moduleType");

        $this->loadModel('tree');
        $this->loadModel('search');
        $this->loadModel('task');
        $this->loadModel('datatable');

        $this->project->getLimitedProject();


        /* Set browse type. */
        $browseType = strtolower($status);
        if ($this->config->global->flow == 'onlyTask' and $browseType == 'byproduct') $param = 0;



        // oscar

        //var_dump($param);

        $milestone = $param;
        if($milestone == 'myQueryID') {
            $milestone = $this->app->session->milestone;
            //error_log("GET milestone from session <<< :$milestone");
        }

        $milestones = $this->project->getMilestonesPairs($projectID);
        $this->view->milestones = $milestones;
        $this->view->milestone = $milestone;
        // oscar

        /* Get products by project. */
        $project = $this->commonAction($projectID, $status);
        $projectID = $project->id;
        $products = $this->config->global->flow == 'onlyTask' ? array() : $this->loadModel('product')->getProductsByProject($projectID);
        setcookie('preProjectID', $projectID, $this->config->cookieLife, $this->config->webRoot);

        if ($this->cookie->preProjectID != $projectID) {
            $_COOKIE['moduleBrowseParam'] = $_COOKIE['productBrowseParam'] = 0;
            setcookie('moduleBrowseParam', 0, $this->config->cookieLife, $this->config->webRoot);
            setcookie('productBrowseParam', 0, $this->config->cookieLife, $this->config->webRoot);
        }
        if ($browseType == 'bymodule') {
            setcookie('moduleBrowseParam', (int)$param, $this->config->cookieLife, $this->config->webRoot);
            setcookie('productBrowseParam', 0, $this->config->cookieLife, $this->config->webRoot);
        } elseif ($browseType == 'byproduct') {
            setcookie('moduleBrowseParam', 0, $this->config->cookieLife, $this->config->webRoot);
            setcookie('productBrowseParam', (int)$param, $this->config->cookieLife, $this->config->webRoot);
        } else {
            $this->session->set('taskBrowseType', $browseType);
        }

        /* Set queryID, moduleID and productID. */
        $queryID = ($browseType == 'bysearch') ? (int)$param : 0;
        //oscar$moduleID = ($browseType == 'bymodule') ? (int)$param : (($browseType == 'bysearch' or $browseType == 'byproduct') ? 0 : $this->cookie->moduleBrowseParam);
        $productID = ($browseType == 'byproduct') ? (int)$param : (($browseType == 'bysearch' or $browseType == 'bymodule') ? 0 : $this->cookie->productBrowseParam);

        // oscar[
        $moduleID = 0;
        if($browseType != 'bymodule')
        {
            //$moduleID = 0;
        }

        if($milestone != 'myQueryID')
        {
            $moduleID = $milestone;
            $this->app->session->set('milestone', $milestone);
            //error_log("SET milestone to session >>> :$milestone");
        }

        // oscar]

        /* Save to session. */
        $uri = $this->app->getURI(true);
        $this->app->session->set('taskList', $uri);
        $this->app->session->set('storyList', $uri);
        $this->app->session->set('projectList', $uri);

        /* Process the order by field. */
        if (!$orderBy) $orderBy = $this->cookie->projectTaskOrder ? $this->cookie->projectTaskOrder : 'status,id_desc';
        setcookie('projectTaskOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);

        /* Append id for secend sort. */
        $sort = $this->loadModel('common')->appendOrder($orderBy);

        /* Header and position. */
        $this->view->title = $project->name . $this->lang->colon . $this->lang->project->task;
        $this->view->position[] = html::a($this->createLink('project', 'browse', "projectID=$projectID"), $project->name);
        $this->view->position[] = $this->lang->project->task;

        /* Load pager and get tasks. */
        $this->app->loadClass('pager', $static = true);
        if ($this->app->getViewType() == 'mhtml') $recPerPage = 10;
        $pager = new pager($recTotal, $recPerPage, $pageID);

        // oscar
        //if($status == 'milestone')
        //{
            //$queryID = $param;
        //}
        // oscar

        /* Get tasks. */
        $tasks = $this->project->getTasks($productID, $projectID, $this->projects, $browseType, $queryID, $moduleType, $moduleID, $sort, $pager);

        /*
        error_log("get-project-tasks:");
        foreach (array_keys($tasks) as $v) {
            error_log("oscar task:" . $v->name);
        }
        //*/

        /* Build the search form. */
        $actionURL = $this->createLink('project', 'task', "projectID=$projectID&status=bySearch&param=myQueryID");
        $this->config->project->search['onMenuBar'] = 'yes';
        $this->project->buildTaskSearchForm($projectID, $this->projects, $queryID, $actionURL);

        /* team member pairs. */
        $memberPairs = array();
        foreach ($this->view->teamMembers as $key => $member) $memberPairs[$key] = $member->realname;


        $showModule = !empty($this->config->datatable->projectTask->showModule) ? $this->config->datatable->projectTask->showModule : '';
        $this->view->modulePairs = $showModule ? $this->tree->getModulePairs($projectID, 'task', $showModule) : array();

        /* Assign. */
        $this->view->tasks = $tasks;
        $this->view->summary = $this->project->summary($tasks);
        $this->view->tabID = 'task';
        $this->view->pager = $pager;
        $this->view->recTotal = $pager->recTotal;
        $this->view->recPerPage = $pager->recPerPage;
        $this->view->orderBy = $orderBy;
        $this->view->browseType = $browseType;
        $this->view->status = $status;
        $this->view->users = $this->loadModel('user')->getPairs('noletter');
        $this->view->param = $param;
        $this->view->projectID = $projectID;
        $this->view->project = $project;
        $this->view->productID = $productID;
        $this->view->modules = $this->tree->getTaskOptionMenu($projectID);
        $this->view->moduleID = $moduleID;
        $this->view->moduleTree = $this->tree->getTaskTreeMenu($projectID, $productID = 0, $startModuleID = 0, array('treeModel', 'createTaskLink'));
        //$this->view->moduleTree = $this->loadModel('tree')->getTreeMenu($productID, $viewType = 'story', $startModuleID = 0, array('treeModel', 'createStoryLink'), '', 0);
        $this->view->projectTree = $this->project->tree();
        $this->view->memberPairs = $memberPairs;
        $this->view->branchGroups = $this->loadModel('branch')->getByProducts(array_keys($products), 'noempty');
        $this->view->setShowModule = true;

        //oscar:
        $this->loadModel('dept');
        $this->dept->setupDeptUsers($this->view, $this->app->user->account, $this->app->user->dept);
        $this->dept->setupDeptWithUsers($this->view);

        $this->loadModel('pipeline');
        $this->pipeline->setupOptionMenu($this->view);

        $versions = $this->dao->select('id, name')->from(TABLE_TASKMILESTONE)
            ->where('project')->eq($projectID)
            ->andWhere('active')->eq(1)
            ->orderBy('id desc')
            //->fetchAll();
            ->fetchPairs('id');

        $this->view->versions = ($versions);

        /*
        error_log("==================== version ====================");
        foreach ($versions as $k => $v) {
            error_log("version: $k -> $v");
        }
        //*/
        //oscar:

        $this->display();
    }

    /**
     * Browse tasks in group.
     *
     * @param  int $projectID
     * @param  string $groupBy the field to group by
     * @access public
     * @return void
     */
    public function grouptask($projectID = 0, $groupBy = 'story', $filter = '')
    {
        $project = $this->commonAction($projectID);
        $projectID = $project->id;

        /* Save session. */
        $this->app->session->set('taskList', $this->app->getURI(true));
        $this->app->session->set('storyList', $this->app->getURI(true));

        /* Header and session. */
        $this->view->title = $project->name . $this->lang->colon . $this->lang->project->task;
        $this->view->position[] = html::a($this->createLink('project', 'browse', "projectID=$projectID"), $project->name);
        $this->view->position[] = $this->lang->project->task;

        /* Get tasks and group them. */
        if (empty($groupBy)) $groupBy = 'story';
        $sort = $this->loadModel('common')->appendOrder($groupBy);
        $tasks = $this->loadModel('task')->getProjectTasks($projectID, $productID = 0, $status = 'all', $modules = 0, $sort);
        $groupBy = str_replace('`', '', $groupBy);
        $taskLang = $this->lang->task;
        $groupByList = array();
        $groupTasks = array();

        $groupTasks = array();
        foreach ($tasks as $task) {
            $groupTasks[] = $task;
            if (isset($task->children)) {
                foreach ($task->children as $child) $groupTasks[] = $child;
                $task->children = true;
                unset($task->children);
            }
        }

        /* Get users. */
        $users = $this->loadModel('user')->getPairs('noletter');
        $tasks = $groupTasks;
        $groupTasks = array();
        foreach ($tasks as $task) {
            if ($groupBy == 'story') {
                $groupTasks[$task->story][] = $task;
                $groupByList[$task->story] = $task->storyTitle;
            } elseif ($groupBy == 'status') {
                $groupTasks[$taskLang->statusList[$task->status]][] = $task;
            } elseif ($groupBy == 'assignedTo') {
                $groupTasks[$task->assignedToRealName][] = $task;
            } elseif ($groupBy == 'finishedBy') {
                $groupTasks[$users[$task->finishedBy]][] = $task;
            } elseif ($groupBy == 'closedBy') {
                $groupTasks[$users[$task->closedBy]][] = $task;
            } elseif ($groupBy == 'type') {
                $groupTasks[$taskLang->typeList[$task->type]][] = $task;
            } else {
                $groupTasks[$task->$groupBy][] = $task;
            }
        }
        /* Process closed data when group by assignedTo. */
        if ($groupBy == 'assignedTo' and isset($groupTasks['Closed'])) {
            $closedTasks = $groupTasks['Closed'];
            unset($groupTasks['Closed']);
            $groupTasks['closed'] = $closedTasks;
        }

        /* Assign. */
        $this->app->loadLang('tree');
        $this->view->members = $this->project->getTeamMembers($projectID);
        $this->view->tasks = $groupTasks;
        $this->view->tabID = 'task';
        $this->view->groupByList = $groupByList;
        $this->view->browseType = 'group';
        $this->view->groupBy = $groupBy;
        $this->view->orderBy = $groupBy;
        $this->view->projectID = $projectID;
        $this->view->users = $users;
        $this->view->moduleID = 0;
        $this->view->moduleName = $this->lang->tree->all;
        $this->view->filter = $filter;
        $this->display();
    }

    /**
     * Import tasks undoned from other projects.
     *
     * @param  int $projectID
     * @param  int $fromProject
     * @access public
     * @return void
     */
    public function importTask($toProject, $fromProject = 0)
    {
        if (!empty($_POST)) {
            $this->project->importTask($toProject, $fromProject);
            die(js::locate(inlink('importTask', "toProject=$toProject&fromProject=$fromProject"), 'parent'));
        }

        $project = $this->commonAction($toProject);
        $toProject = $project->id;
        $branches = $this->project->getProjectBranches($toProject);
        $tasks = $this->project->getTasks2Imported($toProject, $branches);
        $projects = $this->project->getProjectsToImport(array_keys($tasks));
        unset($projects[$toProject]);
        unset($tasks[$toProject]);

        if ($fromProject == 0) {
            $tasks2Imported = array();
            foreach ($projects as $id => $projectName) {
                $tasks2Imported = array_merge($tasks2Imported, $tasks[$id]);
            }
        } else {
            $tasks2Imported = $tasks[$fromProject];
        }

        /* Save session. */
        $this->app->session->set('taskList', $this->app->getURI(true));
        $this->app->session->set('storyList', $this->app->getURI(true));

        $this->view->title = $project->name . $this->lang->colon . $this->lang->project->importTask;
        $this->view->position[] = html::a(inlink('browse', "projectID=$toProject"), $project->name);
        $this->view->position[] = $this->lang->project->importTask;
        $this->view->tasks2Imported = $tasks2Imported;
        $this->view->projects = $projects;
        $this->view->projectID = $project->id;
        $this->view->fromProject = $fromProject;
        $this->display();
    }

    /**
     * Import from Bug.
     *
     * @param  int $projectID
     * @param  string $orderBy
     * @param  int $recTotal
     * @param  int $recPerPage
     * @param  int $pageID
     * @access public
     * @return void
     */
    public function importBug($projectID = 0, $browseType = 'all', $param = 0, $recTotal = 0, $recPerPage = 30, $pageID = 1)
    {
        if (!empty($_POST)) {
            $mails = $this->project->importBug($projectID);
            if (dao::isError()) die(js::error(dao::getError()));

            $this->loadModel('task');
            foreach ($mails as $mail) $this->task->sendmail($mail->taskID, $mail->actionID);

            die(js::locate($this->createLink('project', 'importBug', "projectID=$projectID"), 'parent'));
        }

        /* Set browseType, productID, moduleID and queryID. */
        $browseType = strtolower($browseType);
        $queryID = ($browseType == 'bysearch') ? (int)$param : 0;

        /* Save to session. */
        $uri = $this->app->getURI(true);
        $this->app->session->set('bugList', $uri);
        $this->app->session->set('storyList', $uri);
        $this->app->session->set('projectList', $uri);

        $this->loadModel('bug');
        $projects = $this->project->getPairs('nocode');
        $this->project->setMenu($projects, $projectID);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $title = $projects[$projectID] . $this->lang->colon . $this->lang->project->importBug;
        $position[] = html::a($this->createLink('project', 'task', "projectID=$projectID"), $projects[$projectID]);
        $position[] = $this->lang->project->importBug;

        /* Get users, products and projects.*/
        $users = $this->project->getTeamMemberPairs($projectID, 'nodeleted');
        $products = $this->dao->select('t1.product, t2.name')->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PRODUCT)->alias('t2')
            ->on('t1.product = t2.id')
            ->where('t1.project')->eq($projectID)
            ->fetchPairs('product');
        if (!empty($products)) {
            unset($projects);
            $projects = $this->dao->select('t1.project, t2.name')->from(TABLE_PROJECTPRODUCT)->alias('t1')
                ->leftJoin(TABLE_PROJECT)->alias('t2')
                ->on('t1.project = t2.id')
                ->where('t1.product')->in(array_keys($products))
                ->fetchPairs('project');
        } else {
            $projectName = $projects[$projectID];
            unset($projects);
            $projects[$projectID] = $projectName;
        }

        /* Get bugs.*/
        $bugs = array();
        if ($browseType != "bysearch") {
            $bugs = $this->bug->getActiveAndPostponedBugs(array_keys($products), $projectID, $pager);
        } else {
            if ($queryID) {
                $query = $this->loadModel('search')->getQuery($queryID);
                if ($query) {
                    $this->session->set('importBugQuery', $query->sql);
                    $this->session->set('importBugForm', $query->form);
                } else {
                    $this->session->set('importBugQuery', ' 1 = 1');
                }
            } else {
                if ($this->session->importBugQuery == false) $this->session->set('importBugQuery', ' 1 = 1');
            }
            $bugQuery = str_replace("`product` = 'all'", "`product`" . helper::dbIN(array_keys($products)), $this->session->importBugQuery); // Search all project.
            $bugs = $this->project->getSearchBugs($products, $projectID, $bugQuery, $pager, 'id_desc');
        }

        /* Build the search form. */
        $this->config->bug->search['actionURL'] = $this->createLink('project', 'importBug', "projectID=$projectID&browseType=bySearch&param=myQueryID");
        $this->config->bug->search['queryID'] = $queryID;
        if (!empty($products)) {
            $this->config->bug->search['params']['product']['values'] = array('' => '') + $products + array('all' => $this->lang->project->aboveAllProduct);
        } else {
            $this->config->bug->search['params']['product']['values'] = array('' => '');
        }
        $this->config->bug->search['params']['project']['values'] = array('' => '') + $projects + array('all' => $this->lang->project->aboveAllProject);
        $this->config->bug->search['params']['plan']['values'] = $this->loadModel('productplan')->getPairs(array_keys($products));
        $this->config->bug->search['module'] = 'importBug';
        $this->config->bug->search['params']['confirmed']['values'] = array('' => '') + $this->lang->bug->confirmedList;
        $this->config->bug->search['params']['module']['values'] = $this->loadModel('tree')->getOptionMenu($projectID, $viewType = 'bug', $startModuleID = 0);
        unset($this->config->bug->search['fields']['resolvedBy']);
        unset($this->config->bug->search['fields']['closedBy']);
        unset($this->config->bug->search['fields']['status']);
        unset($this->config->bug->search['fields']['toTask']);
        unset($this->config->bug->search['fields']['toStory']);
        unset($this->config->bug->search['fields']['severity']);
        unset($this->config->bug->search['fields']['resolution']);
        unset($this->config->bug->search['fields']['resolvedBuild']);
        unset($this->config->bug->search['fields']['resolvedDate']);
        unset($this->config->bug->search['fields']['closedDate']);
        unset($this->config->bug->search['fields']['branch']);
        unset($this->config->bug->search['params']['resolvedBy']);
        unset($this->config->bug->search['params']['closedBy']);
        unset($this->config->bug->search['params']['status']);
        unset($this->config->bug->search['params']['toTask']);
        unset($this->config->bug->search['params']['toStory']);
        unset($this->config->bug->search['params']['severity']);
        unset($this->config->bug->search['params']['resolution']);
        unset($this->config->bug->search['params']['resolvedBuild']);
        unset($this->config->bug->search['params']['resolvedDate']);
        unset($this->config->bug->search['params']['closedDate']);
        unset($this->config->bug->search['params']['branch']);
        $this->loadModel('search')->setSearchParams($this->config->bug->search);

        /* Assign. */
        $this->view->title = $title;
        $this->view->position = $position;
        $this->view->pager = $pager;
        $this->view->bugs = $bugs;
        $this->view->recTotal = $pager->recTotal;
        $this->view->recPerPage = $pager->recPerPage;
        $this->view->browseType = $browseType;
        $this->view->param = $param;
        $this->view->users = $users;
        $this->view->project = $this->project->getByID($projectID);
        $this->view->projectID = $projectID;
        $this->display();
    }

    /**
     * Browse stories of a project.
     *
     * @param  int $projectID
     * @param  string $orderBy
     * @access public
     * @return void
     */
    public function story($projectID = 0, $orderBy = 'id_desc', $type = 'byModule', $param = 0, $recTotal = 0, $recPerPage = 50, $pageID = 1)
    {
        /* Load these models. */
        $this->loadModel('story');
        $this->loadModel('user');
        $this->app->loadLang('testcase');

        $this->project->getLimitedProject();

        /* Save session. */
        $this->app->session->set('storyList', $this->app->getURI(true));

        /* Process the order by field. */
        if (!$orderBy) $orderBy = $this->cookie->projectStoryOrder ? $this->cookie->projectStoryOrder : 'pri';
        setcookie('projectStoryOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);

        /* Append id for secend sort. */
        $sort = $this->loadModel('common')->appendOrder($orderBy);

        $queryID = ($type == 'bySearch') ? (int)$param : 0;
        $project = $this->commonAction($projectID);
        $projectID = $project->id;

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $stories = $this->story->getProjectStories($projectID, $sort, $type, $param, $pager);
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'story', true); // oscar: export bug
        $users = $this->user->getPairs('noletter');

        /* Get project's product. */
        $productID = 0;
        $productPairs = $this->loadModel('product')->getProductsByProject($projectID);
        if ($productPairs) $productID = key($productPairs);

        /* Build the search form. */
        $modules = array();
        $projectModules = $this->loadModel('tree')->getTaskTreeModules($projectID, true);
        $products = $this->project->getProducts($projectID);
        foreach ($products as $product) {
            $productModules = $this->tree->getOptionMenu($product->id);
            foreach ($productModules as $moduleID => $moduleName) {
                if ($moduleID and !isset($projectModules[$moduleID])) continue;
                $modules[$moduleID] = ((count($products) >= 2 and $moduleID) ? $product->name : '') . $moduleName;
            }
        }
        $actionURL = $this->createLink('project', 'story', "projectID=$projectID&orderBy=$orderBy&type=bySearch&queryID=myQueryID");
        $branchGroups = $this->loadModel('branch')->getByProducts(array_keys($products), 'noempty');
        $this->project->buildStorySearchForm($products, $branchGroups, $modules, $queryID, $actionURL, 'projectStory');

        /* Header and position. */
        $title = $project->name . $this->lang->colon . $this->lang->project->story;
        $position[] = html::a($this->createLink('project', 'browse', "projectID=$projectID"), $project->name);
        $position[] = $this->lang->project->story;

        /* Count T B C */
        $storyIdList = array_keys($stories);

        $storyTasks = $this->loadModel('task')->getStoryTaskCounts($storyIdList, $projectID);
        $storyBugs = $this->loadModel('bug')->getStoryBugCounts($storyIdList, $projectID);
        $storyCases = $this->loadModel('testcase')->getStoryCaseCounts($storyIdList);

        /* Assign. */
        $this->view->title = $title;
        $this->view->position = $position;
        $this->view->productID = $productID;
        $this->view->project = $project;
        $this->view->stories = $stories;
        $this->view->summary = $this->product->summary($stories);
        $this->view->orderBy = $orderBy;
        $this->view->type = $type;
        $this->view->param = $param;
        $this->view->moduleTree = $this->loadModel('tree')->getProjectStoryTreeMenu($projectID, $startModuleID = 0, array('treeModel', 'createProjectStoryLink'));
        //$this->view->moduleTree = $this->loadModel('tree')->getTreeMenu($productID, $viewType = 'story', $startModuleID = 0, array('treeModel', 'createStoryLink'), '', $branch);
        $this->view->tabID = 'story';
        $this->view->storyTasks = $storyTasks;
        $this->view->storyBugs = $storyBugs;
        $this->view->storyCases = $storyCases;
        $this->view->users = $users;
        $this->view->pager = $pager;
        $this->view->branchGroups = $branchGroups;

        //oscar:
        $this->loadModel('dept');
        $this->dept->setupDeptUsers($this->view, $this->app->user->account, $this->app->user->dept);
        $this->dept->setupDeptWithUsers($this->view);

        $this->loadModel('pipeline');
        $this->pipeline->setupOptionMenu($this->view);
        //oscar:

        //error_log("project-story  orderBy:" . $orderBy);

        $this->display();
    }

    /**
     * Browse bugs of a project.
     *
     * @param  int $projectID
     * @param  string $orderBy
     * @param  int $build
     * @param  string $type
     * @param  int $param
     * @param  int $recTotal
     * @param  int $recPerPage
     * @param  int $pageID
     * @access public
     * @return void
     */
    public function bug($projectID = 0, $orderBy = 'status,id_desc', $build = 0, $type = '', $param = 0, $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Load these two models. */
        $this->loadModel('bug');
        $this->loadModel('user');

        /* Save session. */
        $this->session->set('bugList', $this->app->getURI(true));

        $queryID = ($type == 'bySearch') ? (int)$param : 0;
        $project = $this->commonAction($projectID);
        $projectID = $project->id;
        $products = $this->project->getProducts($project->id);
        $productID = key($products);    // Get the first product for creating bug.
        $branchID = isset($products[$productID]) ? $products[$productID]->branch : 0;

        /* Header and position. */
        $title = $project->name . $this->lang->colon . $this->lang->project->bug;
        $position[] = html::a($this->createLink('project', 'browse', "projectID=$projectID"), $project->name);
        $position[] = $this->lang->project->bug;

        /* Load pager and get bugs, user. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $sort = $this->loadModel('common')->appendOrder($orderBy);
        $bugs = $this->bug->getProjectBugs($projectID, $build, $type, $param, $sort, $pager);
        $users = $this->user->getPairs('noletter');

        /* team member pairs. */
        $memberPairs = array();
        $memberPairs[] = "";
        foreach ($this->view->teamMembers as $key => $member) {
            $memberPairs[$key] = $member->realname;
        }

        /* Build the search form. */
        $actionURL = $this->createLink('project', 'bug', "projectID=$projectID&orderBy=$orderBy&build=$build&type=bySearch&queryID=myQueryID");
        $this->project->buildBugSearchForm($products, $queryID, $actionURL);

        /* Assign. */
        $this->view->title = $title;
        $this->view->position = $position;
        $this->view->bugs = $bugs;
        $this->view->tabID = 'bug';
        $this->view->build = $this->loadModel('buildex')->getById($build);
        $this->view->buildID = $this->view->build ? $this->view->build->id : 0;
        $this->view->pager = $pager;
        $this->view->orderBy = $orderBy;
        $this->view->users = $users;
        $this->view->productID = $productID;
        $this->view->branchID = empty($this->view->build->branch) ? $branchID : $this->view->build->branch;
        $this->view->memberPairs = $memberPairs;
        $this->view->type = $type;
        $this->view->param = $param;

        $this->display();
    }

    /**
     * Browse builds of a project.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function build($projectID = 0)
    {
        $this->loadModel('testtask');
        $this->session->set('buildList', $this->app->getURI(true));

        $project = $this->commonAction($projectID);
        $projectID = $project->id;

        /* Header and position. */
        $this->view->title = $project->name . $this->lang->colon . $this->lang->project->build;
        $this->view->position[] = html::a(inlink('browse', "projectID=$projectID"), $project->name);
        $this->view->position[] = $this->lang->project->build;

        /* Get builds. */
        $this->view->builds = $this->loadModel('buildex')->getProjectBuilds((int)$projectID);
        $this->view->users = $this->loadModel('user')->getPairs('noletter');

        $this->display();
    }

    public function buildex($projectID = 0)
    {
        $this->loadModel('testtask');
        $this->session->set('buildList', $this->app->getURI(true));

        $project = $this->commonAction($projectID);
        $projectID = $project->id;

        /* Header and position. */
        $this->view->title = $project->name . $this->lang->colon . $this->lang->project->build;
        $this->view->position[] = html::a(inlink('browse', "projectID=$projectID"), $project->name);
        $this->view->position[] = $this->lang->project->build;

        /* Get builds. */
        $this->view->builds = $this->loadModel('buildex')->getProjectBuilds((int)$projectID);
        $this->view->users = $this->loadModel('user')->getPairs('noletter');

        $this->display();
    }


    public function burnex($projectID = 0)
    {
        $project = $this->commonAction($projectID);
        $projectID = $project->id;

        $this->loadModel('report');
        $this->view->charts = array();

        $milestoneID = 0;

        $milestones = $this->dao->select('id, name')->from(TABLE_PRODUCTMILESTONE)
            ->where('project')->eq($projectID)
            ->orderBy('id desc')
            ->fetchpairs();
        reset($milestones);

        if (!empty($_POST))
        {
            $milestoneID = $this->post->milestoneID;
        }
        else
        {
            $milestoneID = key($milestones);
        }




        $this->loadModel('dept');

        $depts = $this->dept->getOptionMenu();
        //$displayDepts =  array_values($this->lang->project->report->statDepts);

        $users =  $this->dao->select()->from(TABLE_USER)
            ->where('deleted')->eq(0)
            ->fetchAll('account');
        //var_dump($users);
        //*
        foreach ($users as $k => $v)
        {
            error_log("User:{$k} - dept:{$v->dept} name:{$v->realname}");
            //var_dump($v);
        }
        //*/

        $milestone = $this->project->getMilestoneById($milestoneID);
        $this->view->milestone = $milestone;
        $this->view->milestoneStartDate = helper::showYMD($milestone->deadlineRequirement);
        $this->view->milestoneEndDate = helper::showYMD($milestone->deadline);


        $storieIds = $this->dao->select('id, story')->from(TABLE_PRODUCTMILESTONESTORY)
            ->where('productMilestone')->eq($milestoneID)
            ->andWhere('project')->eq($projectID)
            ->fetchPairs('id');

        //$stories = $this->story->getProjectStories($projectID);
        //$storyIdList = array_keys($stories);

        // 只取当前选中里程碑的需求关联的任务
        $storyTasks = $this->loadModel('task')->getStoryTaskCounts($storieIds, $projectID);
        $milestoneDays = helper::getWorkingDays($milestone->deadlineQA, $milestone->deadlineRequirement);
        //error_log("dl:{$milestone->deadlineQA} dr:{$milestone->deadlineRequirement} milestoneDays:{$milestoneDays}");
        $this->view->milestoneDays = $milestoneDays;

        foreach($this->lang->project->report->statDepts as $idx => $deptID)
        {
            //$deptID = 4;
            $deptName = $depts[$deptID];

            $chartOption = new stdclass();
            $chartOption->item = '人天';
            $chartOption->type = 'bar';
            $chartOption->title = $deptName;
            $chartOption->height = 50;
            //$chartOption->width = 5000;

            $chartOption->graph = new stdclass();
            $chartOption->graph->xAxisName = '用户名';

            $chartData = $this->getDataOfMilestoneHR($deptID, $users, $storyTasks, $milestoneDays);

            $chartKey = 'chartDept' . $deptID;
            $this->view->charts[$chartKey] = $chartOption;
            $this->view->datas[$chartKey] = $chartData;//$this->report->computePercent($chartData);

            //error_log("chart {$idx} dept:{$deptID} {$deptName}");
        }

        $this->view->milestones = $milestones;
        $this->view->lastMilestoneID = $milestoneID;

        //$this->project->setMenu($this->project->getPairs(), $projectID);
        $this->projects = $this->project->getPairs();
        $this->view->title = $this->projects[$projectID] . $this->lang->colon . $this->lang->task->report->common;
        $this->view->position[] = $this->projects[$projectID];
        $this->view->position[] = $this->lang->task->report->common;
        $this->view->projectID = $projectID;
        //$this->view->browseType = $browseType;
        $this->view->checkedCharts = $this->post->charts ? join(',', $this->post->charts) : '';

        //phpinfo();
        //var_dump($chartData);

        $this->display();
    }

    public function getDataOfMilestoneHR($deptID, $users, $storyTasks, $milestoneDays)
    {
        // 先按人名归组
        $taskByUserList = array();
        foreach($storyTasks as $storyId => $cnt)
        {
            $tasks  = $this->dao->select()->from(TABLE_TASK)
                ->where('story')->eq($storyId)->andWhere('deleted')->eq(0)->orderBy('id DESC')->fetchAll();

            foreach($tasks as $task )
            {
                $user = $users[$task->assignedTo];
                //error_log("dept user:{$task->assignedTo} dept:{$user->dept} name:{$user->realname} deptID:{$deptID}");
                if($user->dept == $deptID)
                {
                    if (is_null($taskByUserList[$task->assignedTo])) {
                        $taskByUserList[$task->assignedTo] = array();
                    }

                    array_push($taskByUserList[$task->assignedTo], $task);
                }
            }
        }


        $datas = array();
        $hasTaskUsers = array();
        foreach($taskByUserList as $userName => $userTasks)
        {
            $dat = new stdClass();
            $dat->name = $users[$userName]->realname;
            $dat->value = $this->computeTaskTotalDays($userTasks);
            $dat->percent = round($dat->value / $milestoneDays, 2);
            array_push($datas, $dat);
            array_push($hasTaskUsers, $userName);
            //$hasTaskUsers[$userName] = $userName;
            //error_log("name:{$dat->name} days:{$dat->value} pct:{$dat->percent}");
        }

        $deptUsers = $this->dao->select()->from(TABLE_USER)
            ->where('deleted')->eq(0)
            ->andWhere('dept')->eq($deptID)
            ->fetchAll('account');

        foreach ( $deptUsers as $userName => $user)
        {
            //$user = $users[$userName];
            //var_dump($user);

            if($user->dept == $deptID && !$this->inArray($userName, $hasTaskUsers))
            {
                //error_log("dept2 user:{$userName} dept:{$user->dept} name:{$user->realname} deptID:{$deptID}");
                $dat = new stdClass();
                $dat->name = $user->realname;
                $dat->value = 0;
                $dat->percent = 0;
                array_push($datas, $dat);
            }
            else
            {
                //error_log("dept3 user:{$userName} dept:{$user->dept} name:{$realName} deptID:{$deptID}");
            }
        }

        return $datas;
    }

    public function inArray($val, $arr)
    {
        foreach ($arr as $k => $v)
        {
            //error_log(" inArray k:{$k} v:{$v} val:{$val}");
            if($v == $val)
            {
                return true;
            }
        }

        return false;
    }

    public function computeTaskTotalDays($taskLst)
    {
        $totalDays = 0;
        foreach($taskLst as $task) {
            $startDay = $task->estStarted;
            $endDay = $task->deadline;

            if(!helper::isValidateDate($startDay))
            {
                $startDay = helper::now();
            }

            if(!helper::isValidateDate($endDay))
            {
                $endDay = helper::nowafter(5);
            }

            $days = round(helper::getWorkingDays($endDay, $startDay), 0);
            $totalDays += $days;
            //error_log("task:{$task->id} days:{$days}");
        }
        return $totalDays;
    }


    /**
     * Browse test tasks of project.
     *
     * @param  int $projectID
     * @param  string $orderBy
     * @param  int $recTotal
     * @param  int $recPerPage
     * @param  int $pageID
     * @access public
     * @return void
     */
    public function testtask($projectID = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('testtask');
        /* Save session. */
        $this->session->set('testtaskList', $this->app->getURI(true));

        $project = $this->commonAction($projectID);
        $projectID = $project->id;

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        $this->view->title = $this->projects[$projectID] . $this->lang->colon . $this->lang->testtask->common;
        $this->view->position[] = html::a($this->createLink('project', 'testtask', "projectID=$projectID"), $this->projects[$projectID]);
        $this->view->position[] = $this->lang->testtask->common;
        $this->view->projectID = $projectID;
        $this->view->projectName = $this->projects[$projectID];
        $this->view->pager = $pager;
        $this->view->orderBy = $orderBy;
        $this->view->tasks = $this->testtask->getProjectTasks($projectID);
        $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');

        $this->display();
    }

    /**
     * Browse burndown chart of a project.
     *
     * @param  int $projectID
     * @param  string $type
     * @param  int $interval
     * @access public
     * @return void
     */
    public function burn($projectID = 0, $type = 'noweekend', $interval = 0)
    {
        $this->loadModel('report');
        $project = $this->commonAction($projectID);
        $projectID = $project->id;

        /* Header and position. */
        $title = $project->name . $this->lang->colon . $this->lang->project->burn;
        $position[] = html::a($this->createLink('project', 'browse', "projectID=$projectID"), $project->name);
        $position[] = $this->lang->project->burn;

        /* Get date list. */
        $projectInfo = $this->project->getByID($projectID);
        list($dateList, $interval) = $this->project->getDateList($projectInfo->begin, $projectInfo->end, $type, $interval, 'Y-m-d');
        $chartData = $this->project->buildBurnData($projectID, $dateList, $type);

        /* Set a space when assemble the string for english. */
        $space = $this->app->getClientLang() == 'en' ? ' ' : '';
        $dayList = array_fill(1, floor($project->days / $this->config->project->maxBurnDay) + 5, '');
        foreach ($dayList as $key => $val) $dayList[$key] = $this->lang->project->interval . $space . ($key + 1) . $space . $this->lang->day;

        /* Assign. */
        $this->view->title = $title;
        $this->view->position = $position;
        $this->view->tabID = 'burn';
        $this->view->projectID = $projectID;
        $this->view->projectName = $project->name;
        $this->view->type = $type;
        $this->view->interval = $interval;
        $this->view->chartData = $chartData;
        $this->view->dayList = array('full' => $this->lang->project->interval . $space . 1 . $space . $this->lang->day) + $dayList;

        $this->display();
    }

    /**
     * Compute burndown datas.
     *
     * @param  string $reload
     * @access public
     * @return void
     */
    public function computeBurn($reload = 'no')
    {
        $this->view->burns = $this->project->computeBurn();
        if ($reload == 'yes') die(js::reload('parent'));
        $this->display();
    }

    /**
     * Fix burn for first date.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function fixFirst($projectID)
    {
        if ($_POST) {
            $this->project->fixFirst($projectID);
            die(js::reload('parent.parent'));
        }

        $project = $this->project->getById($projectID);

        $this->view->firstBurn = $this->dao->select('*')->from(TABLE_BURN)->where('project')->eq($projectID)->andWhere('date')->eq($project->begin)->fetch();
        $this->view->project = $project;
        $this->display();
    }

    /**
     * Browse team of a project.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function team($projectID = 0)
    {
        $project = $this->commonAction($projectID);
        $projectID = $project->id;

        $title = $project->name . $this->lang->colon . $this->lang->project->team;
        $position[] = html::a($this->createLink('project', 'browse', "projectID=$projectID"), $project->name);
        $position[] = $this->lang->project->team;

        $this->view->title = $title;
        $this->view->position = $position;

        $this->display();
    }

    /**
     * Create a project.
     *
     * @param string $projectID
     * @param string $copyProjectID
     *
     * @access public
     * @return void
     */
    public function create($projectID = '', $copyProjectID = '')
    {
        if ($projectID) {
            $this->view->title = $this->lang->project->tips;
            $this->view->tips = $this->fetch('project', 'tips', "projectID=$projectID");
            $this->view->projectID = $projectID;
            $this->display();
            exit;
        }

        $name = '';
        $code = '';
        $team = '';
        $products = array();
        $whitelist = '';
        $acl = 'open';

        if ($copyProjectID) {
            $copyProject = $this->dao->select('*')->from(TABLE_PROJECT)->where('id')->eq($copyProjectID)->fetch();
            $name = $copyProject->name;
            $code = $copyProject->code;
            $team = $copyProject->team;
            $acl = $copyProject->acl;
            $whitelist = $copyProject->whitelist;
            $products = $this->project->getProducts($copyProjectID);
        }

        if (!empty($_POST)) {
            $projectID = $copyProjectID == '' ? $this->project->create() : $this->project->create($copyProjectID);
            $this->project->updateProducts($projectID);
            if (dao::isError()) die(js::error(dao::getError()));

            $this->loadModel('action')->create('project', $projectID, 'opened');
            die(js::locate($this->createLink('project', 'create', "projectID=$projectID"), 'parent'));
        }

        $this->project->setMenu($this->projects, key($this->projects));

        $this->view->title = $this->lang->project->create;
        $this->view->position[] = $this->view->title;
        $this->view->projects = array('' => '') + $this->projects;
        $this->view->groups = $this->loadModel('group')->getPairs();
        $this->view->allProducts = array(0 => '') + $this->loadModel('product')->getPairs('noclosed|nocode');
        $this->view->name = $name;
        $this->view->code = $code;
        $this->view->team = $team;
        $this->view->products = $products;
        $this->view->whitelist = $whitelist;
        $this->view->acl = $acl;
        $this->view->copyProjectID = $copyProjectID;
        $this->view->branchGroups = $this->loadModel('branch')->getByProducts(array_keys($products));
        $this->display();
    }

    /**
     * Edit a project.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function edit($projectID, $action = 'edit', $extra = '')
    {
        $browseProjectLink = $this->createLink('project', 'browse', "projectID=$projectID");
        if (!empty($_POST)) {
            $changes = $this->project->update($projectID);
            $this->project->updateProducts($projectID);
            if (dao::isError()) die(js::error(dao::getError()));
            if ($action == 'undelete') {
                $this->loadModel('action');
                $this->dao->update(TABLE_PROJECT)->set('deleted')->eq(0)->where('id')->eq($projectID)->exec();
                $this->dao->update(TABLE_ACTION)->set('extra')->eq(ACTIONMODEL::BE_UNDELETED)->where('id')->eq($extra)->exec();
                $this->action->create('project', $projectID, 'undeleted');
            }
            if ($changes) {
                $actionID = $this->loadModel('action')->create('project', $projectID, 'edited');
                $this->action->logHistory($actionID, $changes);
            }
            die(js::locate($this->createLink('project', 'view', "projectID=$projectID"), 'parent'));
        }

        /* Set menu. */
        $this->project->setMenu($this->projects, $projectID);

        $projects = array('' => '') + $this->projects;
        $project = $this->project->getById($projectID);
        $managers = $this->project->getDefaultManagers($projectID);

        /* Remove current project from the projects. */
        unset($projects[$projectID]);

        $title = $this->lang->project->edit . $this->lang->colon . $project->name;
        $position[] = html::a($browseProjectLink, $project->name);
        $position[] = $this->lang->project->edit;

        $allProducts = array(0 => '') + $this->loadModel('product')->getPairs('noclosed|nocode');
        $linkedProducts = $this->project->getProducts($project->id);
        foreach ($linkedProducts as $product) {
            if (!isset($allProducts[$product->id])) $allProducts[$product->id] = $product->name;
        }

        $this->view->title = $title;
        $this->view->position = $position;
        $this->view->projects = $projects;
        $this->view->project = $project;
        $this->view->poUsers = $this->loadModel('user')->getPairs('noclosed|nodeleted|pofirst', $project->PO);
        $this->view->pmUsers = $this->user->getPairs('noclosed|nodeleted|pmfirst', $project->PM);
        $this->view->qdUsers = $this->user->getPairs('noclosed|nodeleted|qdfirst', $project->QD);
        $this->view->rdUsers = $this->user->getPairs('noclosed|nodeleted|devfirst', $project->RD);
        $this->view->groups = $this->loadModel('group')->getPairs();
        $this->view->allProducts = $allProducts;
        $this->view->linkedProducts = $linkedProducts;
        $this->view->branchGroups = $this->loadModel('branch')->getByProducts(array_keys($linkedProducts));

        $this->display();
    }

    /**
     * Batch edit.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function batchEdit($projectID = 0)
    {
        if ($this->post->names) {
            $allChanges = $this->project->batchUpdate();
            if (!empty($allChanges)) {
                foreach ($allChanges as $projectID => $changes) {
                    if (empty($changes)) continue;

                    $actionID = $this->loadModel('action')->create('project', $projectID, 'Edited');
                    $this->action->logHistory($actionID, $changes);
                }
            }
            die(js::locate($this->session->projectList, 'parent'));
        }

        $this->project->setMenu($this->projects, $projectID);

        $projectIDList = $this->post->projectIDList ? $this->post->projectIDList : die(js::locate($this->session->projectList, 'parent'));
        $projects = $this->dao->select('*')->from(TABLE_PROJECT)->where('id')->in($projectIDList)->fetchAll('id');

        $appendPoUsers = $appendPmUsers = $appendQdUsers = $appendRdUsers = array();
        foreach ($projects as $project) {
            $appendPoUsers[$project->PO] = $project->PO;
            $appendPmUsers[$project->PM] = $project->PM;
            $appendQdUsers[$project->QD] = $project->QD;
            $appendRdUsers[$project->RD] = $project->RD;
        }

        /* Set custom. */
        foreach (explode(',', $this->config->project->customBatchEditFields) as $field) $customFields[$field] = $this->lang->project->$field;
        $this->view->customFields = $customFields;
        $this->view->showFields = $this->config->project->custom->batchEditFields;

        $this->view->title = $this->lang->project->batchEdit;
        $this->view->position[] = $this->lang->project->batchEdit;
        $this->view->projectIDList = $projectIDList;
        $this->view->projects = $projects;
        $this->view->pmUsers = $this->loadModel('user')->getPairs('noclosed|nodeleted|pmfirst', $appendPmUsers);
        $this->view->poUsers = $this->user->getPairs('noclosed|nodeleted|pofirst', $appendPoUsers);
        $this->view->qdUsers = $this->user->getPairs('noclosed|nodeleted|qdfirst', $appendQdUsers);
        $this->view->rdUsers = $this->user->getPairs('noclosed|nodeleted|devfirst', $appendRdUsers);
        $this->display();
    }

    /**
     * Start project.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function start($projectID)
    {
        $project = $this->commonAction($projectID);
        $projectID = $project->id;

        if (!empty($_POST)) {
            $this->loadModel('action');
            $changes = $this->project->start($projectID);
            if (dao::isError()) die(js::error(dao::getError()));

            if ($this->post->comment != '' or !empty($changes)) {
                $actionID = $this->action->create('project', $projectID, 'Started', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }
            die(js::reload('parent.parent'));
        }

        $this->view->title = $this->view->project->name . $this->lang->colon . $this->lang->project->start;
        $this->view->position[] = html::a($this->createLink('project', 'browse', "projectID=$projectID"), $this->view->project->name);
        $this->view->position[] = $this->lang->project->start;
        $this->view->users = $this->loadModel('user')->getPairs('noletter');
        $this->view->actions = $this->loadModel('action')->getList('project', $projectID);
        $this->display();
    }

    /**
     * Delay project.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function putoff($projectID)
    {
        $project = $this->commonAction($projectID);
        $projectID = $project->id;

        if (!empty($_POST)) {
            $this->loadModel('action');
            $changes = $this->project->putoff($projectID);
            if (dao::isError()) die(js::error(dao::getError()));

            if ($this->post->comment != '' or !empty($changes)) {
                $actionID = $this->action->create('project', $projectID, 'Delayed', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }
            die(js::reload('parent.parent'));
        }

        $this->view->title = $this->view->project->name . $this->lang->colon . $this->lang->project->putoff;
        $this->view->position[] = html::a($this->createLink('project', 'browse', "projectID=$projectID"), $this->view->project->name);
        $this->view->position[] = $this->lang->project->putoff;
        $this->view->users = $this->loadModel('user')->getPairs('noletter');
        $this->view->actions = $this->loadModel('action')->getList('project', $projectID);
        $this->display();
    }

    /**
     * Suspend project.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function suspend($projectID)
    {
        $project = $this->commonAction($projectID);
        $projectID = $project->id;

        if (!empty($_POST)) {
            $this->loadModel('action');
            $changes = $this->project->suspend($projectID);
            if (dao::isError()) die(js::error(dao::getError()));

            if ($this->post->comment != '' or !empty($changes)) {
                $actionID = $this->action->create('project', $projectID, 'Suspended', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }
            die(js::reload('parent.parent'));
        }

        $this->view->title = $this->view->project->name . $this->lang->colon . $this->lang->project->suspend;
        $this->view->position[] = html::a($this->createLink('project', 'browse', "projectID=$projectID"), $this->view->project->name);
        $this->view->position[] = $this->lang->project->suspend;
        $this->view->users = $this->loadModel('user')->getPairs('noletter');
        $this->view->actions = $this->loadModel('action')->getList('project', $projectID);
        $this->display();
    }

    /**
     * Activate project.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function activate($projectID)
    {
        $project = $this->commonAction($projectID);
        $projectID = $project->id;

        if (!empty($_POST)) {
            $this->loadModel('action');
            $changes = $this->project->activate($projectID);
            if (dao::isError()) die(js::error(dao::getError()));

            if ($this->post->comment != '' or !empty($changes)) {
                $actionID = $this->action->create('project', $projectID, 'Activated', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }
            die(js::reload('parent.parent'));
        }

        $newBegin = date('Y-m-d');
        $dateDiff = helper::diffDate($newBegin, $project->begin);
        $newEnd = date('Y-m-d', strtotime($project->end) + $dateDiff * 24 * 3600);

        $this->view->title = $this->view->project->name . $this->lang->colon . $this->lang->project->activate;
        $this->view->position[] = html::a($this->createLink('project', 'browse', "projectID=$projectID"), $this->view->project->name);
        $this->view->position[] = $this->lang->project->activate;
        $this->view->project = $project;
        $this->view->users = $this->loadModel('user')->getPairs('noletter');
        $this->view->actions = $this->loadModel('action')->getList('project', $projectID);
        $this->view->newBegin = $newBegin;
        $this->view->newEnd = $newEnd;
        $this->display();
    }

    /**
     * Close project.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function close($projectID)
    {
        $project = $this->commonAction($projectID);
        $projectID = $project->id;

        if (!empty($_POST)) {
            $this->loadModel('action');
            $changes = $this->project->close($projectID);
            if (dao::isError()) die(js::error(dao::getError()));

            if ($this->post->comment != '' or !empty($changes)) {
                $actionID = $this->action->create('project', $projectID, 'Closed', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }
            die(js::reload('parent.parent'));
        }

        $this->view->title = $this->view->project->name . $this->lang->colon . $this->lang->project->close;
        $this->view->position[] = html::a($this->createLink('project', 'browse', "projectID=$projectID"), $this->view->project->name);
        $this->view->position[] = $this->lang->project->close;
        $this->view->users = $this->loadModel('user')->getPairs('noletter');
        $this->view->actions = $this->loadModel('action')->getList('project', $projectID);
        $this->display();
    }

    /**
     * View a project.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function view($projectID)
    {
        $project = $this->project->getById($projectID, true);
        if (!$project) die(js::error($this->lang->notFound) . js::locate('back'));

        $products = $this->project->getProducts($project->id);

        /* Set menu. */
        $this->project->setMenu($this->projects, $project->id);

        $this->view->title = $this->lang->project->view;
        $this->view->position[] = html::a($this->createLink('project', 'browse', "projectID=$projectID"), $project->name);
        $this->view->position[] = $this->view->title;

        $this->view->project = $project;
        $this->view->products = $products;
        $this->view->branchGroups = $this->loadModel('branch')->getByProducts(array_keys($products));
        $this->view->groups = $this->loadModel('group')->getPairs();
        $this->view->actions = $this->loadModel('action')->getList('project', $projectID);
        $this->view->users = $this->loadModel('user')->getPairs('noletter');

        $this->display();
    }

    /**
     * Kanban.
     *
     * @param  int $projectID
     * @param  string $type
     * @param  string $orderBy
     * @access public
     * @return void
     */
    public function kanban($projectID, $type = 'story', $orderBy = 'order_asc')
    {
        /* Save to session. */
        $uri = $this->app->getURI(true);
        $this->app->session->set('taskList', $uri);
        $this->app->session->set('storyList', $uri);
        $this->app->session->set('bugList', $uri);

        /* Compatibility IE8*/
        if (strpos($this->server->http_user_agent, 'MSIE 8.0') !== false) header("X-UA-Compatible: IE=EmulateIE7");

        $this->project->setMenu($this->projects, $projectID);
        $project = $this->loadModel('project')->getById($projectID);
        $tasks = $this->project->getKanbanTasks($projectID, "id");
        $bugs = $this->loadModel('bug')->getProjectBugs($projectID);
        $stories = $this->loadModel('story')->getProjectStories($projectID, $orderBy);

        $kanbanGroup = $this->project->getKanbanGroupData($stories, $tasks, $bugs, $type);
        $kanbanSetting = $this->project->getKanbanSetting($projectID);

        $this->view->title = $this->lang->project->kanban;
        $this->view->position[] = html::a($this->createLink('project', 'browse', "projectID=$projectID"), $project->name);
        $this->view->position[] = $this->lang->project->kanban;
        $this->view->stories = $stories;
        $this->view->realnames = $this->loadModel('user')->getPairs('noletter');
        $this->view->orderBy = $orderBy;
        $this->view->projectID = $projectID;
        $this->view->project = $project;
        $this->view->type = $type;
        $this->view->kanbanGroup = $kanbanGroup;
        $this->view->allCols = $kanbanSetting->allCols;
        $this->view->showOption = $kanbanSetting->showOption;
        $this->view->colorList = $kanbanSetting->colorList;

        $this->display();
    }

    /**
     * Tree view.
     * Product
     *
     * @param  int $projectID
     * @param  string $type
     * @access public
     * @return void
     */
    public function tree($projectID, $type = '')
    {
        $this->project->setMenu($this->projects, $projectID);
        $project = $this->loadModel('project')->getById($projectID);
        $tree = $this->project->getProjectTree($projectID);

        /* Save to session. */
        $uri = $this->app->getURI(true);
        $this->app->session->set('taskList', $uri);
        $this->app->session->set('storyList', $uri);
        $this->app->session->set('projectList', $uri);

        if ($type === 'json') die(helper::jsonEncode4Parse($tree, JSON_HEX_QUOT | JSON_HEX_APOS));

        $this->view->title = $this->lang->project->tree;
        $this->view->position[] = html::a($this->createLink('project', 'browse', "projectID=$projectID"), $project->name);
        $this->view->position[] = $this->lang->project->tree;
        $this->view->project = $project;
        $this->view->projectID = $projectID;
        $this->view->level = $type;
        $this->view->tree = $tree;
        $this->display();
    }

    /**
     * Print kanban.
     *
     * @param  int $projectID
     * @param  string $orderBy
     * @access public
     * @return void
     */
    public function printKanban($projectID, $orderBy = 'id_asc')
    {
        $this->view->title = $this->lang->project->printKanban;
        $contents = array('story', 'wait', 'doing', 'done', 'cancel');

        if ($_POST) {
            $stories = $this->loadModel('story')->getProjectStories($projectID, $orderBy);
            $storySpecs = $this->story->getStorySpecs(array_keys($stories));

            $order = 1;
            foreach ($stories as $story) $story->order = $order++;

            $kanbanTasks = $this->project->getKanbanTasks($projectID, "id");
            $kanbanBugs = $this->loadModel('bug')->getProjectBugs($projectID);

            $users = array();
            $taskAndBugs = array();
            foreach ($kanbanTasks as $task) {
                $storyID = $task->storyID;
                $status = $task->status;
                $users[] = $task->assignedTo;

                $taskAndBugs[$status]["task{$task->id}"] = $task;
            }
            foreach ($kanbanBugs as $bug) {
                $storyID = $bug->story;
                $status = $bug->status;
                $status = $status == 'active' ? 'wait' : ($status == 'resolved' ? ($bug->resolution == 'postponed' ? 'cancel' : 'done') : $status);
                $users[] = $bug->assignedTo;

                $taskAndBugs[$status]["bug{$bug->id}"] = $bug;
            }

            $datas = array();
            foreach ($contents as $content) {
                if ($content != 'story' and !isset($taskAndBugs[$content])) continue;
                $datas[$content] = $content == 'story' ? $stories : $taskAndBugs[$content];
            }

            unset($this->lang->story->stageList['']);
            unset($this->lang->story->stageList['wait']);
            unset($this->lang->story->stageList['planned']);
            unset($this->lang->story->stageList['projected']);
            unset($this->lang->story->stageList['released']);
            unset($this->lang->task->statusList['']);
            unset($this->lang->task->statusList['wait']);
            unset($this->lang->task->statusList['closed']);
            unset($this->lang->bug->statusList['']);
            unset($this->lang->bug->statusList['closed']);

            $originalDatas = $datas;
            if ($this->post->content == 'increment') {
                $prevKanbans = $this->project->getPrevKanban($projectID);
                foreach ($datas as $type => $data) {
                    if (isset($prevKanbans[$type])) {
                        $prevData = $prevKanbans[$type];
                        foreach ($prevData as $id) {
                            if (isset($data[$id])) unset($datas[$type][$id]);
                        }
                    }
                }
            }

            $this->project->saveKanbanData($projectID, $originalDatas);

            $hasBurn = $this->post->content == 'all';
            if ($hasBurn) {
                /* Get date list. */
                $projectInfo = $this->project->getByID($projectID);
                list($dateList) = $this->project->getDateList($projectInfo->begin, $projectInfo->end, 'noweekend');
                $chartData = $this->project->buildBurnData($projectID, $dateList, 'noweekend');
            }

            $this->view->hasBurn = $hasBurn;
            $this->view->datas = $datas;
            $this->view->chartData = $chartData;
            $this->view->storySpecs = $storySpecs;
            $this->view->realnames = $this->loadModel('user')->getRealNameAndEmails($users);
            $this->view->projectID = $projectID;

            die($this->display());

        }

        $this->project->setMenu($this->projects, $projectID);
        $project = $this->project->getById($projectID);

        $this->view->position[] = html::a($this->createLink('project', 'browse', "projectID=$projectID"), $project->name);
        $this->view->position[] = $this->lang->project->printKanban;
        $this->display();
    }

    /**
     * Story kanban.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function storyKanban($projectID)
    {
        /* Save to session. */
        $uri = $this->app->getURI(true);
        $this->app->session->set('storyList', $uri);

        /* Compatibility IE8*/
        if (strpos($this->server->http_user_agent, 'MSIE 8.0') !== false) header("X-UA-Compatible: IE=EmulateIE7");

        $this->project->setMenu($this->projects, $projectID);
        $project = $this->loadModel('project')->getById($projectID);
        $stories = $this->loadModel('story')->getProjectStories($projectID);
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'story', false);

        /* Get project's product. */
        $productID = 0;
        $productPairs = $this->loadModel('product')->getProductsByProject($projectID);
        if ($productPairs) $productID = key($productPairs);

        $this->view->title = $this->lang->project->kanban;
        $this->view->position[] = html::a($this->createLink('project', 'story', "projectID=$projectID"), $project->name);
        $this->view->position[] = $this->lang->project->kanban;
        $this->view->stories = $this->story->getKanbanGroupData($stories);
        $this->view->realnames = $this->loadModel('user')->getPairs('noletter');
        $this->view->projectID = $projectID;
        $this->view->project = $project;
        $this->view->productID = $productID;

        $kanbanSetting = $this->project->getKanbanSetting($projectID);
        $this->view->showOption = $kanbanSetting->showOption;

        $this->display();
    }

    /**
     * Delete a project.
     *
     * @param  int $projectID
     * @param  string $confirm yes|no
     * @access public
     * @return void
     */
    public function delete($projectID, $confirm = 'no')
    {
        if ($confirm == 'no') {
            echo js::confirm(sprintf($this->lang->project->confirmDelete, $this->projects[$projectID]), $this->createLink('project', 'delete', "projectID=$projectID&confirm=yes"));
            exit;
        } else {
            $this->project->delete(TABLE_PROJECT, $projectID);
            $this->dao->update(TABLE_DOCLIB)->set('deleted')->eq(1)->where('project')->eq($projectID)->exec();
            $this->session->set('project', '');
            die(js::locate(inlink('index'), 'parent'));
        }
    }

    /**
     * Manage products.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function manageProducts($projectID, $from = '')
    {
        /* use first project if projectID does not exist. */
        if (!isset($this->projects[$projectID])) $projectID = key($this->projects);

        $browseProjectLink = $this->createLink('project', 'browse', "projectID=$projectID");
        if (!empty($_POST)) {
            if ($from == 'buildCreate' && $this->session->buildCreate) $browseProjectLink = $this->session->buildCreate;

            $this->project->updateProducts($projectID);
            if (dao::isError()) dis(js::error(dao::getError()));
            die(js::locate($browseProjectLink));
        }

        $this->loadModel('product');
        $project = $this->project->getById($projectID);

        /* Set menu. */
        $this->project->setMenu($this->projects, $project->id);

        /* Title and position. */
        $title = $this->lang->project->manageProducts . $this->lang->colon . $project->name;
        $position[] = html::a($browseProjectLink, $project->name);
        $position[] = $this->lang->project->manageProducts;

        $allProducts = $this->product->getPairs('noclosed|nocode');
        $linkedProducts = $this->project->getProducts($project->id);
        // Merge allProducts and linkedProducts for closed product.
        foreach ($linkedProducts as $product) {
            if (!isset($allProducts[$product->id])) $allProducts[$product->id] = $product->name;
        }

        /* Assign. */
        $this->view->title = $title;
        $this->view->position = $position;
        $this->view->allProducts = $allProducts;
        $this->view->linkedProducts = $linkedProducts;
        $this->view->branchGroups = $this->loadModel('branch')->getByProducts(array_keys($allProducts));

        $this->display();
    }

    /**
     * Manage childs projects.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function manageChilds($projectID)
    {
        $browseProjectLink = $this->createLink('project', 'browse', "projectID=$projectID");
        if (!empty($_POST)) {
            $this->project->updateChilds($projectID);
            die(js::locate($browseProjectLink));
        }
        $project = $this->project->getById($projectID);
        $projects = $this->projects;
        unset($projects[$projectID]);
        unset($projects[$project->parent]);
        if (empty($projects)) $this->locate($browseProjectLink);

        /* Header and position. */
        $title = $this->lang->project->manageChilds . $this->lang->colon . $project->name;
        $position[] = html::a($browseProjectLink, $project->name);
        $position[] = $this->lang->project->manageChilds;

        $childProjects = $this->project->getChildProjects($project->id);
        $childProjects = join(",", array_keys($childProjects));

        /* Set menu. */
        $this->project->setMenu($this->projects, $project->id);

        /* Assign. */
        $this->view->title = $title;
        $this->view->position = $position;
        $this->view->projects = $projects;
        $this->view->childProjects = $childProjects;

        $this->display();
    }

    /**
     * Manage members of the project.
     *
     * @param  int $projectID
     * @param  int $team2Import the team to import.
     * @access public
     * @return void
     */
    public function manageMembers($projectID = 0, $team2Import = 0, $dept = '')
    {
        if (!empty($_POST)) {
            $this->project->manageMembers($projectID);
            $this->locate($this->createLink('project', 'team', "projectID=$projectID"));
        }

        /* Load model. */
        $this->loadModel('user');
        $this->loadModel('dept');

        $project = $this->project->getById($projectID);
        $users = $this->user->getPairs('noclosed|nodeleted|devfirst');
        $roles = $this->user->getUserRoles(array_keys($users));
        $deptUsers = $dept === '' ? array() : $this->dept->getDeptUserPairs($dept);
        $currentMembers = $this->project->getTeamMembers($projectID);
        $members2Import = $this->project->getMembers2Import($team2Import, array_keys($currentMembers));
        $teams2Import = $this->project->getTeams2Import($this->app->user->account, $projectID);
        $teams2Import = array('' => '') + $teams2Import;

        /* Set menu. */
        $this->project->setMenu($this->projects, $project->id);

        $title = $this->lang->project->manageMembers . $this->lang->colon . $project->name;
        $position[] = html::a($this->createLink('project', 'browse', "projectID=$projectID"), $project->name);
        $position[] = $this->lang->project->manageMembers;

        $this->view->title = $title;
        $this->view->position = $position;
        $this->view->project = $project;
        $this->view->users = $users;
        $this->view->deptUsers = $deptUsers;
        $this->view->roles = $roles;
        $this->view->dept = $dept;
        $this->view->depts = array('' => '') + $this->loadModel('dept')->getOptionMenu();
        $this->view->currentMembers = $currentMembers;
        $this->view->members2Import = $members2Import;
        $this->view->teams2Import = $teams2Import;
        $this->view->team2Import = $team2Import;
        $this->display();
    }

    /**
     * Unlink a memeber.
     *
     * @param  int $projectID
     * @param  string $account
     * @param  string $confirm yes|no
     * @access public
     * @return void
     */
    public function unlinkMember($projectID, $account, $confirm = 'no')
    {
        if ($confirm == 'no') {
            die(js::confirm($this->lang->project->confirmUnlinkMember, $this->inlink('unlinkMember', "projectID=$projectID&account=$account&confirm=yes")));
        } else {
            $this->project->unlinkMember($projectID, $account);

            /* if ajax request, send result. */
            if ($this->server->ajax) {
                if (dao::isError()) {
                    $response['result'] = 'fail';
                    $response['message'] = dao::getError();
                } else {
                    $response['result'] = 'success';
                    $response['message'] = '';
                }
                $this->send($response);
            }
            die(js::locate($this->inlink('team', "projectID=$projectID"), 'parent'));
        }
    }

    /**
     * Link stories to a project.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function linkStory($projectID = 0, $browseType = '', $param = 0)
    {
        $this->loadModel('story');
        $this->loadModel('product');

        /* Get projects and products. */
        $project = $this->project->getById($projectID);
        $products = $this->project->getProducts($projectID);
        $browseLink = $this->createLink('project', 'story', "projectID=$projectID");

        $this->session->set('storyList', $this->app->getURI(true)); // Save session.
        $this->project->setMenu($this->projects, $project->id);     // Set menu.

        if (empty($products)) {
            echo js::alert($this->lang->project->errorNoLinkedProducts);
            die(js::locate($this->createLink('project', 'manageproducts', "projectID=$projectID")));
        }

        if (!empty($_POST)) {
            $this->project->linkStory($projectID);
            die(js::locate($browseLink));
        }

        $queryID = ($browseType == 'bySearch') ? (int)$param : 0;

        /* Set modules and branches. */
        $modules = array();
        $branches = array();
        $productType = 'normal';
        $this->loadModel('tree');
        $this->loadModel('branch');
        foreach ($products as $product) {
            $productModules = $this->tree->getOptionMenu($product->id);
            foreach ($productModules as $moduleID => $moduleName) $modules[$moduleID] = ((count($products) >= 2 and $moduleID != 0) ? $product->name : '') . $moduleName;
            if ($product->type != 'normal') {
                $productType = $product->type;
                $branches[$product->branch] = $product->branch;
                if ($product->branch == 0) {
                    foreach ($this->branch->getPairs($product->id, 'noempty') as $branchID => $branchName) $branches[$branchID] = $branchID;
                }
            }
        }

        /* Build the search form. */
        $actionURL = $this->createLink('project', 'linkStory', "projectID=$projectID&browseType=bySearch&queryID=myQueryID");
        $branchGroups = $this->loadModel('branch')->getByProducts(array_keys($products), 'noempty');
        $this->project->buildStorySearchForm($products, $branchGroups, $modules, $queryID, $actionURL, 'linkStory');

        if ($browseType == 'bySearch') {
            $allStories = $this->story->getBySearch('', $queryID, 'id', null, $projectID);
        } else {
            $allStories = $this->story->getProductStories(array_keys($products), $branches, $moduleID = '0', $status = 'active');
        }
        $prjStories = $this->story->getProjectStoryPairs($projectID);

        /* Assign. */
        $title = $project->name . $this->lang->colon . $this->lang->project->linkStory;
        $position[] = html::a($browseLink, $project->name);
        $position[] = $this->lang->project->linkStory;

        $this->view->title = $title;
        $this->view->position = $position;
        $this->view->project = $project;
        $this->view->products = $products;
        $this->view->allStories = $allStories;
        $this->view->prjStories = $prjStories;
        $this->view->browseType = $browseType;
        $this->view->productType = $productType;
        $this->view->modules = $modules;
        $this->view->users = $this->loadModel('user')->getPairs('noletter');
        $this->view->branchGroups = $branchGroups;
        $this->display();
    }

    /**
     * Unlink a story.
     *
     * @param  int $projectID
     * @param  int $storyID
     * @param  string $confirm yes|no
     * @access public
     * @return void
     */
    public function unlinkStory($projectID, $storyID, $confirm = 'no')
    {
        if ($confirm == 'no') {
            die(js::confirm($this->lang->project->confirmUnlinkStory, $this->createLink('project', 'unlinkstory', "projectID=$projectID&storyID=$storyID&confirm=yes")));
        } else {
            $this->project->unlinkStory($projectID, $storyID);

            /* if kanban then reload and if ajax request then send result. */
            if (isonlybody()) {
                die(js::reload('parent'));
            } elseif (helper::isAjaxRequest()) {
                if (dao::isError()) {
                    $response['result'] = 'fail';
                    $response['message'] = dao::getError();
                } else {
                    $response['result'] = 'success';
                    $response['message'] = '';
                }
                $this->send($response);
            }
            die(js::locate($this->app->session->storyList, 'parent'));
        }
    }

    /**
     * batch unlink story.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function batchUnlinkStory($projectID)
    {
        if (isset($_POST['storyIDList'])) {
            $storyIDList = $this->post->storyIDList;
            $_POST = array();
            foreach ($storyIDList as $storyID) {
                $this->project->unlinkStory($projectID, $storyID);
            }
        }
        if (!dao::isError()) $this->loadModel('score')->create('ajax', 'batchOther');
        die(js::locate($this->createLink('project', 'story', "projectID=$projectID")));
    }

    /**
     * Project dynamic.
     *
     * @param  string $type
     * @param  string $orderBy
     * @param  int $recTotal
     * @param  int $recPerPage
     * @param  int $pageID
     * @access public
     * @return void
     */
    public function dynamic($projectID = 0, $type = 'today', $param = '', $orderBy = 'date_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Save session. */
        $uri = $this->app->getURI(true);
        $this->session->set('productList', $uri);
        $this->session->set('productPlanList', $uri);
        $this->session->set('releaseList', $uri);
        $this->session->set('storyList', $uri);
        $this->session->set('projectList', $uri);
        $this->session->set('taskList', $uri);
        $this->session->set('buildList', $uri);
        $this->session->set('bugList', $uri);
        $this->session->set('caseList', $uri);
        $this->session->set('testtaskList', $uri);

        /* use first project if projectID does not exist. */
        if (!isset($this->projects[$projectID])) $projectID = key($this->projects);

        /* Append id for secend sort. */
        $sort = $this->loadModel('common')->appendOrder($orderBy);

        /* Set the menu. If the projectID = 0, use the indexMenu instead. */
        $this->project->setMenu($this->projects, $projectID);
        if ($projectID == 0) {
            $this->projects = array('0' => $this->lang->project->selectProject) + $this->projects;
            unset($this->lang->project->menu);
            $this->lang->project->menu = $this->lang->project->indexMenu;
            $this->lang->project->menu->list = $this->project->select($this->projects, 0, 'project', 'dynamic');
        }

        /* Set the pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        /* Set the user and type. */
        $account = $type == 'account' ? $param : 'all';
        $period = $type == 'account' ? 'all' : $type;

        /* The header and position. */
        $project = $this->project->getByID($projectID);
        $this->view->title = $project->name . $this->lang->colon . $this->lang->project->dynamic;
        $this->view->position[] = html::a($this->createLink('project', 'browse', "projectID=$projectID"), $project->name);
        $this->view->position[] = $this->lang->project->dynamic;

        /* Assign. */
        $this->view->projectID = $projectID;
        $this->view->type = $type;
        $this->view->users = $this->loadModel('user')->getPairs('noletter|nodeleted');
        $this->view->account = $account;
        $this->view->orderBy = $orderBy;
        $this->view->pager = $pager;
        $this->view->param = $param;
        $this->view->actions = $this->loadModel('action')->getDynamic($account, $period, $sort, $pager, 'all', $projectID);
        $this->display();
    }

    /**
     * AJAX: get products of a project in html select.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function ajaxGetProducts($projectID)
    {
        $products = $this->project->getProducts($projectID, false);
        die(html::select('product', $products, '', 'class="form-control"'));
    }

    /**
     * AJAX: get team members of the project.
     *
     * @param  int $projectID
     * @param  string $assignedTo
     * @access public
     * @return void
     */
    public function ajaxGetMembers($projectID, $assignedTo = '')
    {
        $users = $this->project->getTeamMemberPairs($projectID);
        if ($this->app->getViewType() === 'json') {
            die(json_encode($users));
        } else {
            $assignedTo = isset($users[$assignedTo]) ? $assignedTo : '';
            die(html::select('assignedTo', $users, $assignedTo, "class='form-control'"));
        }
    }

    /**
     * When create a project, help the user.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function tips($projectID)
    {
        $this->view->project = $this->project->getById($projectID);
        $this->view->projectID = $projectID;
        $this->display('project', 'tips');
    }

    /**
     * Drop menu page.
     *
     * @param  int $projectID
     * @param  int $module
     * @param  int $method
     * @param  int $extra
     * @access public
     * @return void
     */
    public function ajaxGetDropMenu($projectID, $module, $method, $extra)
    {
        $this->view->link = $this->project->getProjectLink($module, $method, $extra);
        $this->view->projectID = $projectID;
        $this->view->module = $module;
        $this->view->method = $method;
        $this->view->extra = $extra;

        $projects = $this->dao->select('*')->from(TABLE_PROJECT)->where('id')->in(array_keys($this->projects))->orderBy('order desc')->fetchAll();
        $projectPairs = array();
        foreach ($projects as $project) $projectPairs[$project->id] = $project->name;
        $projectsPinyin = common::convert2Pinyin($projectPairs);
        foreach ($projects as $key => $project) $project->key = $projectsPinyin[$project->name];

        $this->view->projects = $projects;
        $this->display();
    }

    /**
     * Update order.
     *
     * @access public
     * @return void
     */
    public function updateOrder()
    {
        $idList = explode(',', trim($this->post->projects, ','));
        $orderBy = $this->post->orderBy;
        if (strpos($orderBy, 'order') === false) return false;

        $projects = $this->dao->select('id,`order`')->from(TABLE_PROJECT)->where('id')->in($idList)->orderBy($orderBy)->fetchPairs('order', 'id');
        foreach ($projects as $order => $id) {
            $newID = array_shift($idList);
            if ($id == $newID) continue;
            $this->dao->update(TABLE_PROJECT)->set('`order`')->eq($order)->where('id')->eq($newID)->exec();
        }
    }

    /**
     * Story sort.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function storySort($projectID)
    {
        $idList = explode(',', trim($this->post->storys, ','));
        $orderBy = $this->post->orderBy;

        $order = $this->dao->select('*')->from(TABLE_PROJECTSTORY)->where('story')->in($idList)->andWhere('project')->eq($projectID)->orderBy('order_asc')->fetch('order');
        foreach ($idList as $storyID) {
            $this->dao->update(TABLE_PROJECTSTORY)->set('`order`')->eq($order)->where('story')->eq($storyID)->andWhere('project')->eq($projectID)->exec();
            $order++;
        }
    }

    /**
     * All project.
     *
     * @param  string $status
     * @param  int $projectID
     * @param  string $orderBy
     * @param  int $productID
     * @param  int $recTotal
     * @param  int $recPerPage
     * @param  int $pageID
     * @access public
     * @return void
     */
    public function all($status = 'undone', $projectID = 0, $orderBy = 'order_desc', $productID = 0, $recTotal = 0, $recPerPage = 10, $pageID = 1)
    {
        if ($this->projects) {
            $project = $this->commonAction($projectID);
            $projectID = $project->id;
        }
        $this->session->set('projectList', $this->app->getURI(true));

        /* Load pager and get tasks. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->app->loadLang('my');
        $this->view->title = $this->lang->project->allProject;
        $this->view->position[] = $this->lang->project->allProject;
        $this->view->projectStats = $this->project->getProjectStats($status == 'byproduct' ? 'all' : $status, $productID, 0, 30, $orderBy, $pager);
        $this->view->products = array(0 => $this->lang->product->select) + $this->loadModel('product')->getPairs();
        $this->view->productID = $productID;
        $this->view->projectID = $projectID;
        $this->view->pager = $pager;
        $this->view->orderBy = $orderBy;
        $this->view->users = $this->loadModel('user')->getPairs('noletter');
        $this->view->status = $status;

        $this->display();
    }

    /**
     * Export project.
     *
     * @param  string $status
     * @param  int $productID
     * @param  string $orderBy
     * @access public
     * @return void
     */
    public function export($status, $productID, $orderBy)
    {
        //error_log("XXXXXXX: project.control.export product:$productID order:$orderBy post:$this->post");

        if ($_POST) {

            //error_log("XXXXXXX: project.control.export.post product:$productID order:$orderBy");

            $projectLang = $this->lang->project;
            $projectConfig = $this->config->project;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $projectConfig->list->exportFields);
            foreach ($fields as $key => $fieldName) {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = zget($projectLang, $fieldName);
                unset($fields[$key]);
            }

            $projectStats = $this->project->getProjectStats($status == 'byproduct' ? 'all' : $status, $productID, 0, 30, $orderBy, null);
            $users = $this->loadModel('user')->getPairs('noletter');
            foreach ($projectStats as $i => $project) {
                $project->PM = zget($users, $project->PM);
                $project->status = isset($project->delay) ? $projectLang->delayed : $projectLang->statusList[$project->status];
                $project->totalEstimate = $project->hours->totalEstimate;
                $project->totalConsumed = $project->hours->totalConsumed;
                $project->totalLeft = $project->hours->totalLeft;
                $project->progress = $project->hours->progress . '%';

                if ($this->post->exportType == 'selected') {
                    $checkedItem = $this->cookie->checkedItem;
                    if (strpos(",$checkedItem,", ",{$project->id},") === false) unset($projectStats[$i]);
                }
            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $projectStats);
            $this->post->set('kind', 'project');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->display();
    }

    /**
     * Doc for compatible.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function doc($projectID)
    {
        $this->locate($this->createLink('doc', 'objectLibs', "type=project&objectID=$projectID&from=project"));
    }

    /**
     * Kanban setting.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function ajaxKanbanSetting($projectID)
    {
        if ($_POST) {
            $this->loadModel('setting');
            $data = fixer::input('post')->get();
            if (common::hasPriv('project', 'kanbanHideCols')) {
                $allCols = $data->allCols;
                $this->setting->setItem("system.project.kanbanSetting.allCols", $allCols);
            }

            $account = $this->app->user->account;
            $this->setting->setItem("{$account}.project.kanbanSetting.showOption", $data->showOption);

            if (common::hasPriv('project', 'kanbanColsColor')) $this->setting->setItem("system.project.kanbanSetting.colorList", json_encode($data->colorList));

            die(js::reload('parent.parent'));
        }

        $this->app->loadLang('task');
        $kanbanSetting = $this->project->getKanbanSetting($projectID);

        $this->view->allCols = $kanbanSetting->allCols;
        $this->view->showOption = $kanbanSetting->showOption;
        $this->view->colorList = $kanbanSetting->colorList;
        $this->view->projectID = $projectID;
        $this->display();
    }

    /**
     * Ajax reset kanban setting
     *
     * @param  int $projectID
     * @param  string $confirm
     * @access public
     * @return void
     */
    public function ajaxResetKanban($projectID, $confirm = 'no')
    {
        if ($confirm != 'yes') die(js::confirm($this->lang->kanbanSetting->noticeReset, inlink('ajaxResetKanban', "projectID=$projectID&confirm=yes")));

        $this->loadModel('setting');

        if (common::hasPriv('project', 'kanbanHideCols') and isset($this->config->project->kanbanSetting->allCols)) {
            $allCols = json_decode($this->config->project->kanbanSetting->allCols, true);
            unset($allCols[$projectID]);
            $this->setting->setItem("system.project.kanbanSetting.allCols", json_encode($allCols));
        }

        $account = $this->app->user->account;
        $this->setting->deleteItems("owner={$account}&module=project&section=kanbanSetting&key=showOption");

        if (common::hasPriv('project', 'kanbanColsColor')) $this->setting->deleteItems("owner=system&module=project&section=kanbanSetting&key=colorList");

        die(js::reload('parent.parent'));
    }

    public function projectBlueprint($projectID)
    {
        $project = $this->project->getById($projectID, true);
        if (!$project) die(js::error($this->lang->notFound) . js::locate('back'));

        $products = $this->project->getProducts($project->id);

        /* Set menu. */
        $this->project->setMenu($this->projects, $project->id);

        $this->view->title = $this->lang->project->view;
        $this->view->position[] = html::a($this->createLink('project', 'browse', "projectID=$projectID"), $project->name);
        $this->view->position[] = $this->view->title;

        $this->view->project = $project;
        $this->view->products = $products;
        $this->view->branchGroups = $this->loadModel('branch')->getByProducts(array_keys($products));
        $this->view->groups = $this->loadModel('group')->getPairs();
        $this->view->actions = $this->loadModel('action')->getList('project', $projectID);
        $this->view->users = $this->loadModel('user')->getPairs('noletter');

        $this->view->depts = $this->loadModel('dept')->getOptionMenu();

        $this->app->user->currentPrj = $projectID;

        // oscar
//        $milestones = $this->dao->select('id, name')->from(TABLE_TASKMILESTONE)
//            ->where('project')->eq($this->view->project->id)
//            ->orderBy('id desc')
//            ->fetchPairs('id');
//        $milestones[0] = '无';
        $this->view->milestones = $this->project->getMilestonesPairs($projectID, 'none');
        // oscar

        /*
        $tasks = $this->dao->select()
            ->from(TABLE_TASK)
            ->where('deleted')->eq(0)
            ->andWhere('project')->eq($projectID)
            //->markRight(1)
            ->orderBy('id asc')
            ->fetchAll();
        $this->tasks = $tasks;
        //*/
        $this->tasks = array();


        $this->view->browseType = 'projectBlueprint';
        $this->view->moduleID = 'project';
        $this->view->projectID = $projectID;
        //$this->view->productID = $productID;

        //var_dump($tasks);
        //$this->grouptask($project->id, 'assignedTo');
        $this->display();
    }


    public function taskmilestone($projectID = 0)
    {
        $project = $this->project->getById($projectID, true);
        if (!$project) die(js::error($this->lang->notFound) . js::locate('back'));
        $this->project->setMenu($this->projects, $project->id);
        $this->view->project = $project;

        $this->view->msg = "";

        if (!empty($_POST)) {
            $version = fixer::input('post')->get()->version;

            if (empty($version) || $version == '') {
                $this->view->msg = "不能添加空的里程碑";
            } else {
                $dat = array();
                $dat['name'] = $version;
                $dat['active'] = 1;
                $dat['deadline'] = helper::today();
                $dat['project'] = $projectID;

                $c = $this->dao->select()->from(TABLE_TASKMILESTONE)
                    ->where('name')->eq($version)
                    ->count();

                if ($c == 0) {
                    $this->dao->insert(TABLE_TASKMILESTONE)->data($dat)
                        ->autoCheck()
                        ->batchCheck('name', 'notempty')
                        ->exec();
                    $this->view->msg = "版本[$version]添加成功!";
                } else {
                    $this->view->msg = "版本[$version]已经存在!";
                }
            }
        }

        $versions = $this->dao->select()->from(TABLE_TASKMILESTONE)
            ->where('project')->eq($projectID)
            ->orderBy('id desc')
            ->fetchAll();

        $this->view->versions = ($versions);

        /*
        error_log("oscar ver cnt:" . count($versions));
        foreach (array_keys($versions) as $v) {
            error_log("oscar ver:" . $v);
        }
        //*/

        $this->display();
    }

    public function activeVersion()
    {
        if (!empty($_POST)) {

            $postVals = fixer::input('post')
                ->get();

            $id = $postVals->id;

            //error_log("oscar: active_version $id");
            $this->dao->update(TABLE_TASKMILESTONE)
                ->set('active')->eq(1)
                ->where('id')->eq($id)
                ->exec();
        } else {
            return false;
        }
    }

    public function closeVersion()
    {
        if (!empty($_POST)) {

            $postVals = fixer::input('post')
                ->get();

            $id = $postVals->id;

            //error_log("oscar: close_version $id");
            $this->dao->update(TABLE_TASKMILESTONE)
                ->set('active')->eq(0)
                ->where('id')->eq($id)
                ->exec();
        } else {
            return false;
        }
    }

    public function updateVersionDeadline()
    {
        if (!empty($_POST)) {

            $postVals = fixer::input('post')
                ->get();

            $deadline = $postVals->deadline;
            $id = $postVals->id;

            //error_log("oscar: updateVersionDeadline $id deadline:$deadline");
            $this->dao->update(TABLE_TASKMILESTONE)
                ->set('deadline')->eq($deadline)
                ->where('id')->eq($id)
                ->exec();
        } else {
            return false;
        }
    }

    public function batchChangeVersion()
    {
        if(!empty($_POST))
        {
            //error_log( var_export($_POST));

            $milestone  = $this->post->changeVersion;
            $taskIDList = $this->post->taskIDList;
            $taskIDList = array_unique($taskIDList);
            //unset($_POST['taskIDList']);
            if(!is_array($taskIDList)) die(js::locate($this->createLink('gametaskinternal', 'mydept', ""), 'parent'));

            /*
            $msg = '';
            foreach ($taskIDList as $item) {$msg .= $item . "  ";}
            echo js::alert("batchChangeVersion: $msg");
            echo $msg;
            //*/

            foreach($taskIDList as $taskID)
            {
                $this->dao->update('zt_task')
                    ->set('milestone')->eq($milestone)
                    ->where('id')->eq($taskID)->exec();

                //$this->loadModel('action');
                //$changes = $this->task->assign($taskID);
                if(dao::isError()) die(js::error(dao::getError()));
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



    public function productMilestone($projectID = 0, $orderBy = 'order_desc', $type = 'byMilestone', $param = 0, $recTotal = 0, $recPerPage = 50, $pageID = 1)
    {
//        $this->task($projectID);
//        return;
        // oscar
        $milestones = $this->project->getMilestonesPairs($projectID);
        $this->view->milestones = $milestones;
        // oscar

        /* Load these models. */
        $this->loadModel('story');
        $this->loadModel('user');
        $this->app->loadLang('testcase');

        $this->project->getLimitedProject();

        /* Save session. */
        $this->app->session->set('storyList', $this->app->getURI(true));
        $this->app->session->set('mileStoneParamater', $param);
        $this->app->session->set('mileStoneQueryType', $type);

        /* Process the order by field. */
        if (!$orderBy) $orderBy = $this->cookie->projectStoryOrder ? $this->cookie->projectStoryOrder : 'pri';
        setcookie('projectStoryOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);

        /* Append id for secend sort. */
        $sort = $this->loadModel('common')->appendOrder($orderBy);

        $queryID = ($type == 'bySearch') ? (int)$param : 0;
        $project = $this->commonAction($projectID);
        $projectID = $project->id;

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $stories = $this->story->getProjectStories($projectID, $sort, $type, $param, $pager);
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'story', false);
        $users = $this->user->getPairs('noletter');

        /* Get project's product. */
        $productID = 0;
        $productPairs = $this->loadModel('product')->getProductsByProject($projectID);
        if ($productPairs) $productID = key($productPairs);

        /* Build the search form. */
        $modules = array();
        $projectModules = $this->loadModel('tree')->getTaskTreeModules($projectID, true);
        $products = $this->project->getProducts($projectID);
        foreach ($products as $product) {
            $productModules = $this->tree->getOptionMenu($product->id);
            foreach ($productModules as $moduleID => $moduleName) {
                if ($moduleID and !isset($projectModules[$moduleID])) continue;
                $modules[$moduleID] = ((count($products) >= 2 and $moduleID) ? $product->name : '') . $moduleName;
            }
        }
        $actionURL = $this->createLink('project', 'story', "projectID=$projectID&orderBy=$orderBy&type=bySearch&queryID=myQueryID");
        $branchGroups = $this->loadModel('branch')->getByProducts(array_keys($products), 'noempty');
        $this->project->buildStorySearchForm($products, $branchGroups, $modules, $queryID, $actionURL, 'projectStory');

        /* Header and position. */
        $title = $project->name . $this->lang->colon . $this->lang->project->story;
        $position[] = html::a($this->createLink('project', 'browse', "projectID=$projectID"), $project->name);
        $position[] = $this->lang->project->story;

        /* Count T B C */
        $storyIdList = array_keys($stories);
        $storyTasks = $this->loadModel('task')->getStoryTaskCounts($storyIdList, $projectID);
        $storyBugs = $this->loadModel('bug')->getStoryBugCounts($storyIdList, $projectID);
        $storyCases = $this->loadModel('testcase')->getStoryCaseCounts($storyIdList);

        if($type == 'byMilestone')
        {
            $this->view->milestone = $param;
        }
        else
        {
            $this->view->milestone = 0;
        }

        $this->view->browseType = 'productMilestone';
        $this->view->moduleID = 'project';
        $this->view->projectID = $projectID;

        /* Assign. */
        $this->view->title = $title;
        $this->view->position = $position;
        $this->view->productID = $productID;
        $this->view->project = $project;
        $this->view->stories = $stories;
        $this->view->summary = $this->product->summary($stories);
        $this->view->orderBy = $orderBy;
        $this->view->type = $type;
        $this->view->param = $param;
        //$this->view->moduleTree = $this->loadModel('tree')->getProductMilestoneTreeMenu($projectID, $startModuleID = 0, array('treeModel', 'createProjectMilestoneStoryLink'));
        $this->view->tabID = 'story';
        $this->view->storyTasks = $storyTasks;
        $this->view->storyBugs = $storyBugs;
        $this->view->storyCases = $storyCases;
        $this->view->users = $users;
        $this->view->pager = $pager;
        $this->view->branchGroups = $branchGroups;

        //oscar:
        $this->loadModel('dept');
        $this->dept->setupDeptUsers($this->view, $this->app->user->account, $this->app->user->dept);

        $this->loadModel('pipeline');
        $this->pipeline->setupOptionMenu($this->view);

        $milestoneId = $this->view->milestone;
        //$milestoneData = $this->project->getMilestoneById($milestoneId);
        $milestoneData = $this->dao->findById((int)$milestoneId)->from(TABLE_PRODUCTMILESTONE)->fetch();
        $this->view->milestoneData = $milestoneData;
        //var_dump($milestoneData);
        //oscar:

        $this->display();
    }


    public function productMilestonesManage($projectID = 0)
    {
        $project = $this->project->getById($projectID, true);
        if (!$project) die(js::error($this->lang->notFound) . js::locate('back'));
        $this->project->setMenu($this->projects, $project->id);
        $this->view->project = $project;

        $this->view->msg = "";

        if (!empty($_POST)) {
            $milestone = $this->post->milestone;

            if(empty($milestone) || $milestone === '')
            {
                $this->msg = "不能添加空的里程碑";
            }
            else {
                $dat = array();
                $dat['name'] = $milestone;
                $dat['active'] = 1;
                $dat['project'] = $projectID;
                $dat['deadlineRequirement'] = $this->post->deadlineRequirement;
                $dat['deadline'] = $this->post->deadline;
                $dat['deadlineQA'] = $this->post->deadlineQA;
                $dat['opOnlineDate'] = $this->post->opOnlineDate;

                $c = $this->dao->select()->from(TABLE_PRODUCTMILESTONE)
                    ->where('name')->eq($milestone)
                    ->count();

                if ($c == 0) {
                    $this->dao->insert(TABLE_PRODUCTMILESTONE)->data($dat)
                        ->autoCheck()
                        ->batchCheck('name', 'notempty')
                        ->exec();
                    $this->view->msg = "里程碑[$milestone]添加成功!";
                } else {
                    $this->view->msg = "里程碑[$milestone]已经存在!";
                }
            }
        }

        $milestones = $this->dao->select()->from(TABLE_PRODUCTMILESTONE)
            ->where('project')->eq($projectID)
            ->orderBy('id desc')
            ->fetchAll();

        $this->view->browseType = 'productMilestonesManage';
        $this->view->moduleID = 'project';
        $this->view->projectID = $projectID;

        $this->view->milestones = ($milestones);

        $this->display();
    }



    public function activeMilestone()
    {
        if (!empty($_POST)) {

            $postVals = fixer::input('post')
                ->get();

            $id = $postVals->id;

            //error_log("oscar: active_version $id");
            $this->dao->update(TABLE_PRODUCTMILESTONE)
                ->set('active')->eq(1)
                ->where('id')->eq($id)
                ->exec();
        } else {
            return false;
        }
    }

    public function closeMilestone()
    {
        if (!empty($_POST)) {

            $postVals = fixer::input('post')
                ->get();

            $id = $postVals->id;

            //error_log("oscar: close_version $id");
            $this->dao->update(TABLE_PRODUCTMILESTONE)
                ->set('active')->eq(0)
                ->where('id')->eq($id)
                ->exec();
        } else {
            return false;
        }
    }

    public function updateMilestoneDeadline()
    {
        if (!empty($_POST)) {

            $postVals = fixer::input('post')
                ->get();

            $deadline = $postVals->deadline;
            $id = $postVals->id;

            //error_log("oscar: updateVersionDeadline $id deadline:$deadline");
            $this->dao->update(TABLE_PRODUCTMILESTONE)
                ->set('deadline')->eq($deadline)
                ->where('id')->eq($id)
                ->exec();
        } else {
            return false;
        }
    }


    /**
     * Link stories to a Milestone.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function linkMilestoneStory($projectID = 0, $milestone, $browseType = '', $param = 0)
    {
        $this->loadModel('story');
        $this->loadModel('product');

        $milestoneName = $this->dao->select('name')->from(TABLE_PRODUCTMILESTONE)
            ->where('id')->eq($milestone)
            ->fetch('name');

        //$query = $this->dao->get();
        //$this->console_log($query);

        /* Get projects and products. */
        $project = $this->project->getById($projectID);
        $products = $this->project->getProducts($projectID);
        $browseLink = $this->createLink('project', 'linkMilestoneStory', "projectID=$projectID&milestone=$milestone");

        //$this->session->set('storyList', $this->app->getURI(true)); // Save session.
        $this->project->setMenu($this->projects, $project->id);     // Set menu.

        if (empty($products)) {
            echo js::alert($this->lang->project->errorNoLinkedProducts);
            die(js::locate($this->createLink('project', 'manageproducts', "projectID=$projectID")));
        }

        if (!empty($_POST)) {
            $this->project->linkMilestoneStory($projectID, $milestone);
            die(js::locate($browseLink));
        }

        $queryID = ($browseType == 'bySearch') ? (int)$param : 0;

        /* Set modules and branches. */
        $modules = array();
        $branches = array();
        $productType = 'normal';
        $this->loadModel('tree');
        $this->loadModel('branch');
        foreach ($products as $product) {
            $productModules = $this->tree->getOptionMenu($product->id);
            foreach ($productModules as $moduleID => $moduleName) $modules[$moduleID] = ((count($products) >= 2 and $moduleID != 0) ? $product->name : '') . $moduleName;
            if ($product->type != 'normal') {
                $productType = $product->type;
                $branches[$product->branch] = $product->branch;
                if ($product->branch == 0) {
                    foreach ($this->branch->getPairs($product->id, 'noempty') as $branchID => $branchName) $branches[$branchID] = $branchID;
                }
            }
        }

        /* Build the search form. */
        $actionURL = $this->createLink('project', 'linkMilestoneStory', "projectID=$projectID&browseType=bySearch&queryID=myQueryID");
        $branchGroups = $this->loadModel('branch')->getByProducts(array_keys($products), 'noempty');
        $this->project->buildStorySearchForm($products, $branchGroups, $modules, $queryID, $actionURL, 'linkMilestoneStory');

        if ($browseType == 'bySearch') {
            $allStories = $this->story->getBySearch('', $queryID, 'id', null, $projectID);
        } else {
            $allStories = $this->story->getProductStories(array_keys($products), $branches, $moduleID = '0', $status = 'active');
        }
        $prjStories = $this->story->getProjectStories($projectID);

        /* Assign. */
        $title = '工程：' . $project->name . "   里程碑：" . $milestoneName . '  ' . $this->lang->project->linkMilestoneStory;
        $position[] = html::a($browseLink, $project->name);
        $position[] = $this->lang->project->linkMilestoneStory;

        $milestoneStories = $this->project->getMilestonesStories($projectID, $milestone, 'all');
        $this->view->milestoneStories = $milestoneStories;

        /* Count T B C */
        $storyIdList = array_keys($prjStories);

        $storyTasks = $this->loadModel('task')->getStoryTaskCounts($storyIdList, $projectID);
        $storyBugs = $this->loadModel('bug')->getStoryBugCounts($storyIdList, $projectID);
        $storyCases = $this->loadModel('testcase')->getStoryCaseCounts($storyIdList);

        $this->view->storyTasks = $storyTasks;
        $this->view->storyBugs = $storyBugs;
        $this->view->storyCases = $storyCases;

        $this->view->title = $title;
        $this->view->position = $position;
        $this->view->project = $project;
        $this->view->products = $products;
        $this->view->allStories = $allStories;
        $this->view->prjStories = $prjStories;
        $this->view->browseType = $browseType;
        $this->view->productType = $productType;
        $this->view->modules = $modules;
        $this->view->users = $this->loadModel('user')->getPairs('noletter');
        $this->view->branchGroups = $branchGroups;
        $this->display();
    }

    /**
     * Unlink a story from Milestone.
     *
     * @param  int $projectID
     * @param  int $storyID
     * @param  string $confirm yes|no
     * @access public
     * @return void
     */
    public function unlinkMilestoneStory($projectID, $storyID, $milestone, $confirm = 'no')
    {
        if ($confirm == 'no') {
            die(js::confirm($this->lang->project->confirmUnlinkStory, $this->createLink('project', 'unlinkstory', "projectID=$projectID&storyID=$storyID&confirm=yes")));
        } else {
            $this->project->unlinkStory($projectID, $storyID);

            /* if kanban then reload and if ajax request then send result. */
            if (isonlybody()) {
                die(js::reload('parent'));
            } elseif (helper::isAjaxRequest()) {
                if (dao::isError()) {
                    $response['result'] = 'fail';
                    $response['message'] = dao::getError();
                } else {
                    $response['result'] = 'success';
                    $response['message'] = '';
                }
                $this->send($response);
            }
            die(js::locate($this->app->session->storyList, 'parent'));
        }
    }

    /**
     * batch unlink story.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function batchUnlinkMilestoneStory($projectID, $milestone)
    {
        if (isset($_POST['storyIDList'])) {
            $storyIDList = $this->post->storyIDList;
            $_POST = array();
            foreach ($storyIDList as $storyID) {
                $this->project->unlinkMilestoneStory($projectID, $milestone, $storyID);
            }
        }

        //if (!dao::isError()) $this->loadModel('score')->create('ajax', 'batchOther');
        // productMilestone-2-1-byMilestone-2.html
        //public function productMilestone($projectID = 0, $orderBy = 'order_desc', $type = 'byMilestone', $param = 0, $recTotal = 0, $recPerPage = 50, $pageID = 1)
        die(js::locate($this->createLink('project', 'productMilestone', "projectID=$projectID&orderby=desc&type=byMilestone&param=$milestone")));
    }



    /**
     * get data to export
     *
     * @param  int $productID
     * @param  string $orderBy
     * @access public
     * @return void
     */
    public function exportProgress($productID, $projectID, $orderBy)
    {
        //error_log("XXXXXXX: project.control.exportProgress product:$productID order:$orderBy post:$this->post");

        /* format the fields of every story in order to export data. */
        if($_POST)
        {
            //error_log("XXXXXXX: project.control.exportProgress.post product:$productID order:$orderBy post:". print_r($_POST, 1));

            $this->loadModel('story');
            $this->loadModel('user');
            $this->loadModel('project');

            $param = $this->app->session->mileStoneParamater;
            $type = $this->app->session->mileStoneQueryType;

            $milestones = $this->project->getMilestonesPairs($projectID);

            /* Process the order by field. */
            if (!$orderBy) $orderBy = $this->cookie->projectStoryOrder ? $this->cookie->projectStoryOrder : 'pri';
            setcookie('projectStoryOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);

            /* Append id for secend sort. */
            $sort = $this->loadModel('common')->appendOrder($orderBy);

            $queryID = 0;//($type == 'bySearch') ? (int)$param : 0;
            $project = $this->commonAction($projectID);
            $projectID = $project->id;


            /* Load pager. */
            //$this->app->loadClass('pager', $static = true);
            //$pager = new pager($recTotal, $recPerPage, $pageID);

            $stories = $this->story->getProjectStories($projectID, $sort, $type, $param, null);
            //error_log("=========");
            //error_log(print_r($stories, 1));
            //error_log("=========");

            $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'story', false);
            $users = $this->user->getPairs('noletter');

            //error_log("=========");
            //error_log(print_r($users, 1));
            //error_log("=========");

            /* Get project's product. */
            $productID = 0;
            $productPairs = $this->loadModel('product')->getProductsByProject($projectID);
            if ($productPairs) $productID = key($productPairs);

            /* Build the search form. */
            $modules = array();
            $projectModules = $this->loadModel('tree')->getTaskTreeModules($projectID, true);
            $products = $this->project->getProducts($projectID);
            foreach ($products as $product) {
                $productModules = $this->tree->getOptionMenu($product->id);
                foreach ($productModules as $moduleID => $moduleName) {
                    if ($moduleID and !isset($projectModules[$moduleID])) continue;
                    $modules[$moduleID] = ((count($products) >= 2 and $moduleID) ? $product->name : '') . $moduleName;
                }
            }
            $actionURL = $this->createLink('project', 'story', "projectID=$projectID&orderBy=$orderBy&type=bySearch&queryID=myQueryID");
            $branchGroups = $this->loadModel('branch')->getByProducts(array_keys($products), 'noempty');
            $this->project->buildStorySearchForm($products, $branchGroups, $modules, $queryID, $actionURL, 'projectStory');

            /* Header and position. */
            $title = $project->name . $this->lang->colon . $this->lang->project->story;
            $position[] = html::a($this->createLink('project', 'browse', "projectID=$projectID"), $project->name);
            $position[] = $this->lang->project->story;

            /* Count T B C */
            $storyIdList = array_keys($stories);
            $storyTasks = $this->loadModel('task')->getStoryTaskCounts($storyIdList, $projectID);
            $storyBugs = $this->loadModel('bug')->getStoryBugCounts($storyIdList, $projectID);
            $storyCases = $this->loadModel('testcase')->getStoryCaseCounts($storyIdList);

            $projectLang = $this->lang->story;
            $projectConfig = $this->config->project;

            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $projectConfig->list->exportProgressFields);
            foreach ($fields as $key => $fieldName) {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = zget($projectLang, $fieldName);
                unset($fields[$key]);
            }

            foreach($stories as $story)
            {
                //$story->openedBy = $users[$story->openedBy] . "测试";
                $story->assignedTo = $users[$story->assignedTo];
                $story->openedBy = $users[$story->openedBy];
                $story->deadline = helper::today();
                $story->taskProgress = $story->taskProgress . "%";
                $story->storyProgress = $story->storyProgress . "%";

                $story->openedDate = date('Y-m-d', strtotime($story->openedDate));
                $story->assignedDate = date('Y-m-d', strtotime($story->assignedDate));

                $storyTsks = $this->task->getStoryTasks($story->id, $projectID);

                //error_log("========= storyTsks");
                //error_log(print_r($storyTsks, 1));

                $deadline = date("Y-m-d", strtotime("1000-01-01"));
                foreach ($storyTsks as $tsk) {
                    $tskDdline = date("Y-m-d", strtotime($tsk->deadline));
                    if($tskDdline > $deadline)
                    {
                        //error_log("task deadline: $tskDdline orig:$tsk->deadline");
                        $deadline = $tsk->deadline;
                    }
                }

                $story->deadline = $deadline;
                //error_log("stroy $story->id deadline:" . $deadline);

                if(isset($storyTasks[$story->id]))     $story->taskCountAB = $storyTasks[$story->id];
                if(isset($storyBugs[$story->id]))      $story->bugCountAB  = $storyBugs[$story->id];
                //if(isset($storyCases[$story->id]))     $story->caseCountAB = $storyCases[$story->id];
            }


            //error_log( $stories);
            // write(print_r($stories, 1));
            //error_log("========= storyTasks");
            //error_log(print_r($stories, 1));
            //error_log(print_r($storyTasks, 1));
            //error_log("=========");


            $this->post->set('fields', $fields);
            $this->post->set('rows', $stories);
            $this->post->set('kind', 'story');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->allExportFields = $this->config->project->list->exportFields;
        $this->view->customExport    = false;
        $this->display();
    }

    public function editMilestone($projectId, $milestoneId)
    {
        if (!empty($_POST)) {
            $changes = $this->project->updateMilestone($projectId, $milestoneId);
            if (dao::isError()) die(js::error(dao::getError()));
            if ($changes) {
                $actionID = $this->loadModel('action')->create('project', $projectId, 'edited');
                $this->action->logHistory($actionID, $changes);
            }
            die(js::locate($this->createLink('project', 'productMilestonesManage', "projectID=$projectId"), 'parent'));
        }

        $this->view->browseType = 'productMilestone';
        $this->view->moduleID = 'project';
        $this->view->projectID = $projectId;
        $milestoneData = $this->dao->findById((int)$milestoneId)->from(TABLE_PRODUCTMILESTONE)->fetch();
        $this->view->milestone = $milestoneData;//$this->project->getMilestoneById($milestoneId);
        $this->display();
    }

    public function createMilestone($projectId)
    {
        if (!empty($_POST)) {
            $changes = $this->project->createMilestone($projectId);
            die(js::locate($this->createLink('project', 'productMilestonesManage', "projectID=$projectId"), 'parent'));
        }

        $this->view->browseType = 'productMilestone';
        $this->view->moduleID = 'project';
        $this->view->projectID = $projectId;
        $this->display();
    }
}
