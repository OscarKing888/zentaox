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
class buildex extends control
{
    /**
     * Create a buld.
     * 
     * @param  int    $projectID 
     * @access public
     * @return void
     */
    public function create($projectID)
    {
        if(!empty($_POST))
        {
            $buildID = $this->buildex->create($projectID);
            if(dao::isError()) die(js::error(dao::getError()));
            $this->loadModel('action')->create('buildex', $buildID, 'opened');
            die(js::locate($this->createLink('buildex', 'view', "buildID=$buildID"), 'parent'));
        }

        $this->session->set('buildCreate', $this->app->getURI(true));

        /* Load these models. */
        $this->loadModel('project');
        $this->loadModel('user');
        
        if($this->config->global->flow == 'onlyTest')
        {
            $product  = $this->loadModel('product')->getByID($projectID);
            $products = $this->product->getPairs();
            $this->product->setMenu($products, $projectID);
            $this->lang->build->menu = $this->lang->product->menu;

            $productGroups   = array();
            $product->branch = 0;
            foreach($products as $productID => $name) $productGroups[$productID]['branch'] = 0;

            $this->view->title    = $this->lang->build->create;
            $this->view->product  = $product;
            $this->view->branches = ($product and $product->type == 'normal') ? array() : $this->loadModel('branch')->getPairs($projectID);
        }
        else
        {
            /* Set menu. */
            $this->project->setMenu($this->project->getPairs(), $projectID);

            /* Get stories and bugs. */
            $orderBy  = 'status_asc, stage_asc, id_desc';

            /* Assign. */
            $project = $this->loadModel('project')->getById($projectID);

            $productGroups = $this->project->getProducts($projectID);
            $productID     = key($productGroups);
            $products      = array();
            foreach($productGroups as $product) $products[$product->id] = $product->name;

            $this->view->title         = $project->name . $this->lang->colon . $this->lang->build->create;
            $this->view->position[]    = html::a($this->createLink('project', 'task', "projectID=$projectID"), $project->name);
            $this->view->position[]    = $this->lang->build->create;
            $this->view->product       = isset($productGroups[$productID]) ? $productGroups[$productID] : '';
            $this->view->branches      = (isset($productGroups[$productID]) and $productGroups[$productID]->type == 'normal') ? array() : $this->loadModel('branch')->getPairs($productID);
            $this->view->projectID     = $projectID;
            $this->view->orderBy       = $orderBy;
        }

        $this->view->products      = $products;
        $this->view->lastBuild     = $this->buildex->getLast($projectID);
        $this->view->productGroups = $productGroups;
        $this->view->users         = $this->user->getPairs('nodeleted');
        $this->display();
    }

