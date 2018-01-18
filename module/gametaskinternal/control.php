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

        $gameTasks = $this->dao->select()->from(TABLE_GAMETASKINTERNAL)
            ->where('deleted')->eq(0)
            ->fetchAll();

        $this->view->gameTasks = $gameTasks;

        $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        $depts = $this->dao->select('id,name')->from(TABLE_DEPT)->fetchPairs();
        $this->view->depts = $depts;

        $this->view->allOwners = $this->getUserByGroupName(GROUPNAME_CQYH);

        $allUsers = $this->user->getPairs('nodeleted|noclosed');
        $this->view->allUsers = $allUsers;

        $this->view->user = $this->app->user->account;

        $versions = $this->dao->select('id,name')->from(TABLE_GAMETASKINTERNALVERSION)
            //->where('active')->eq(1)
            ->orderBy('id desc')
            ->fetchPairs();
        $this->view->versions = ($versions);

        $this->view->pager    = $pager;
    }

    public function create()
    {
        if(!empty($_POST))
        {
            $newGameTasks = fixer::input('post')->get();
            $batchNum = count(reset($newGameTasks));

            $version = '';
            $dept = 0;
            $owner = '';

            for($i = 0; $i < $batchNum; $i++)
            {
                if(empty($newGameTasks->title[$i]))
                {
                    continue;
                }

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
                    ->checkIF($data[$i]->estimate != '', 'estimate', 'float')
                    ->exec();

                if(dao::isError())
                {
                    die(js::error(dao::getError()));
                }
                else
                {

                }
            }


            //$pipelineID = $this->pipeline->create();
            //if(dao::isError()) die(js::error(dao::getError()) . js::locate('back'));
            //die(js::locate(inlink('index')));
        }

        $gameTasks = array();

        if(count($gameTasks) < $this->config->gameTaskInternal->batchCreate)
        {
            $paddingCount = $this->config->pipeline->defaultStages - count($gameTasks);
            $newTask = new stdclass();
            //$step->type   = 'item';
            //$step->desc   = '';
            //$step->expect = '';
            for($i = 1; $i <= $paddingCount; $i ++)
                $gameTasks[$i] = $newTask;
        }
        $this->view->gameTasks            = $gameTasks;

        $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
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

        foreach($users as $account => $user)
        {
            $firstLetter = ucfirst(substr($account, 0, 1)) . ':';
            $users[$account] =  $firstLetter . $user;
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

            $v = $this->dao->select()->from(TABLE_GAMETASKINTERNALVERSION)
                ->where('name')->eq($version)
                ->count();

            if($v == 0) {
                $this->dao->insert(TABLE_GAMETASKINTERNALVERSION)->data($dat)
                    ->autoCheck()
                    ->batchCheck('name', 'notempty')
                    ->exec();
                $this->view->msg = "版本[$version]添加成功!";
            }
            else
            {
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
        if(!empty($_POST)) {

            $postVals = fixer::input('post')
                ->get();

            $id = $postVals->id;

            //error_log("oscar: active_version $id");
            $this->dao->update(TABLE_GAMETASKINTERNALVERSION)
                ->set('active')->eq(1)
                ->where('id')->eq($id)
                ->exec();
        }
        else
        {
            return false;
        }
    }

    public function closeVersion()
    {
        if(!empty($_POST)) {

            $postVals = fixer::input('post')
                ->get();

            $id = $postVals->id;

            //error_log("oscar: close_version $id");
            $this->dao->update(TABLE_GAMETASKINTERNALVERSION)
                ->set('active')->eq(0)
                ->where('id')->eq($id)
                ->exec();
        }
        else
        {
            return false;
        }
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
