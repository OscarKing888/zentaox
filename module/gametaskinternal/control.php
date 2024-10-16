<?php

/**
 * The control file of gametaskinternal module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
class gametaskinternal extends control
{
    /**
     * The construct function.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->app->loadLang('index');

        $this->loadModel('dept');
        $this->loadModel('file');
        $this->loadModel('product');
        $this->loadModel('project');
        $this->loadModel('user');
        $this->loadModel('group');
        $this->loadModel('datatable');
        $this->loadModel('search');
        $this->loadModel('task');
    }

    function convertImageURL($tasks)
    {
        foreach ($tasks as $task) {
            $this->convertImageURLTask($task);
        }
        return $tasks;
    }

    function convertImageURLTask($task)
    {
        $task = $this->file->replaceImgURL($task, "desc");
        $task->desc = htmlspecialchars_decode($task->desc);

        $task = $this->file->replaceImgURL($task, "srcResPath");
        $task->srcResPath = htmlspecialchars_decode($task->srcResPath);

        $task = $this->file->replaceImgURL($task, "gameResPath");
        $task->gameResPath = htmlspecialchars_decode($task->gameResPath);
        return $task;
    }


    /**
     * The index page of gametaskinternal module.
     *
     * @access public
     * @return void
     */
    public function index($orderBy='id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 0)
    {
        $this->view->tools = $this->config->gametaskinternal->toolsIndex;
        $this->view->customFieldsName = "indexField";
        $this->setupViewTasks($orderBy, $recTotal, $recPerPage, $pageID);
        $this->setupCommonViewVars();
        $this->display();
    }

    public function statbydept($matchVer)
    {
        $this->view->tools = $this->config->gametaskinternal->toolsIndex;
        $this->view->customFieldsName = "indexField";
        $this->setupViewTasks(0, 0, 0, 0,
            -1, '', '', 0,
            -1,-1,  $matchVer);
        $this->setupCommonViewVars();
        $this->display();
    }


    public function details($orderBy='id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 0, $matchVer = '')
    {
        $this->view->tools = $this->config->gametaskinternal->toolsDetails;
        $this->view->customFieldsName = "defaultField";
        $this->view->methodName = "details";
        $this->setupViewTasks($orderBy, $recTotal, $recPerPage, $pageID,
            -1, '', '', 0,
            -1,-1,  $matchVer);
        $this->setupCommonViewVars();

        $this->display();
    }

    public function mytasks($orderBy='pri_asc', $recTotal = 0, $recPerPage = 20, $pageID = 0, $matchVer = '')
    {
        $this->view->tools = $this->config->gametaskinternal->toolsMyTask;
        $this->view->customFieldsName = "ownerTaskField";
        $this->view->methodName = "mytasks";
        $this->setupViewTasks($orderBy, $recTotal, $recPerPage, $pageID,
            -1, $this->app->user->account, '', 0,
            -1,-1,  $matchVer);
        $this->setupCommonViewVars();
        $this->display();
    }

    public function assignedtome($orderBy='pri_asc', $recTotal = 0, $recPerPage = 20, $pageID = 0, $matchVer = '')
    {
        $this->view->tools = $this->config->gametaskinternal->toolsAssignedToMe;
        $this->view->customFieldsName = "myTaskField";
        $this->view->methodName = "assignedtome";
        $this->setupViewTasks($orderBy, $recTotal, $recPerPage, $pageID,
            -1, '', $this->app->user->account, 0,
            -1,-1,  $matchVer);
        $this->setupCommonViewVars();
        $this->display();
    }


    public function mydept($orderBy='pri_asc', $recTotal = 0, $recPerPage = 20, $pageID = 1, $matchVer = '')
    {
        $this->view->tools = $this->config->gametaskinternal->toolsMyDept;
        $this->view->customFieldsName = "myDeptTaskField";
        $this->view->methodName = "mydept";
        $this->setupViewTasksMyDept($orderBy, $recTotal, $recPerPage, $pageID,
            $this->app->user->dept, '', '', 0,
            -1,-1,  $matchVer);
        $this->setupCommonViewVars();

        $this->display();
    }

    public function completedlist($orderBy='id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 0, $matchVer = '')
    {
        $this->view->tools = $this->config->gametaskinternal->toolsCompletedlist;
        $this->view->customFieldsName = "completedField";
        $this->view->methodName = "completedlist";
        $this->setupViewTasks($orderBy, $recTotal, $recPerPage, $pageID,
            -1, '', '', 0,
            1,-1,  $matchVer);

        $this->setupCommonViewVars();
        $this->display();
    }

    public function incompletelist($orderBy='id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 0, $matchVer = '')
    {
        $this->view->tools = $this->config->gametaskinternal->toolsIncompletedlist;
        $this->view->customFieldsName = "indexField";
        $this->view->methodName = "incompletelist";
        $this->setupViewTasks($orderBy, $recTotal, $recPerPage, $pageID,
            -1, '', '', 0,
            0,-1,  $matchVer);
        $this->setupCommonViewVars();
        $this->display();
    }

    public function unassigned($orderBy='id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 0, $matchVer = '')
    {
        $this->view->tools = $this->config->gametaskinternal->toolsIncompletedlist;
        $this->view->customFieldsName = "indexField";
        $this->view->methodName = "unassigned";
        $this->setupViewTasks($orderBy, $recTotal, $recPerPage, $pageID,
            -1, '', $this->config->gametaskinteranl->unassigned, 0,
            -1,-1,  $matchVer);
        $this->setupCommonViewVars();
        $this->display();
    }

    public function restore($orderBy='id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 0, $matchVer = '')
    {
        $this->view->tools = $this->config->gametaskinternal->toolsRestore;
        $this->view->customFieldsName = "defaultField";
        $this->view->methodName = "restore";
        $this->setupViewTasks($orderBy, $recTotal, $recPerPage, $pageID,
            -1, '', '', 1,
            -1,-1,  $matchVer);
        $this->setupCommonViewVars();
        $this->display();
    }

    public function view($id)
    {
        $gameTask = $this->dao->select()->from(TABLE_GAMETASKINTERNAL)
            ->where('id')->eq($id)
            ->fetch();

        $gameTask = $this->convertImageURLTask($gameTask);
        $this->view->task = $gameTask;

        $versions = $this->dao->select('id,name')->from(TABLE_GAMETASKINTERNALVERSION)
            ->where('active')->eq(1)
            ->orderBy('id desc')
            ->fetchPairs();

        $this->view->versions = $versions;

        $this->setupCommonViewVars();
        $this->view->deptUsers = $this->user->getPairs('nodeleted|noclosed|noletter');
        $this->view->allUsers = $this->user->getPairs('nodeleted|noclosed|noletter');
        $this->display();
    }

    public function edit($id)
    {
        if(!empty($_POST)) {
            $task = fixer::input('post')->specialchars($this->config->gametaskinternal->editFields)
                ->add('lastUpdateBy', $this->app->user->account)
                ->add('lastUpdateDate', helper::now())
                ->stripTags($this->config->gametaskinternal->editor->edit['id'], $this->config->allowedTags)
                ->get();

            $this->dao->update(TABLE_GAMETASKINTERNAL)->data($task)
                ->batchCheck('version,dept,title,owner,product', 'notempty')
                ->where('id')->eq((int)$id)->exec();

            die(js::locate($this->createLink('gametaskinternal', 'view', "id=$id"), 'parent'));
        }

        $gameTask = $this->dao->select()->from(TABLE_GAMETASKINTERNAL)
            ->where('id')->eq($id)
            ->fetch();

        $gameTask = $this->convertImageURLTask($gameTask);
        $this->view->task = $gameTask;

        $versions = $this->dao->select('id,name')->from(TABLE_GAMETASKINTERNALVERSION)
            ->where('active')->eq(1)
            ->orderBy('id desc')
            ->fetchPairs();

        $this->view->versions = $versions;

        $this->setupCommonViewVars();

        $this->view->deptUsers = $this->user->getPairs('nodeleted|noclosed');
        $this->display();
    }

    public function setupViewTasks($orderBy='id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 0,
                                   $matchDept = -1, $matchOwner ='',  $matchAssignedTo='', $matchDeleted = 0,
                                   $matchCompleted = -1, $matchClosed = -1, $matchVer = '')
    {
        //$this->view->debugStr +=  $this->menu;
        if(!$orderBy) $orderBy = $this->cookie->gameinternalTaskOrder ? $this->cookie->gameinternalTaskOrder : 'id_desc';
        setcookie('gameinternalTaskOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);

        $sort = $this->loadModel('common')->appendOrder($orderBy);
        //$sort = $orderBy;
        //$sort = str_replace('desc asc', "\'desc\' asc", $sort);
        //$sort = str_replace('desc desc', "\'desc\' desc", $sort);

        //error_log("oscar:+++++ orderby:$orderBy  sort:$sort");

        $this->app->loadClass('pager');
        $pager = new pager($recTotal, $recPerPage, $pageID);

        // versions
        $versions = $this->dao->select('id,name')->from(TABLE_GAMETASKINTERNALVERSION)
            ->where('active')->eq(1)
            ->orderBy('id desc')
            ->fetchPairs();

        $this->view->versions = $versions;

        //foreach (array_keys($versions) as $k) { error_log("oscar: version:k $k");  }

        $gameTasks = $this->dao->select()->from(TABLE_GAMETASKINTERNAL)
            ->beginIF($matchVer == '')->where('version')->in(array_keys($versions))->fi()
            ->beginIF($matchVer != '')->where('version')->eq($matchVer)->fi()
            ->beginIF($matchDeleted >= 0 )->andWhere('deleted')->eq($matchDeleted)->fi()
            ->beginIF($matchDept >= 0 )->andWhere('dept')->eq($matchDept)->fi()
            ->beginIF($matchCompleted >= 0 )->andWhere('completed')->eq($matchCompleted)->fi()
            ->beginIF($matchClosed >= 0 )->andWhere('closed')->eq($matchClosed)->fi()
            ->beginIF($matchOwner != '')->andWhere('owner')->eq($matchOwner)->fi()
            ->beginIF($matchAssignedTo == $this->config->gametaskinteranl->unassigned)->andWhere('assignedTo')->eq('')->fi()
            ->beginIF($matchAssignedTo != '' && $matchAssignedTo != $this->config->gametaskinteranl->unassigned)->andWhere('assignedTo')->eq($matchAssignedTo)->fi()
            //->groupBy('version')
            ->orderBy($sort)
            ->page($pager)
            ->fetchAll();

        $gameTasks = $this->convertImageURL($gameTasks);
        $this->view->gameTasks = $gameTasks;

        $this->session->set('taskOrderBy', $sort);
        $this->view->orderBy       = $orderBy;
        $this->view->pager = $pager;
        $this->view->recTotal      = $pager->recTotal;
        $this->view->recPerPage    = $pager->recPerPage;
    }

    public function setupViewTasksMyDept($orderBy='id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 0,
                                   $matchDept = -1, $matchOwner ='',  $matchAssignedTo='', $matchDeleted = 0,
                                   $matchCompleted = -1, $matchClosed = -1, $matchVer = '')
    {
        $leaders = $this->dao->select('dept,username')->from(TABLE_GAMEGROUPLEADERS)
            ->orderBy('dept asc')
            ->fetchPairs();

        $proxyDepts = array();
        foreach ($leaders as $key => $leader) {
            if($leader == $this->app->user->account)
            {
                $proxyDepts[$key] = $key;
            }
        }


        //$this->view->debugStr +=  $this->menu;
        if(!$orderBy) $orderBy = $this->cookie->gameinternalTaskOrder ? $this->cookie->gameinternalTaskOrder : 'id_desc';
        setcookie('gameinternalTaskOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);
        $sort = $this->loadModel('common')->appendOrder($orderBy);

        $this->app->loadClass('pager');
        $pager = new pager($recTotal, $recPerPage, $pageID);

        // versions
        $versions = $this->dao->select('id,name')->from(TABLE_GAMETASKINTERNALVERSION)
            ->where('active')->eq(1)
            ->orderBy('id desc')
            ->fetchPairs();

        $this->view->versions = $versions;

        //foreach (array_keys($versions) as $k) { error_log("oscar: version:k $k");  }

        $gameTasks = $this->dao->select()->from(TABLE_GAMETASKINTERNAL)
            ->beginIF($matchVer == '')->where('version')->in(array_keys($versions))->fi()
            ->beginIF($matchVer != '')->where('version')->eq($matchVer)->fi()
            ->beginIF($matchDeleted >= 0 )->andWhere('deleted')->eq($matchDeleted)->fi()
            ->beginIF($matchDept >= 0 )->andWhere('dept', true)->eq($matchDept)->orWhere('dept')->in($proxyDepts)->markRight(1)->fi()
            ->beginIF($matchCompleted >= 0 )->andWhere('completed')->eq($matchCompleted)->fi()
            ->beginIF($matchClosed >= 0 )->andWhere('closed')->eq($matchClosed)->fi()
            ->beginIF($matchOwner != '')->andWhere('owner')->eq($matchOwner)->fi()
            ->beginIF($matchAssignedTo != '')->andWhere('assignedTo')->eq($matchAssignedTo)->fi()
            //->groupBy('version')
            ->orderBy($sort)
            ->page($pager)
            ->fetchAll();

        $gameTasks = $this->convertImageURL($gameTasks);
        $this->view->gameTasks = $gameTasks;

        $this->session->set('taskOrderBy', $sort);
        $this->view->orderBy       = $orderBy;
        $this->view->pager = $pager;
        $this->view->recTotal      = $pager->recTotal;
        $this->view->recPerPage    = $pager->recPerPage;
    }

    public function setupCommonViewVars()
    {
        // products
        $this->view->allProducts = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        $depts = $this->dao->select('id,name')->from(TABLE_DEPT)->fetchPairs();

        // depts
        //$this->view->depts = $depts;
        $this->view->depts = $this->dept->getOptionMenu();

        $this->view->dept = $this->app->user->dept;

        // owners
        $this->view->allOwners = $this->getUserByGroupName(GROUPNAME_CQYH);

        // users
        $allUsers = $this->user->getPairs('nodeleted|noclosed');
        $this->view->allUsers = $allUsers;
        $this->view->users = $this->user->getPairs('nodeleted|noclosed|noletter');
        $this->dept->setupDeptUsers($this->view, $this->app->user->account, $this->app->user->dept);
        /*
        $deptUsers = $this->dept->getDeptUserPairs($this->app->user->dept);
        $this->view->user = $this->app->user->account;

        $leaders = $this->dao->select('dept,username')->from(TABLE_GAMEGROUPLEADERS)
            ->orderBy('dept asc')
            ->fetchPairs();


        foreach ($leaders as $key => $leader) {
            if($leader == $this->app->user->account)
            {
                $deptUsers += $this->dept->getDeptUserPairs($key);

                //error_log("oscar: $key : $leader deptUsers:" . count($deptUsers));
                //foreach ($deptUsers as $deptUser) {                    error_log("     oscar:$deptUser");                }
            }
        }

        $deptUsers = array_unique($deptUsers);

        foreach ($deptUsers as $account => $user) {
            $firstLetter = ucfirst(substr($account, 0, 1)) . ':';
            $deptUsers[$account] = $firstLetter . $user;
        }

        $this->view->deptUsers = $deptUsers;
        //*/

        // versions
        $verDeadlines = $this->dao->select('id,deadline')->from(TABLE_GAMETASKINTERNALVERSION)
            ->where('active')->eq(1)
            ->orderBy('id desc')
            ->fetchPairs();
        $this->view->verDeadlines = $verDeadlines;
        //error_log("oscar: deptUsers:" . count($deptUsers));
    }

    public function create()
    {
        $this->view->msg = "";

        if (!empty($_POST)) {
            $newGameTasks = fixer::input('post')->get();
                //->specialchars($this->config->gametaskinternal->fields);
            $batchNum = count($newGameTasks->version);
            //error_log("oscar: batchNum $batchNum count:" . count($newGameTasks));

            $version = '';
            $dept = 0;
            $owner = '';

            for ($i = 0; $i < $batchNum; $i++) {
                if (empty($newGameTasks->title[$i])) {
                    continue;
                }

                $this->view->msg = "添加成功！";

                $version = !isset($newGameTasks->version[$i]) || $newGameTasks->version[$i] == 'ditto' ? $version : $newGameTasks->version[$i];
                $dept = !isset($newGameTasks->dept[$i]) || $newGameTasks->dept[$i] == 'ditto' ? $dept : $newGameTasks->dept[$i];
                $owner = !isset($newGameTasks->owner[$i]) || $newGameTasks->owner[$i] == 'ditto' ? $owner : $newGameTasks->owner[$i];

                $data[$i] = new stdclass();
                $data[$i]->version = $version;
                $data[$i]->dept = $dept;
                $data[$i]->owner = $owner;
                $data[$i]->title = $newGameTasks->title[$i];
                $data[$i]->count = (int)$newGameTasks->count[$i];
                $data[$i]->desc = nl2br($newGameTasks->desc[$i]);
                $data[$i]->srcResPath = nl2br($newGameTasks->srcResPath[$i]);
                $data[$i]->gameResPath = nl2br($newGameTasks->gameResPath[$i]);
                $data[$i]->pri = (int)$newGameTasks->pri[$i];
                $data[$i]->product = (int)$newGameTasks->product;
                $data[$i]->createDate = helper::now();
                $data[$i]->sizeWidth = (int)$newGameTasks->sizeWidth[$i];
                $data[$i]->sizeHeight = (int)$newGameTasks->sizeHeight[$i];

                $this->dao->insert(TABLE_GAMETASKINTERNAL)->data($data[$i])
                    ->autoCheck()
                    ->batchCheck($this->config->gameTaskInternal->create->requiredFields, 'notempty')
                    ->exec();

                if (dao::isError()) {
                    die(js::error(dao::getError()));
                } else {
                }
            }


            //$pipelineID = $this->pipeline->create();
            //if(dao::isError()) die(js::error(dao::getError()) . js::locate('back'));
            //s$jumpLink  = $this->createLink('gametaskinternal', 'index', "");
            //error_log("oscar: $jumpLink");
            //die(js::reload("parent"));
            //die(js::refresh($jumpLink));
            //js::error(dao::getError());
        }

        $gameTasks = array();

        if (count($gameTasks) < $this->config->gameTaskInternal->batchCreate) {
            $paddingCount = $this->config->pipeline->defaultStages - count($gameTasks);
            $newTask = new stdclass();
            //$step->type   = 'item';
            //$step->desc   = '';
            //$step->expect = '';
            for ($i = 1; $i <= $paddingCount; $i++)
                $gameTasks[$i] = $newTask;
        }

        //$gameTasks = $this->convertImageURL($gameTasks);
        $this->view->gameTasks = $gameTasks;

        $this->view->allProducts = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        //$depts = $this->dao->select('id,name')->from(TABLE_DEPT)->fetchPairs();
        //$this->view->depts = $depts;
        $this->view->depts = $this->dept->getOptionMenu();

        $this->view->allOwners = $this->getUserByGroupName(GROUPNAME_CQYH);

        $allUsers = $this->user->getPairs('nodeleted|noclosed');
        $this->view->allUsers = $allUsers;

        $this->view->user = $this->app->user->account;

        $versions = $this->dao->select('id,name')->from(TABLE_GAMETASKINTERNALVERSION)
            ->where('active')->eq(1)
            ->orderBy('id desc')
            ->fetchPairs();

        $this->view->versions = ($versions);

        $this->display();
    }

    public function getUserByGroupName($grpName)
    {
        $groupID = $this->getGroupIDByName($grpName);
        $users = $this->group->getUserPairs($groupID);

        foreach ($users as $account => $user) {
            $firstLetter = ucfirst(substr($account, 0, 1)) . ':';
            $users[$account] = $firstLetter . $user;
        }

        return $users;
    }

    public function getGroupIDByName($grpName)
    {
        $grpId = $this->dao->select()->from(TABLE_GROUP)
            ->where('name')->eq($grpName)
            ->fetch('id');

        //error_log("oscar: getGroupID $grpName = $grpId");
        //$grpId = 28;
        return $grpId;
    }

    public function version()
    {
        $this->view->msg = "";

        if (!empty($_POST)) {
            $version = fixer::input('post')->get()->version;

            $dat = array();
            $dat['name'] = $version;
            $dat['active'] = 1;
            $dat['deadline'] = helper::today();

            $c = $this->dao->select()->from(TABLE_GAMETASKINTERNALVERSION)
                ->where('name')->eq($version)
                ->count();

            if ($c == 0) {
                $this->dao->insert(TABLE_GAMETASKINTERNALVERSION)->data($dat)
                    ->autoCheck()
                    ->batchCheck('name', 'notempty')
                    ->exec();
                $this->view->msg = "版本[$version]添加成功!";
            } else {
                $this->view->msg = "版本[$version]已经存在!";
            }
        }

        $versions = $this->dao->select()->from(TABLE_GAMETASKINTERNALVERSION)
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
            $this->dao->update(TABLE_GAMETASKINTERNALVERSION)
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
            $this->dao->update(TABLE_GAMETASKINTERNALVERSION)
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
            $this->dao->update(TABLE_GAMETASKINTERNALVERSION)
                ->set('deadline')->eq($deadline)
                ->where('id')->eq($id)
                ->exec();
        } else {
            return false;
        }
    }


    public function delete($id)
    {
        //delete(TABLE_BLOG, $id);
        $this->dao->update(TABLE_GAMETASKINTERNAL)
            ->set('deleted')->eq(1)->where('id')->eq($id)->exec();

        //die(js::reload());
        //$this->locate(inlink('index'));
        die(js::reload('parent'));
        //die(js::locate($this->session->taskList, 'parent'));
        //$this->display();
    }

    public function restoreTask($id)
    {
        $this->dao->update(TABLE_GAMETASKINTERNAL)
            ->set('deleted')->eq(0)->where('id')->eq($id)->exec();

        $this->locate(inlink('restore'));
    }

    public function batchAssignTo()
    {
        //$msg = "" . count($_POST). "  ";

        //$msg = $this->post->assignedTo . "  ";

        //echo js::alert("batchAssignTo: $msg");


        if(!empty($_POST))
        {
            $assignedTo  = $this->post->assignedTo;
            $taskIDList = $this->post->taskIDList;
            $taskIDList = array_unique($taskIDList);
            //unset($_POST['taskIDList']);
            if(!is_array($taskIDList)) die(js::locate($this->createLink('gametaskinternal', 'mydept', ""), 'parent'));


            //foreach ($taskIDList as $item) {$msg .= $item . "  ";}

            //echo js::alert("batchAssignTo: $msg");


            foreach($taskIDList as $taskID)
            {
                $this->dao->update(TABLE_GAMETASKINTERNAL)
                    ->set('assignedTo')->eq($assignedTo)
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
            //echo js::alert("assignTo: $assignedTo");
        }
    }

    public function batchAssignToDept()
    {
        /*
        $msg = " count:" . count($_POST). "  ";
        $msg .= " dept:" . $this->post->assignedToDept . "  ";
        $msg .= " assigned:" . $this->post->assignedTo . "  ";
        //echo js::alert("batchAssignTo: $msg");

        foreach ($_POST as $p)
        {
            if(is_array($p))
            {
                foreach ($p as $pp)
                {$msg .= "<br> _P:" . $pp;}
            }
            else
            {
                $msg .= "<br>   _PP:" . $p;
            }
        }
        //*/


        if(!empty($_POST))
        {
            //error_log( var_export($_POST));

            $assignedTo  = $this->post->assignedToDept;
            $taskIDList = $this->post->taskIDList;
            $taskIDList = array_unique($taskIDList);
            //unset($_POST['taskIDList']);
            if(!is_array($taskIDList)) die(js::locate($this->createLink('gametaskinternal', 'mydept', ""), 'parent'));

            //foreach ($taskIDList as $item) {$msg .= $item . "  ";}

            //echo js::alert("batchAssignToDept: $msg");
            //echo $msg;

            foreach($taskIDList as $taskID)
            {
                $this->dao->update(TABLE_GAMETASKINTERNAL)
                    ->set('dept')->eq($assignedTo)
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


    public function batchChangeVersion()
    {
        if(!empty($_POST))
        {
            //error_log( var_export($_POST));

            $assignedTo  = $this->post->changeVersion;
            $taskIDList = $this->post->taskIDList;
            $taskIDList = array_unique($taskIDList);
            //unset($_POST['taskIDList']);
            if(!is_array($taskIDList)) die(js::locate($this->createLink('gametaskinternal', 'mydept', ""), 'parent'));

            //foreach ($taskIDList as $item) {$msg .= $item . "  ";}

            //echo js::alert("batchChangeVersion: $msg");
            //echo $msg;

            foreach($taskIDList as $taskID)
            {
                $this->dao->update(TABLE_GAMETASKINTERNAL)
                    ->set('version')->eq($assignedTo)
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

    public function batchSetWorkhour()
    {
        //*
        $msg = " count:" . count($_POST). "  ";
        $msg .= " workHour:" . $this->post->workHour . "  ";

        foreach ($_POST as $p)
        {
            if(is_array($p))
            {
                foreach ($p as $pp)
                {$msg .= "<br> _P:" . $pp;}
            }
            else
            {
                $msg .= "<br>   _PP:" . $p;
            }
        }
        //*/

        if(!empty($_POST))
        {
            //error_log( var_export($_POST));

            $assignedTo  = $this->post->workHour;
            $taskIDList = $this->post->taskIDList;
            $taskIDList = array_unique($taskIDList);
            //unset($_POST['taskIDList']);
            if(!is_array($taskIDList)) die(js::locate($this->createLink('gametaskinternal', 'mydept', ""), 'parent'));

            //foreach ($taskIDList as $item) {$msg .= $item . "  ";}

            //echo js::alert("batchSetWorkhour: $msg");
            //echo $msg;

            foreach($taskIDList as $taskID)
            {
                $this->dao->update(TABLE_GAMETASKINTERNAL)
                    ->set('workhour')->eq($assignedTo)
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


    public function batchClose()
    {
        //echo js::alert("batchClose");


        if(!empty($_POST))
        {
            $taskIDList = $this->post->taskIDList;
            $taskIDList = array_unique($taskIDList);
            //unset($_POST['taskIDList']);
            if(!is_array($taskIDList)) die(js::locate($this->createLink('gametaskinternal', 'mytasks', ""), 'parent'));


            foreach($taskIDList as $taskID)
            {
                $this->dao->update(TABLE_GAMETASKINTERNAL)
                    ->set('closed')->eq(1)
                    ->where('id')->eq($taskID)
                    ->andWhere('completed')->eq(1)
                    ->exec();

                //$this->loadModel('action');
                //$changes = $this->task->assign($taskID);
                if(dao::isError()) die(js::error(dao::getError()));
                //$actionID = $this->action->create('task', $taskID, 'Assigned', $this->post->comment, $this->post->assignedTo);
                //$this->action->logHistory($actionID, $changes);
                //$this->task->sendmail($taskID, $actionID);
            }
            //if(!dao::isError()) $this->loadModel('score')->create('ajax', 'batchOther');
            die(js::locate($this->createLink('gametaskinternal', 'mytasks', ""), 'parent'));
            //echo js::alert("assignTo: $assignedTo");
        }
    }

    public function batchComplete()
    {
        //echo js::alert("batchComplete");


        if(!empty($_POST))
        {
            $taskIDList = $this->post->taskIDList;
            $taskIDList = array_unique($taskIDList);
            //unset($_POST['taskIDList']);
            if(!is_array($taskIDList)) die(js::locate($this->createLink('gametaskinternal', 'mydept', ""), 'parent'));


            foreach($taskIDList as $taskID)
            {
                $this->dao->update(TABLE_GAMETASKINTERNAL)
                    ->set('completed')->eq(1)
                    ->set('completeDate')->eq(helper::now())
                    ->where('id')->eq($taskID)
                    ->exec();

                //$this->loadModel('action');
                //$changes = $this->task->assign($taskID);
                if(dao::isError()) die(js::error(dao::getError()));
                //$actionID = $this->action->create('task', $taskID, 'Assigned', $this->post->comment, $this->post->assignedTo);
                //$this->action->logHistory($actionID, $changes);
                //$this->task->sendmail($taskID, $actionID);
            }
            //if(!dao::isError()) $this->loadModel('score')->create('ajax', 'batchOther');
            die(js::reload('parent'));
            //echo js::alert("assignTo: $assignedTo");
        }
    }

    public function batchActive()
    {
        //echo js::alert("batchActive");


        if(!empty($_POST))
        {
            $taskIDList = $this->post->taskIDList;
            $taskIDList = array_unique($taskIDList);
            //unset($_POST['taskIDList']);
            if(!is_array($taskIDList)) die(js::locate($this->createLink('gametaskinternal', 'mydept', ""), 'parent'));


            foreach($taskIDList as $taskID)
            {
                $this->dao->update(TABLE_GAMETASKINTERNAL)
                    ->set('closed')->eq(0)
                    ->set('completed')->eq(0)
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
            //echo js::alert("assignTo: $assignedTo");
        }
    }

    public function batchDelete()
    {
        //echo js::alert("batchDelete");


        if(!empty($_POST))
        {
            $taskIDList = $this->post->taskIDList;
            $taskIDList = array_unique($taskIDList);
            //unset($_POST['taskIDList']);
            if(!is_array($taskIDList)) die(js::locate($this->createLink('gametaskinternal', 'mydept', ""), 'parent'));


            foreach($taskIDList as $taskID)
            {
                $this->dao->update(TABLE_GAMETASKINTERNAL)
                    ->set('deleted')->eq(1)
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
            //echo js::alert("assignTo: $assignedTo");
        }
    }

    public function batchRestore()
    {
        //echo js::alert("batchDelete");


        if(!empty($_POST))
        {
            $taskIDList = $this->post->taskIDList;
            $taskIDList = array_unique($taskIDList);
            //unset($_POST['taskIDList']);
            if(!is_array($taskIDList)) die(js::locate($this->createLink('gametaskinternal', 'mydept', ""), 'parent'));


            foreach($taskIDList as $taskID)
            {
                $this->dao->update(TABLE_GAMETASKINTERNAL)
                    ->set('deleted')->eq(0)
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
            //echo js::alert("assignTo: $assignedTo");
        }
    }

}
