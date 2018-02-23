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
    public function index($recTotal = 0, $recPerPage = 100, $pageID = 0)
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

    public function create()
    {
        $product = 1;
        if(!empty($_POST))
        {
            $nid = $this->artstation->create();
            if(dao::isError()) die(js::error(dao::getError()) . js::locate('back'));
            die(js::locate(inlink('view', "id=$nid")));
        }

        //$products = $this->product->getPairs();
        //$this->view->products = $products;
        $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        $this->view->title = $this->lang->artstation->add;


        $stories = $this->story->getProjectStoryByProduct($product);
        //$stories = $this->story->getProjectStoryPairs(1);
        $this->view->stories = $stories;
        $this->display();
    }


    public function edit($id)
    {
        if(!empty($_POST))
        {
            $this->artstation->update($id);
            $this->locate(inlink('view', "id=$id"));
        }
        else
        {
            $article = $this->artstation->getById($id);
            $this->view->article = $article;

            $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
            $this->view->title   = $this->lang->artstation->view;
            $allUsers = $this->user->getpairs('nodeleted|noclosed|noletter');
            $this->view->allUsers = $allUsers;
            $this->display();
        }
    }

    public function view($id)
    {
        if(!empty($_POST))
        {
            $data = fixer::input('post')
                ->specialchars($this->config->artstation->commentFields)
                ->add('date', helper::now())
                ->add('owner', $this->app->user->account)
                ->get();

            $lk = $this->dao->select()->from(TABLE_ARTSTATION_COMMENT)
                ->where('owner')->eq($data->owner)
                ->andWhere('content')->eq($data->content)
                ->fetch();

            if(!empty($lk))
            {
                error_log("oscar: add duplicate comment:$data->owner content:$data->content");
            }
            else{
                $this->dao->insert(TABLE_ARTSTATION_COMMENT)->data($data)->exec();
            }
        }

        $article = $this->artstation->getById($id);
        $this->view->article = $article;

        $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        $this->view->title   = $this->lang->artstation->view;
        $allUsers = $this->user->getpairs('nodeleted|noclosed|noletter');
        $this->view->allUsers = $allUsers;
        $this->view->comments = $this->artstation->getComments($id);


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

    public function search($tags = '', $product=0, $recTotal = 0, $recPerPage = 20, $pageID = 0)
    {
        $this->app->loadClass('pager');
        $pager = new pager($recTotal, $recPerPage, $pageID);

        //$product = 0;
        $type = -1;
        //$tags = "";
        $user = "";

        if(!empty($_POST))
        {
            $pst = fixer::input('post')
                ->get();

            //$product = $pst->product;
            $type = $pst->type;
            //$tags = $pst->tags;
            $user = $pst->user;
        }

        $articles = $this->artstation->search($pager, $product, $type, $tags, $user);

        $this->view->allProducts   = array(0 => '无') + $this->product->getPairs('noclosed|nocode');
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