    /**
     * Edit a build.
     * 
     * @param  int    $buildID 
     * @access public
     * @return void
     */
    public function edit($buildID)
    {
        if(!empty($_POST))
        {
            $changes = $this->buildex->update($buildID);
            if(dao::isError()) die(js::error(dao::getError()));
            $files = $this->loadModel('file')->saveUpload('buildex', $buildID);

            if($changes or $files)
            {
                $fileAction = '';
                if(!empty($files)) $fileAction = $this->lang->addFiles . join(',', $files) . "\n" ;
                $actionID = $this->loadModel('action')->create('buildex', $buildID, 'Edited', $fileAction);
                if(!empty($changes)) $this->action->logHistory($actionID, $changes);
            }

            //phpinfo();
            die(js::locate(inlink('view', "buildID=$buildID"), 'parent'));
        }

        $build = $this->buildex->getById((int)$buildID);

        if($this->config->global->flow == 'onlyTest')
        {
            $product  = $this->loadModel('product')->getById($build->product);
            $products = $this->product->getPairs();
            $this->product->setMenu($products, $build->product);
            $this->lang->build->menu = $this->lang->product->menu;

            $productGroups   = array();
            $product->branch = 0;
            foreach($products as $productID => $name) $productGroups[$productID]['branch'] = 0;

            $this->view->title      = $this->lang->build->edit;
            $this->view->position[] = $this->lang->build->edit;
            $this->view->product    = $product;
            $this->view->branches   = ($product and $product->type == 'normal') ? array() : $this->loadModel('branch')->getPairs($build->product);
        }
        else
        {
            $this->loadModel('project');

            /* Set menu. */
            $this->project->setMenu($this->project->getPairs(), $build->project);

            /* Get stories and bugs. */
            $orderBy = 'status_asc, stage_asc, id_desc';

            /* Assign. */
            $project = $this->loadModel('project')->getById($build->project);
            if(empty($project))
            {
                $project = new stdclass();
                $project->name = '';
            }

            $productGroups = $this->project->getProducts($build->project);

            $products      = array();
            foreach($productGroups as $product) $products[$product->id] = $product->name;
            if(empty($productGroups) and $build->product)
            {
                $product = $this->loadModel('product')->getById($build->product);
                $products[$product->id] = $product->name;
            }

            $this->view->title      = $project->name . $this->lang->colon . $this->lang->build->edit;
            $this->view->position[] = html::a($this->createLink('project', 'task', "projectID=$build->project"), $project->name);
            $this->view->position[] = $this->lang->build->edit;
            $this->view->product    = isset($productGroups[$build->product]) ? $productGroups[$build->product] : '';
            $this->view->branches   = (isset($productGroups[$build->product]) and $productGroups[$build->product]->type == 'normal') ? array() : $this->loadModel('branch')->getPairs($build->product);
            $this->view->orderBy    = $orderBy;
        }

        $this->view->productGroups = $productGroups;
        $this->view->products      = $products;
        $this->view->users         = $this->loadModel('user')->getPairs('noletter', $build->builder);
        $this->view->buildObj         = $build;

        $this->display();
    }
                                                          
    /**
     * View a build.
     * 
     * @param  int    $buildID 
     * @access public
     * @return void
     */
    public function view($buildID, $type = 'buildInfo', $link = 'false', $param = '', $orderBy = 'id_desc')
    {
        if($type == 'story')$this->session->set('storyList', $this->app->getURI(true));
        if($type == 'bug')  $this->session->set('bugList', $this->app->getURI(true));

        $this->loadModel('story');
        $this->loadModel('bug');

        /* Set menu. */
        $build = $this->buildex->getById((int)$buildID, true);
        if(!$build) die(js::error($this->lang->notFound) . js::locate('back'));

        $product = $this->loadModel('product')->getById($build->product);
        if($product->type != 'normal') $this->lang->product->branch = sprintf($this->lang->product->branch, $this->lang->product->branchName[$product->type]);

        $bugs = $this->dao->select('*')->from(TABLE_BUG)->where('id')->in($build->bugs)->andWhere('deleted')->eq(0)
            ->beginIF($type == 'bug')->orderBy($orderBy)->fi()
            ->fetchAll();

        if($this->config->global->flow == 'onlyTest')
        {
            $products = $this->loadModel('product')->getPairs();
            $this->product->setMenu($products, $build->product);
            $this->lang->build->menu = $this->lang->product->menu;

            $this->view->title      = "BUILD #$build->id $build->name - " . $build->productName;
            $this->view->position[] = html::a($this->createLink('product', 'buildex', "productID=$build->product"), $build->productName);
            $this->view->position[] = $this->lang->build->view;
        }
        else
        {
            $stories = $this->dao->select('*')->from(TABLE_STORY)->where('id')->in($build->stories)->andWhere('deleted')->eq(0)
                ->beginIF($type == 'story')->orderBy($orderBy)->fi()
                ->fetchAll('id');
            $stages  = $this->dao->select('*')->from(TABLE_STORYSTAGE)->where('story')->in($build->stories)->andWhere('branch')->eq($build->branch)->fetchPairs('story', 'stage');
            foreach($stages as $storyID => $stage)$stories[$storyID]->stage = $stage;

            $this->loadModel('project')->setMenu($this->project->getPairs(), $build->project);
            $projects = $this->project->getPairs('empty');

            $this->view->title         = "BUILD #$build->id $build->name - " . $projects[$build->project];
            $this->view->position[]    = html::a($this->createLink('project', 'task', "projectID=$build->project"), $projects[$build->project]);
            $this->view->position[]    = $this->lang->build->view;
            $this->view->stories       = $stories;
            $this->view->generatedBugs = $this->bug->getProjectBugs($build->project, $build->id, '', 0, $type == 'newbug' ? $orderBy : 'status_desc,id_desc', null);
            $this->view->bugs          = $bugs;
            $this->view->type          = $type;
        }

        $products = $this->loadModel('product')->getPairs();
        $product  = $this->loadModel('product')->getById($build->product);

        /* Assign. */
        $this->view->users         = $this->loadModel('user')->getPairs('noletter');
        $this->view->buildObj         = $build;
        $this->view->actions       = $this->loadModel('action')->getList('buildex', $buildID);
        $this->view->link          = $link;
        $this->view->param         = $param;
        $this->view->orderBy       = $orderBy;
        $this->view->branchName    = $build->productType == 'normal' ? '' : $this->loadModel('branch')->getById($build->branch);
        $this->view->products      = $products;
        $this->view->product      = $product;

        $this->display();
    }
 
