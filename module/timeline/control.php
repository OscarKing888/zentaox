<?php
/**
 * The control file of timeline module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 * 
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
class timeline extends control
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

        $this->loadModel('file');
    }

    /**
     * The index page of timeline module.
     * 
     * @access public
     * @return void
     */
    public function index()
    {
        if(!empty($_POST)) {
        }
        //$this->view->articles = $this->timeline->getList($pager);
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
        if(!empty($_POST))
        {
            $timelineID = $this->timeline->create();
            if(dao::isError()) die(js::error(dao::getError()) . js::locate('back'));
            die(js::locate(inlink('index')));
        }

        //$products = $this->product->getPairs();
        //$this->view->products = $products;
        //$this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        $this->view->title = $this->lang->timeline->addAB;
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
            $this->timeline->update($id);
            $this->locate(inlink('index'));
        }
        else
        {
            //$products = $this->product->getPairs();
            $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
            $this->view->title   = $this->lang->timeline->edit;
            $article = $this->timeline->getByID($id);
            $this->view->article = $article;
            $this->view->product = $article->product;
            $this->display();
        }
    }

    /**
     * View an article.
     *
     * @access public
     * @return void
     */
    public function view($recTotal = 0, $recPerPage = 20, $pageID = 0)
    {
        $this->app->loadClass('pager');
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $user = $this->app->user->account;

        if(!empty($_POST))
        {
            $user = fixer::input('post')
                ->get()->user;
        }


        $articles = $this->timeline->getListByUser($user, $pager);
        $newarticles = $this->processArticles($articles);
        $this->view->articles = $newarticles;

        $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        $this->view->title   = $this->lang->timeline->view;
        $allUsers = $this->user->getpairs('nodeleted|noclosed');
        $this->view->allUsers = $allUsers;
        $this->view->user = $user;
        $this->view->pager    = $pager;

        $this->display();
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
        $this->timeline->delete($id);
        $this->locate(inlink('index'));
    }

    public function restore($recTotal = 0, $recPerPage = 20, $pageID = 0)
    {
        $this->app->loadClass('pager');
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $products = $this->product->getPairs();
        $this->view->products = $products;
        $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        $this->view->title    = $this->lang->timeline->index;
        $this->view->articles = $this->timeline->getDeletedList($pager);
        $this->view->pager    = $pager;
        $this->display();
    }

    public function restoretimeline($id)
    {
        $this->timeline->restore($id);
        $this->locate(inlink('restore'));
    }
    function processArticles($articles)
    {
        $newarticles = array();
        foreach ($articles as $tart) {
            //error_log("oscar: owner " . $tart->owner);
            $tart->user = $this->user->getById($tart->owner);
            $tart->ownerrealname = $tart->user->realname;
            $tart->account = $tart->user->account;
            $tart->dept = $tart->user->dept;

            $newarticles[$tart->id]=($tart);
        }

        return $newarticles;
    }

    public function ajaxGetTimelineEvents()
    {
        $tasks = $this->timeline->ajaxGetTimelineEvents();
        die(json_encode($tasks));
    }
}

