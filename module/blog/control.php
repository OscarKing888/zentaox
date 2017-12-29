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
        if(!empty($_POST))
        {
            $blogID = $this->blog->create();
            if(dao::isError()) die(js::error(dao::getError()) . js::locate('back'));
            die(js::locate(inlink('index')));
        }

        $products = $this->product->getPairs();
        $this->view->products = $products;
        $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        $this->view->title = $this->lang->blog->add;
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
            $products = $this->product->getPairs();
            $this->view->products = $products;
            $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
            $this->view->title   = $this->lang->blog->edit;
            $this->view->article = $this->blog->getByID($id);
            $this->display();
        }
    }

    /**
     * View an article.
     * 
     * @param  int    $id 
     * @access public
     * @return void
     */
    public function view($id)
    {
        $this->view->title   = $this->lang->blog->view;
        $this->view->article = $this->blog->getByID($id);
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
        if(!empty($_POST))
        {
            $day = fixer::input('post')
                //->specialchars('day')
                ->get()->day;
        }
        else
        {
            $day = helper::today();
        }


        $articles = $this->blog->getGroupReport($day);
        //$articles = $this->dao->select("name")->from('zt_dept')->fetchAll();
        //$articles = $this->dao->select("*")->from('gameblog')->fetchAll();

        $products = $this->product->getPairs();
        $this->view->products = $products;
        $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        $this->view->title    = $this->lang->blog->reportmyteam;
        $this->view->articles = $articles;
        $this->view->dept = $this->dept->getById($this->app->user->dept)->name;
        $this->view->day = $day;

        $this->display();
    }

    public function reportproject()
    {
        if(!empty($_POST))
        {
            $day = fixer::input('post')
                //->specialchars('day')
                ->get()->day;
        }
        else
        {
            $day = helper::today();
        }

        $articles = $this->blog->getProjectReport($day);
        //$articles = $this->dao->select("name")->from('zt_dept')->fetchAll();
        //$articles = $this->dao->select("*")->from('gameblog')->fetchAll();

        $products = $this->product->getPairs();
        $this->view->products = $products;
        $this->view->allProducts   = array(0 => '') + $this->product->getPairs('noclosed|nocode');
        $this->view->title    = $this->lang->blog->reportproject;
        $this->view->articles = $articles;
        $this->view->dept = $this->dept->getById($this->app->user->dept)->name;
        $this->view->day = $day;

        $this->display();
    }
}
