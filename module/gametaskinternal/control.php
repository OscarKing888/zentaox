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
    }

    /**
     * The index page of gametaskinternal module.
     *
     * @access public
     * @return void
     */
    public function index($recTotal = 0, $recPerPage = 20, $pageID = 0)
    {
        $this->setupViewTasks($recTotal, $recPerPage, $pageID);
        $this->display();
    }

    public function details($recTotal = 0, $recPerPage = 20, $pageID = 0)
    {
        $this->setupViewTasks($recTotal, $recPerPage, $pageID);
        $this->display();
    }

    public function mytasks($recTotal = 0, $recPerPage = 20, $pageID = 0)
    {
        $this->setupViewTasks($recTotal, $recPerPage, $pageID);
        $this->display();
    }

    public function mydept($recTotal = 0, $recPerPage = 20, $pageID = 0)
    {
        $this->setupViewTasks($recTotal, $recPerPage, $pageID);
        $this->display();
    }

    public function completedlist($recTotal = 0, $recPerPage = 20, $pageID = 0)
    {
        $this->setupViewTasks($recTotal, $recPerPage, $pageID);
        $this->display();
    }

    public function incompletelist($recTotal = 0, $recPerPage = 20, $pageID = 0)
    {
        $this->setupViewTasks($recTotal, $recPerPage, $pageID);
        $this->display();
    }

    public function setupViewTasks($recTotal = 0, $recPerPage = 20, $pageID = 0)
    {
        //$this->view->debugStr +=  $this->menu;

        $this->app->loadClass('pager');
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->allProducts = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        $depts = $this->dao->select('id,name')->from(TABLE_DEPT)->fetchPairs();
        $this->view->depts = $depts;

        $this->view->allOwners = $this->getUserByGroupName(GROUPNAME_CQYH);

        $allUsers = $this->user->getPairs('nodeleted|noclosed');
        $this->view->allUsers = $allUsers;

        $this->view->user = $this->app->user->account;

        $versions = $this->dao->select('id,name')->from(TABLE_GAMETASKINTERNALVERSION)
            ->where('active')->eq(1)
            ->orderBy('id desc')
            ->fetchPairs();

        $this->view->versions = $versions;

        //foreach (array_keys($versions) as $k) { error_log("oscar: version:k $k");  }

        $gameTasks = $this->dao->select()->from(TABLE_GAMETASKINTERNAL)
            ->where('deleted')->eq(0)
            ->andWhere('version')->in(array_keys($versions))
            //->groupBy('version')
            ->orderBy('pri asc')
            ->fetchAll();

        $this->view->gameTasks = $gameTasks;

        $this->view->pager = $pager;
    }

    public function create()
    {
        $this->view->msg = "";

        if (!empty($_POST)) {
            $newGameTasks = fixer::input('post')->get();
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
        $this->view->gameTasks = $gameTasks;

        $this->view->allProducts = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        $depts = $this->dao->select('id,name')->from(TABLE_DEPT)->fetchPairs();
        $this->view->depts = $depts;

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

            error_log("oscar: updateVersionDeadline $id deadline:$deadline");
            $this->dao->update(TABLE_GAMETASKINTERNALVERSION)
                ->set('deadline')->eq($deadline)
                ->where('id')->eq($id)
                ->exec();
        } else {
            return false;
        }
    }

    public function groupleaders()
    {
        //error_log("oscar: view groupleaders");

        //$depts = $this->dao->select('id,name')->from(TABLE_DEPT)->fetchPairs();
        $depts = $this->dept->getOptionMenu();
        $this->view->depts = $depts;

        if (!empty($_POST)) {

            //error_log("oscar: view groupleaders  1");

            $leader = fixer::input('post')->get();
            $batchNum = count($depts);
            //error_log("oscar: groupleaders batchNum $batchNum count:" . count($leader));

            var_dump($leader);

            for ($i = 0; $i < $batchNum; $i++) {
                //error_log("oscar: groupleaders $i dept;$depts[$i]  username:$leader->username[$i]");

                if (empty($leader->username[$i])) {
                    continue;
                }

                $dat = new stdclass();
                $dat->dept = $i;
                $dat->username = $leader->username[$i];

                $c = $this->dao->select()->from(TABLE_GAMEGROUPLEADERS)
                    ->where('dept')->eq($dat->dept)
                    ->count();

                //error_log("oscar: groupleaders  select count:$c $dat->dept $dat->username" );

                if ($c == 0) {
                    //error_log("oscar: groupleaders  insert count:$c $dat->dept $dat->username");
                    $this->dao->insert(TABLE_GAMEGROUPLEADERS)->data($dat)
                        //->autoCheck()
                        //->batchCheck('dept,username', 'notempty')
                        ->exec();
                } else {
                    //error_log("oscar: groupleaders  update count:$c $dat->dept $dat->username");
                    $this->dao->update(TABLE_GAMEGROUPLEADERS)->data($dat)
                        ->where('dept')->eq($dat->dept)
                        //->autoCheck()
                        //->batchCheck('dept,username', 'notempty')
                        ->exec();
                }

                if (dao::isError()) {
                    die(js::error(dao::getError()));
                }
            }

        }

        //error_log("oscar: view groupleaders  2");


        //error_log("oscar: view groupleaders  3");
        $allUsers = $this->user->getPairs('nodeleted|noclosed');
        $this->view->allUsers = $allUsers;

        $leaders = $this->dao->select('dept,username')->from(TABLE_GAMEGROUPLEADERS)
            ->orderBy('dept asc')
            ->fetchPairs();

        //error_log("oscar: view groupleaders  count:" . count($leaders));
        $this->view->leaders = $leaders;

        $this->display();
    }

    public function delete($id)
    {
        $this->gametaskinternal->delete($id);
        $this->locate(inlink('index'));
    }

    public function restore($recTotal = 0, $recPerPage = 20, $pageID = 0)
    {
        $this->setupViewTasks($recTotal, $recPerPage, $pageID);
        $this->display();
    }

    public function restorepipeline($id)
    {
        $this->gametaskinternal->restore($id);
        $this->locate(inlink('restore'));
    }

}