    /**
     * Delete a build.
     * 
     * @param  int    $buildID 
     * @param  string $confirm  yes|noe
     * @access public
     * @return void
     */
    public function delete($buildID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->build->confirmDelete, $this->createLink('buildex', 'delete', "buildID=$buildID&confirm=yes")));
        }
        else
        {
            $build = $this->buildex->getById($buildID);
            $this->buildex->delete(TABLE_BUILDEX, $buildID);

            /* if ajax request, send result. */
            if($this->server->ajax)
            {
                if(dao::isError())
                {
                    $response['result']  = 'fail';
                    $response['message'] = dao::getError();
                }
                else
                {
                    $response['result']  = 'success';
                    $response['message'] = '';
                }
                $this->send($response);
            }

            if($this->config->global->flow == 'onlyTest') die(js::locate($this->createLink('project', 'buildex', "productID=$build->product"), 'parent'));
            die(js::locate($this->createLink('project', 'buildex', "projectID=$build->project"), 'parent'));
        }
    }

    /**
     * AJAX: get builds of a product in html select.
     * 
     * @param  int    $productID 
     * @param  string $varName      the name of the select object to create
     * @param  string $build        build to selected
     * @param  int    $branch
     * @param  int    $index        the index of batch create bug.
     * @param  string $type         get all builds or some builds belong to normal releases and projects are not done.
     * @access public
     * @return string
     */
    public function ajaxGetProductBuilds($productID, $varName, $build = '', $branch = 0, $index = 0, $type = 'normal')
    {
        $branch = $branch ? "0,$branch" : $branch;
        $isJsonView = $this->app->getViewType() == 'json';
        if($varName == 'openedBuild' )
        { 
            $params = ($type == 'all') ? 'noempty' : 'noempty, noterminate, nodone';
            $builds = $this->buildex->getProductBuildPairs($productID, $branch, $params);
            if($isJsonView) die(json_encode($builds));
            else die(html::select($varName . '[]', $builds, $build, 'size=4 class=form-control multiple'));
        }
        if($varName == 'openedBuilds' )
        {
            $builds = $this->buildex->getProductBuildPairs($productID, $branch, 'noempty');
            if($isJsonView) die(json_encode($builds));
            else die(html::select($varName . "[$index][]", $builds, $build, 'size=4 class=form-control multiple'));
        }
        if($varName == 'resolvedBuild')
        { 
            $params = ($type == 'all') ? '' : 'noterminate, nodone';
            $builds = $this->buildex->getProductBuildPairs($productID, $branch, $params);
            if($isJsonView) die(json_encode($builds));
            else die(html::select($varName, $builds, $build, "class='form-control'"));
        }
    }

    /**
     * AJAX: get builds of a project in html select.
     * 
     * @param  int    $projectID
     * @param  string $varName      the name of the select object to create
     * @param  string $build        build to selected
     * @param  int    $branch       
     * @param  int    $index        the index of batch create bug.
     * @param  bool   $needCreate   if need to append the link of create build
     * @param  string $type         get all builds or some builds belong to normal releases and projects are not done.
     * @access public
     * @return string
     */
    public function ajaxGetProjectBuilds($projectID, $productID, $varName, $build = '', $branch = 0, $index = 0, $needCreate = false, $type = 'normal')
    {
        $branch = $branch ? "0,$branch" : $branch;
        $isJsonView = $this->app->getViewType() == 'json';
        if($varName == 'openedBuild')
        {
            $params = ($type == 'all') ? 'noempty' : 'noempty, noterminate, nodone';
            $builds = $this->buildex->getProjectBuildPairs($projectID, $productID, $branch, $params);
            if($isJsonView) die(json_encode($builds));
            else die(html::select($varName . '[]', $builds , $build, 'size=4 class=form-control multiple'));
        }
        if($varName == 'openedBuilds')
        {
            $builds = $this->buildex->getProjectBuildPairs($projectID, $productID, $branch, 'noempty');
            if($isJsonView) die(json_encode($builds));
            else die(html::select($varName . "[$index][]", $builds , $build, 'size=4 class=form-control multiple'));
        }
        if($varName == 'resolvedBuild')
        { 
            $params = ($type == 'all') ? '' : 'noterminate, nodone';
            $builds = $this->buildex->getProjectBuildPairs($projectID, $productID, $branch, $params);
            if($isJsonView) die(json_encode($builds));
            else die(html::select($varName, $builds, $build, "class='form-control'"));
        }
        if($varName == 'testTaskBuild')
        {
            $builds = $this->buildex->getProjectBuildPairs($projectID, $productID, $branch, 'noempty,notrunk');
            if($isJsonView) die(json_encode($builds));
            else die(html::select('buildex', $builds, $build, "class='form-control'"));
        }
    }

    /**
     * Link stories
     * 
     * @param  int    $buildID 
     * @param  string $browseType 
     * @param  int    $param 
     * @access public
     * @return void
     */
    public function linkStory($buildID = 0, $browseType = '', $param = 0)
    {
        if(!empty($_POST['stories']))
        {
            $this->buildex->linkStory($buildID);
            die(js::locate(inlink('view', "buildID=$buildID&type=story"), 'parent'));
        }

        $this->session->set('storyList', inlink('view', "buildID=$buildID&type=story&link=true&param=" . helper::safe64Encode("&browseType=$browseType&queryID=$param")));
        $build   = $this->buildex->getById($buildID);
        $product = $this->loadModel('product')->getById($build->product);
        $this->loadModel('project')->setMenu($this->project->getPairs(), $build->project);
        $this->loadModel('story');
        $this->loadModel('tree');
        $this->loadModel('product');

        /* Build search form. */
        $queryID = ($browseType == 'bySearch') ? (int)$param : 0;
        unset($this->config->product->search['fields']['product']);
        unset($this->config->product->search['fields']['project']);
        $this->config->product->search['actionURL'] = $this->createLink('buildex', 'view', "buildID=$buildID&type=story&link=true&param=" . helper::safe64Encode("&browseType=bySearch&queryID=myQueryID"));
        $this->config->product->search['queryID']   = $queryID;
        $this->config->product->search['style']     = 'simple';
        $this->config->product->search['params']['plan']['values'] = $this->loadModel('productplan')->getForProducts(array($build->product => $build->product));
        $this->config->product->search['params']['module']['values']  = $this->tree->getOptionMenu($build->product, $viewType = 'story', $startModuleID = 0);
        $this->config->product->search['params']['status'] = array('operator' => '=', 'control' => 'select', 'values' => $this->lang->story->statusList);
        if($product->type == 'normal')
        {
            unset($this->config->product->search['fields']['branch']);
            unset($this->config->product->search['params']['branch']);
        }
        else
        {
            $this->config->product->search['fields']['branch'] = sprintf($this->lang->product->branch, $this->lang->product->branchName[$product->type]);
            $branches = array('' => '') + $this->loadModel('branch')->getPairs($build->product, 'noempty');
            if($build->branch) $branches = array('' => '', $build->branch => $branches[$build->branch]);
            $this->config->product->search['params']['branch']['values'] = $branches;
        }
        $this->loadModel('search')->setSearchParams($this->config->product->search);

        if($browseType == 'bySearch')
        {
            $allStories = $this->story->getBySearch($build->product, $queryID, 'id', null, $build->project, $build->branch);
        }
        else
        {
            $allStories = $this->story->getProjectStories($build->project);
        }

        $this->view->allStories   = $allStories;
        $this->view->build        = $build;
        $this->view->buildStories = empty($build->stories) ? array() : $this->story->getByList($build->stories);
        $this->view->users        = $this->loadModel('user')->getPairs('noletter');
        $this->view->browseType   = $browseType;
        $this->view->param        = $param;
        $this->display();
    }

    /**
     * Unlink story 
     * 
     * @param  int    $storyID 
     * @param  string $confirm  yes|no
     * @access public
     * @return void
     */
    public function unlinkStory($buildID, $storyID)
    {
        $this->buildex->unlinkStory($buildID, $storyID);

        /* if ajax request, send result. */
        if($this->server->ajax)
        {
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
            }
            else
            {
                $response['result']  = 'success';
                $response['message'] = '';
            }
            $this->send($response);
        }
        die(js::reload('parent'));
    }

    /**
     * Batch unlink story. 
     * 
     * @param  string $confirm 
     * @access public
     * @return void
     */
    public function batchUnlinkStory($buildID)
    {
        $this->buildex->batchUnlinkStory($buildID);
        die(js::locate($this->createLink('buildex', 'view', "buildID=$buildID&type=story"), 'parent'));
    }

    /**
     * Link bugs.
     * 
     * @param  int    $buildID 
     * @param  string $browseType 
     * @param  int    $param 
     * @access public
     * @return void
     */
    public function linkBug($buildID = 0, $browseType = '', $param = 0)
    {
        //error_log("== linkBug begin");

        if(!empty($_POST['bugs']))
        {
            $this->buildex->linkBug($buildID);
            die(js::locate(inlink('view', "buildID=$buildID&type=bug"), 'parent'));
        }

        $this->session->set('bugList', inlink('view', "buildID=$buildID&type=bug&link=true&param=" . helper::safe64Encode("&browseType=$browseType&queryID=$param")));
        /* Set menu. */
        $build   = $this->buildex->getByID($buildID);
        $product = $this->loadModel('product')->getByID($build->product);
        $this->loadModel('project')->setMenu($this->project->getPairs(), $build->project);

        $queryID = ($browseType == 'bysearch') ? (int)$param : 0;

        /* Build the search form. */
        $this->loadModel('bug');
        $this->config->bug->search['actionURL'] = $this->createLink('buildex', 'view', "buildID=$buildID&type=bug&link=true&param=" . helper::safe64Encode("&browseType=bySearch&queryID=myQueryID"));
        $this->config->bug->search['queryID']   = $queryID;
        $this->config->bug->search['style']     = 'simple';
        $this->config->bug->search['params']['plan']['values']          = $this->loadModel('productplan')->getForProducts(array($build->product => $build->product));
        $this->config->bug->search['params']['module']['values']        = $this->loadModel('tree')->getOptionMenu($build->product, $viewType = 'bug', $startModuleID = 0);
        $this->config->bug->search['params']['project']['values']       = $this->loadModel('product')->getProjectPairs($build->product);
        $this->config->bug->search['params']['openedBuild']['values']   = $this->buildex->getProductBuildPairs($build->product, $branch = 0, $params = '');
        $this->config->bug->search['params']['resolvedBuild']['values'] = $this->config->bug->search['params']['openedBuild']['values'];

        unset($this->config->bug->search['fields']['product']);
        unset($this->config->bug->search['params']['product']);
        if($product->type == 'normal')
        {
            //error_log("== linkBug - product == normal");

            unset($this->config->bug->search['fields']['branch']);
            unset($this->config->bug->search['params']['branch']);
        }
        else
        {
            //error_log("== linkBug - product != normal");

            $this->config->bug->search['fields']['branch'] = sprintf($this->lang->product->branch, $this->lang->product->branchName[$product->type]);
            $branches = array('' => '') + $this->loadModel('branch')->getPairs($build->product, 'noempty');
            if($build->branch) $branches = array('' => '', $build->branch => $branches[$build->branch]);
            $this->config->bug->search['params']['branch']['values'] = $branches;
        }
        $this->loadModel('search')->setSearchParams($this->config->bug->search);

        if($browseType == 'bySearch')
        {
            //error_log("== linkBug - bySearch");

            $allBugs = $this->bug->getBySearch($build->product, $queryID, 'id_desc', null, $build->branch);
        }
        else
        {
            //error_log("== linkBug - getBuildBugs");

            $projectBugs = $this->bug->getBuildBugs($build->project);
            $allBugs     = array();
            foreach($projectBugs as $key => $bug)
            {
                /*
                if($bug->status == 'resolved')
                {
                    $allBugs[$key] = $bug;
                    unset($projectBugs[$key]);
                }
                elseif($bug->status == 'closed') 
                {
                    unset($projectBugs[$key]);
                }
                */
            }
            $allBugs += $projectBugs;
        }

        //error_log("linkBug all bugs =:", sizeof($allBugs));
        $this->view->allBugs    = $allBugs;
        $this->view->buildBugs  = empty($build->bugs) ? array() : $this->bug->getByList($build->bugs);
        $this->view->build      = $build;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->browseType = $browseType;
        $this->view->param      = $param;
        $this->display();
    }

    /**
     * Unlink story 
     * 
     * @param  int    $buildID
     * @param  int    $bugID 
     * @access public
     * @return void
     */
    public function unlinkBug($buildID, $bugID)
    {
        $this->buildex->unlinkBug($buildID, $bugID);

        /* if ajax request, send result. */
        if($this->server->ajax)
        {
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
            }
            else
            {
                $response['result']  = 'success';
                $response['message'] = '';
            }
            $this->send($response);
        }
        die(js::reload('parent'));
    }

    /**
     * Batch unlink story. 
     * 
     * @param  int $buildID 
     * @access public
     * @return void
     */
    public function batchUnlinkBug($buildID)
    {
        $this->buildex->batchUnlinkBug($buildID);
        die(js::locate($this->createLink('buildex', 'view', "buildID=$buildID&type=bug"), 'parent'));
    }

    public function updatePathInfo($buildID, $fieldName)
    {
        $buildObj = $this->buildex->getById($buildID);

        if(!empty($_POST))
        {
            $this->loadModel('action');
            $changes = $this->buildex->updatePathInfo($buildID, $fieldName, $this->post->$fieldName);

            if(dao::isError()) die(js::error(dao::getError()));
            $actionID = $this->action->create('buildex', $buildID, 'Assigned', $this->post->$fieldName);
            $this->action->logHistory($actionID, $changes);

            if(isonlybody()) die(js::closeModal('parent.parent'));
            die(js::locate($this->createLink('buildex', 'view', "buildID=$buildID"), 'parent'));
        }


        $this->view->buildObj   = $buildObj;
        $this->view->fieldName = $fieldName;
        $this->view->title = $this->lang->buildex->updatePathInfo;
        $this->display();
    }
}
