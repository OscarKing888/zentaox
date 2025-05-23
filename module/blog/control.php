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
class blog extends control
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
        $this->view->title    = $this->lang->blog->index;
        $this->view->articles = $this->blog->getList($pager);
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
        $myAccount = $this->app->user->account;
        $product =  $this->cookie->lastProduct;

        if(empty($product))
        {
            $product = 1;
        }

        $oldBlog = $this->dao->select('*, DATE_FORMAT(date, \'%Y-%m-%d\') ')->from(TABLE_BLOG)
            ->where('owner')->eq($myAccount)
            ->andWhere('DATE(date)')->eq(helper::today())
            ->andWhere('product')->eq($product)
            ->andWhere('deleted')->eq(0)
            ->fetch();

        //error_log("==================");
        //error_log(var_dump($oldBlog));


        if(!empty($_POST))
        {
            if(!empty($oldBlog))
            {
                $this->blog->update($oldBlog->id);
            }
            else
            {
                $blogID = $this->blog->create();
            }
            if(dao::isError()) die(js::error(dao::getError()) . js::locate('back'));
            die(js::locate(inlink('index')));
        }


        //$products = $this->product->getPairs();
        //$this->view->products = $products;
        $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');


        //error_log("last product:$product");



        $this->view->product = $product;

        if(!empty($oldBlog))
        {
            $this->view->title   = $this->lang->blog->edit;
            $this->view->article = $oldBlog;

            $oldBlog = $this->file->replaceImgURL($oldBlog, $this->config->blog->imageContentFieldName);
            $oldBlog->contentimages = htmlspecialchars_decode($oldBlog->contentimages);
        }
        else
        {
            $blog = new stdClass();
            $blog->date = helper::now();
            $blog->content = "";
            $blog->contentimages = "";

            $this->view->title = $this->lang->blog->add;
            $this->view->article = $blog;
        }

        //error_log("blog create title:" . $this->view->title);

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
            $this->blog->update($id);
            $this->locate(inlink('index'));
        }
        else
        {
            //$products = $this->product->getPairs();
            $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
            $this->view->title   = $this->lang->blog->edit;
            $article = $this->blog->getByID($id);
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


        $articles = $this->blog->getListByUser($user, $pager);
        $newarticles = $this->processArticles($articles);
        $this->view->articles = $newarticles;

        $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        $this->view->title   = $this->lang->blog->view;
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
        $this->blog->delete($id);
        $this->locate(inlink('index'));
    }

    public function restore($recTotal = 0, $recPerPage = 20, $pageID = 0)
    {
        $this->app->loadClass('pager');
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $products = $this->product->getPairs();
        $this->view->products = $products;
        $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        $this->view->title    = $this->lang->blog->index;
        $this->view->articles = $this->blog->getDeletedList($pager);
        $this->view->pager    = $pager;
        $this->display();
    }

    public function restoreblog($id)
    {
        $this->blog->restore($id);
        $this->locate(inlink('restore'));
    }

    public function reportmyteam()
    {
        $day = helper::today();
        $product = 1;

        if(!empty($_POST))
        {
            $day = fixer::input('post')
                //->specialchars('day')
                ->get()->day;

            $product = fixer::input('post')
                ->get()->product;
        }

        $articles = $this->blog->getGroupReport($day, $product, (int)$this->app->user->dept);
        //$articles = $this->blog->getAllReport($day);
        //$articles = $this->dao->select("name")->from('zt_dept')->fetchAll();
        //$articles = $this->dao->select("*")->from('gameblog')->fetchAll();

        //$products = $this->product->getPairs();
        //$this->view->products = $products;
        $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        $this->view->title    = $this->lang->blog->reportmyteam;

        $newarticles = $this->processArticles($articles);
        $this->view->articles = $newarticles;

        $this->view->mydept = $this->app->user->dept;
        $this->view->mydeptName = $this->dept->getById($this->app->user->dept)->name;
        $this->view->day = $day;
        $this->view->product = $product;
        $depts = $this->dao->select('id,name')->from(TABLE_DEPT)->fetchPairs();
        $this->view->depts = $depts;
        $this->view->showstat = 1;
        $this->view->userAbsent = $this->blog->getUserAbsent($day);

        //foreach (array_keys($depts) as $d)
        {
            $this->view->deptusers[$this->app->user->dept] = $this->dao->select('account,realname')->from(TABLE_USER)
                ->where('deleted')->eq(0)
                ->andWhere('dept')->eq($this->app->user->dept)
                ->orderBy('account')
                ->fetchPairs();
        }

        $this->display();
    }


    //public $showstat;
    public function reportproject()
    {
        $day = helper::today();
        $product = 1;
        $showstat = 1;

        if(!empty($_POST))
        {
            $day = fixer::input('post')
                //->specialchars('day')
                ->get()->day;

            $product = fixer::input('post')
                ->get()->product;

            //$showstat = fixer::input('post')
                //->get()->showstat;

            //var_dump($_POST['checkbox']);

            if(!is_null($_POST['checkbox']))
            {
                $showstat = 1;
            }
            else
            {
                $showstat = 0;
            }
            //error_log("oscar: showstat=$showstats postempty:". empty($_POST['showstat']));
            //error_log("oscar: checkbox:". is_null($_POST['checkbox']));
        }
        //error_log("oscar: showstat=$showstats");

        $articles = $this->blog->getAllReport($day);
        //$articles = $this->dao->select("name")->from('zt_dept')->fetchAll();
        //$articles = $this->dao->select("*")->from('gameblog')->fetchAll();

        //$products = $this->product->getPairs();
        //$this->view->products = $products;
        $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        $this->view->title    = $this->lang->blog->reportproject;

        $newarticles = $this->processArticles($articles);
        $this->view->articles = $newarticles;

        $this->view->dept = $this->dept->getById($this->app->user->dept)->name;
        $this->view->day = $day;
        $this->view->product = $product;
        $depts = $this->dao->select('id,name')->from(TABLE_DEPT)->fetchPairs();
        $this->view->depts = $depts;
        $this->view->showstat = $showstat;
        $this->view->userAbsent = $this->blog->getUserAbsent($day);

        foreach (array_keys($depts) as $d) {
            $this->view->deptusers[$d] = $this->dao->select('account,realname')->from(TABLE_USER)
                ->where('deleted')->eq(0)
                ->andWhere('dept')->eq($d)
                ->orderBy('account')
                ->fetchPairs();
        }

        $this->display();
    }

    public function searchbydepartment()
    {
        $dept = 0;
        $day = helper::today();
        $product = 1;

        if(!empty($_POST))
        {
            $dept = fixer::input('post')
                //->specialchars('day')
                ->get()->dept;

            $day = fixer::input('post')
                //->specialchars('day')
                ->get()->day;

            $product = fixer::input('post')
                ->get()->product;
        }

        $articles = $this->blog->getGroupReport($day, $product, $dept);

        $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        $this->view->title    = $this->lang->blog->reportproject;

        $newarticles = $this->processArticles($articles);
        $this->view->articles = $newarticles;

        $this->view->deptName = $this->dept->getById($this->app->user->dept)->name;
        $this->view->day = $day;
        $this->view->product = $product;
        $this->view->dept = $dept;
        $this->view->depts = $this->dept->getOptionMenu();

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

    public function createUserAbsent()
    {
        $userid = "";
        $day = helper::today();

        $msg = "";
        foreach (array_keys($_POST) as $p)
        {
            $msg .= $p . "\n";
        }

        //error_log("oscar: setUserAbsent :" . $msg);

        if(!empty($_POST)) {
            $postVals = fixer::input('post')
                ->get();
            $userid = $postVals->userid;
            $day = $postVals->day;
        }
        else
        {
            return false;
        }

        $this->blog->createUserAbsent($userid, $day);
        //$this->display();
    }

    public function setUserAbsent()
    {
        $userid = "";
        $day = helper::today();

        $msg = "";
        foreach (array_keys($_POST) as $p)
        {
            $msg .= $p . "\n";
        }

        //error_log("oscar: setUserAbsent :" . $msg);

        if(!empty($_POST)) {
            $postVals = fixer::input('post')
                ->get();

            //error_log("oscar: setUserAbsent postVals:" . $postVals);

            $userid = $postVals->userid;

            //error_log("oscar: setUserAbsent userid:" . $userid);

            $day = $postVals->day;

            //error_log("oscar: setUserAbsent day:" . $day);
        }
        else
        {
            return false;
        }

        $this->blog->setUserAbsent($userid, $day);
        //$this->display();
    }

    public function removeUserAbsent()
    {
        $userid = "";
        $day = helper::today();
        $msg = "";

        if(!empty($_POST)) {
            $postVals = fixer::input('post')
                ->get();

            //error_log("oscar: setUserAbsent postVals:" . $postVals);

            $userid = $postVals->userid;

            //error_log("oscar: setUserAbsent userid:" . $userid);

            $day = $postVals->day;

            //error_log("oscar: setUserAbsent day:" . $day);
        }
        else
        {
            return false;
        }

        $this->blog->removeUserAbsent($userid, $day);
        //$this->display();
    }
}

