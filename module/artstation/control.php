<?php
/**
 * The control file of blog module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 * 
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
class artstation extends control
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
        $this->loadModel('story');
    }

    /**
     * The index page of blog module.
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
        $this->view->title    = $this->lang->artstation->index;
        $this->view->articles = $this->artstation->getList($pager);
        $this->view->pager    = $pager;
        $this->display();
    }

    public function my($recTotal = 0, $recPerPage = 20, $pageID = 0)
    {
        //$this->view->debugStr +=  $this->menu;

        $this->app->loadClass('pager');
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $products = $this->product->getPairs();
        $this->view->products = $products;
        $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        $this->view->title    = $this->lang->artstation->index;
        $this->view->articles = $this->artstation->getListByUser($this->app->user->account, $pager);
        $this->view->pager    = $pager;
        $this->display();
    }

    /*
    public function browse()
    {
        $this->view->debugStr += "browse()    ";
    }

    public function commonAction()
    {
        $this->view->debugStr += "commonAction()    ";
    }
    */

    /**
     * Create an article.
     * 
     * @access public
     * @return void
     */
    public function create()
    {
        $product = 1;
        if(!empty($_POST))
        {
            $blogID = $this->artstation->create();
            if(dao::isError()) die(js::error(dao::getError()) . js::locate('back'));
            die(js::locate(inlink('index')));
        }

        //$products = $this->product->getPairs();
        //$this->view->products = $products;
        $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        $this->view->title = $this->lang->artstation->add;


        $stories = $this->story->getProductStories($product);
        //$stories = $this->story->getProjectStoryPairs(1);
        $this->view->stories = $stories;
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
            $this->artstation->update($id);
            $this->locate(inlink('index'));
        }
        else
        {
            //$products = $this->product->getPairs();
            $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
            $this->view->title   = $this->lang->artstation->edit;
            $article = $this->artstation->getByID($id);
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
    public function view($id)
    {
        $user = $this->app->user->account;

        if(!empty($_POST))
        {
            $user = fixer::input('post')
                ->get()->user;
        }

        $article = $this->artstation->getById($id);
        //$newarticles = $this->processArticles($articles);
        $this->view->article = $article;

        $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        $this->view->title   = $this->lang->artstation->view;
        $allUsers = $this->user->getpairs('nodeleted|noclosed|noletter');
        $this->view->allUsers = $allUsers;
        $this->view->user = $user;

        $this->display();
    }

    public function like()
    {
        $userid = "";
        $imageid = -1;
        if(!empty($_POST)) {
            $postVals = fixer::input('post')->get();
            $userid = $postVals->userid;
            $imageid = $postVals->imageid;
        }
        else
        {
            return false;
        }

        $this->artstation->like($userid, $imageid);
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
        $this->artstation->delete($id);
        $this->locate(inlink('index'));
    }

    public function restore($recTotal = 0, $recPerPage = 20, $pageID = 0)
    {
        $this->app->loadClass('pager');
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $products = $this->product->getPairs();
        $this->view->products = $products;
        $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        $this->view->title    = $this->lang->artstation->restore;
        $this->view->articles = $this->artstation->getDeletedList($pager);
        $this->view->pager    = $pager;
        $this->display();
    }

    public function restoreartstation($id)
    {
        $this->artstation->restore($id);
        $this->locate(inlink('restore'));
    }

    public function search($recTotal = 0, $recPerPage = 20, $pageID = 0)
    {
        $this->app->loadClass('pager');
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $product = 1;
        $type = -1;
        $tags = "";
        $user = "";

        if(!empty($_POST))
        {
            $pst = fixer::input('post')
                ->get();

            $product = $pst->product;
            $type = $pst->type;
            $tags = $pst->tags;
            $user = $pst->user;
        }

        $articles = $this->artstation->search($pager, $product, $type, $tags, $user);

        $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        $this->view->title    = $this->lang->artstation->reportproject;

        $newarticles = $this->processArticles($articles);
        $this->view->articles = $newarticles;

        $this->view->product = $product;
        $this->view->type = $type;
        $this->view->tags = $tags;
        $this->view->user = $user;

        $allUsers = array("" => "无") + $this->user->getpairs('nodeleted|noclosed');
        $this->view->allUsers = $allUsers;

        $this->view->depts = $this->dept->getOptionMenu();
        $this->view->pager    = $pager;

        $this->display();
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

}

