<?php
/**
 * The control file of pipeline module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 * 
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
class pipeline extends control
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
        $this->loadModel('user');
    }

    /**
     * The index page of pipeline module.
     * 
     * @access public
     * @return void
     */
    public function index($recTotal = 0, $recPerPage = 20, $pageID = 0)
    {
        //$this->view->debugStr +=  $this->menu;

        $this->app->loadClass('pager');
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $products = $this->product->getPairs();
        $this->view->products = $products;
        $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        $this->view->title    = $this->lang->pipeline->index;
        $this->view->articles = $this->pipeline->getList($pager);
        $this->view->pager    = $pager;
        //oscar:
        //$this->loadModel('dept');

        $this->view->depts = $this->dept->getOptionMenu();
        //$this->dept->setupDeptUsers($this->view, $this->app->user->account, $this->app->user->dept);
        //oscar:
        $this->display();
    }

    /**
     * Create an article.
     * 
     * @access public
     * @return void
     */
    public function create()
    {
        $steps        = array();

        /* Padding the steps to the default steps count. */
        if(count($steps) < $this->config->pipeline->defaultStages)
        {
            $paddingCount = $this->config->pipeline->defaultStages - count($steps);
            $step = new stdclass();
            $step->type   = 'group';
            $step->desc   = '';
            $step->estimate = 8;
            for($i = 1; $i <= $paddingCount; $i ++)
                $steps[$i] = $step;
        }

        if(!empty($_POST))
        {
            $pipelineID = $this->pipeline->create();
            if(dao::isError()) die(js::error(dao::getError()) . js::locate('back'));
            die(js::locate(inlink('index')));
        }

        $products = $this->product->getPairs();
        $this->view->products = $products;
        $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        $this->view->title = $this->lang->pipeline->create;
        $this->view->depts = $this->dept->getOptionMenu();
        $this->view->steps            = $steps;

        //error_log(var_dump($_POST));

        $this->display();
    }

   /**
     * Update an article.
     * 
     * @param  int    $id 
     * @access public
     * @return void
     */
    public function edit($id)
    {
        if(!empty($_POST))
        {
            error_log("edit pipline:$id");
            $this->pipeline->update($id);
            $this->locate(inlink('index'));
        }
        else
        {
            $products = $this->product->getPairs();
            $this->view->products = $products;
            $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
            $this->view->title   = $this->lang->pipeline->edit;
            $this->view->article = $this->pipeline->getByID($id);
            $this->view->depts = $this->dept->getOptionMenu();
            $this->display();
        }
    }

    /**
     * Delete an article.
     * 
     * @param  int    $id 
     * @access public
     * @return void
     */
    public function delete($id)
    {
        $this->pipeline->delete($id);
        $this->locate(inlink('index'));
    }

    public function restore($recTotal = 0, $recPerPage = 20, $pageID = 0)
    {
        $this->app->loadClass('pager');
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $products = $this->product->getPairs();
        $this->view->products = $products;
        $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        $this->view->title    = $this->lang->pipeline->index;
        $this->view->articles = $this->pipeline->getDeletedList($pager);
        $this->view->pager    = $pager;
        $this->view->depts = $this->dept->getOptionMenu();
        $this->display();
    }

    public function restorepipeline($id)
    {
        $this->pipeline->restore($id);
        $this->locate(inlink('restore'));
    }

    public function batchCreateRootTaskWithConfirm()
    {
        $projectID = 0;
        $pipelineID = 0;
        $storyID = 0;
        $productID = 0;

        if(!empty($_POST)) {
            $postVals = fixer::input('post')
                ->get();
            $projectID = $postVals->projectID;
            $pipelineID = $postVals->pipelineID;
            $storyID = $postVals->storyID;
            $productID = $postVals->productID;
        }
        else
        {
            return false;
        }

        $this->display();
    }

    public function batchCreateRootTask()
    {
        $projectID = 0;
        $pipelineID = 0;
        $storyID = 0;
        $productID = 0;

        if(!empty($_POST)) {
            $postVals = fixer::input('post')
                ->get();
            $projectID = $postVals->projectID;
            $pipelineID = $postVals->pipelineID;
            $storyID = $postVals->storyID;
            $productID = $postVals->productID;
        }
        else
        {
            return false;
        }


        //error_log("oscar: batchCreateRootTask projectID:$projectID pipelineID:$pipelineID storyID:$storyID");

        $pipeline = $this->pipeline->getById($pipelineID);

        //$view = new stdClass();
        //$this->dept->setupDeptUsers($view, $this->app->user->account, $this->app->user->dept);

        $leaders = $this->dao->select('dept,username')->from(TABLE_GAMEGROUPLEADERS)
            ->orderBy('dept asc')
            ->fetchPairs();


        //$this->loadModel('task');
        $this->loadModel('story');
        $story = $this->story->getById($storyID);

        foreach ($pipeline->steps as $step) {
            //error_log("oscar: batchCreateRootTask step:$step $step->type dept:$step->dept");
            if($step->type == 'step')
            {
                //error_log("oscar: create root task step:$step dept:$step->dept est:$step->estimate");

                $task = new stdClass();
                $task->dept = $step->dept;
                $task->estimate = (float)$step->estimate;
                $task->name = $story->title;
                $task->desc = $story->spec;
                //$task->product = $productID;
                $task->module = $story->module;
                $task->project = $projectID;
                $task->story = $storyID;
                $task->storyVersion = $this->story->getVersion($storyID);
                $task->type = "";
                $task->left = 0;
                //$task->build = 0;
                $task->status = 'wait';
                $task->estStarted = helper::nowafter(5);
                $task->deadline =  helper::nowafter(10);
                $task->openedBy = $this->app->user->account;
                $task->openedDate = helper::now();
                $task->pipeline = $pipelineID;
                $task->createtype = 'pipelineBatchCreate';
                $task->pri = $story->pri;

                $this->dao->insert(TABLE_TASK)->data($task)
                    ->autoCheck()
                    ->batchCheck($this->config->task->create->requiredFields, 'notempty')
                    //->checkIF($task->estimate != '', 'estimate', 'float')
                    //->checkIF($task->deadline != '0000-00-00', 'deadline', 'ge', $task->estStarted)
                    //->check($task->deadline != '0000-00-00', 'deadline')
                    ->exec();

                if(dao::isError()) return false;
            }
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

            //var_dump($leader);

            for ($i = 0; $i < $batchNum; $i++) {
                //error_log("oscar: groupleaders $i dept;$depts[$i]  username:$leader->username[$i]");

                if (empty($leader->username[$i])) {
                    continue;
                }

                $dat = new stdclass();
                $dat->dept = $leader->dept[$i];
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

    public function autostory()
    {
        $products = $this->loadModel('product')->getPairs();
        $this->view->products = $products;

        $projects = $this->loadModel('project')->getPairs();
        $this->view->projects = $projects;

        if (!empty($_POST)) {


            $projprodPairs = fixer::input('post')->get();
            $batchNum = count($projects);


            for ($i = 0; $i < $batchNum; $i++) {

                if (empty($projprodPairs->product[$i])) {
                    continue;
                }

                $dat = new stdclass();
                $dat->project = $projprodPairs->project[$i];
                $dat->product = $projprodPairs->product[$i];

                $c = $this->dao->select()->from(TABLE_AUTOSTORY)
                    ->where('project')->eq($dat->project)
                    ->count();


                if ($c == 0) {
                    $this->dao->insert(TABLE_AUTOSTORY)->data($dat)
                        ->exec();
                } else {
                    $this->dao->update(TABLE_AUTOSTORY)->data($dat)
                        ->where('project')->eq($dat->project)
                        ->exec();
                }

                if (dao::isError()) {
                    die(js::error(dao::getError()));
                }
            }

        }

        $projProdPairs = $this->dao->select('project, product')->from(TABLE_AUTOSTORY)
            ->orderBy('project asc')
            ->fetchPairs();

        $this->view->projProdPairs = $projProdPairs;

        $this->display();
    }

}
