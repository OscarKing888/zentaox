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
class books extends control
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

        $this->view->title    = $this->lang->books->index;
        $this->setupCommonVars($pager);
        $this->display();
    }

    public function setupCommonVars($pager = null, $matchDeleted = 0)
    {
        $this->view->books = $this->books->getBookList($pager, $matchDeleted);
        $this->view->borrowLogs = $this->books->getBorrowLogList();
        $this->view->librarians = ($this->config->books->adminAccounts);
        $this->view->pager    = $pager;
        $this->view->user = $this->app->user->account;
        $this->view->allUsers = $this->user->getPairs('nodeleted|noclosed|noletter');
        $this->view->bookTypes = $this->config->books->typeList;
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
            $blogID = $this->books->create();
            if(dao::isError()) die(js::error(dao::getError()) . js::locate('back'));
            die(js::locate(inlink('index')));
        }

        $this->view->title = $this->lang->books->add;

        $this->setupCommonVars();
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
            $this->books->update($id);
            $this->locate(inlink('index'));
        }
        else
        {
            $this->view->title = $this->lang->books->edit;
            $this->setupCommonVars();
            $this->view->book = $this->books->getById($id);
            $this->display();
        }
    }

    public function borrow($id)
    {
        if(!empty($_POST))
        {
            $this->books->borrow();
            $this->locate(inlink('index'));
        }
        else
        {
            $this->view->title = $this->lang->books->borrow;
            $this->setupCommonVars();
            $this->view->book = $this->books->getById($id);
            $this->display();
        }
    }

    public function returnBook($id)
    {
        $this->books->returnBook($id);
        $this->locate(inlink('index'));
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
        if(array_key_exists($this->app->user->account, $this->config->books->adminAccounts))
        {
            $this->books->delete($id);
        }

        $this->locate(inlink('index'));
    }

    public function restore($recTotal = 0, $recPerPage = 20, $pageID = 0)
    {
        $this->app->loadClass('pager');
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->setupCommonVars($pager, 1);
        $this->view->title    = $this->lang->books->restore;
        $this->view->pager    = $pager;
        $this->display();
    }

    public function restorebook($id)
    {
        $this->books->restore($id);
        $this->locate(inlink('restore'));
    }

    public function reports($recTotal = 0, $recPerPage = 20, $pageID = 0)
    {
        $this->app->loadClass('pager');
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->title = $this->lang->books->report;
        $this->setupCommonVars($pager);
        $this->view->books = $this->books->getBorrowBookList($pager);
        $this->display();
    }

    public function mybooks($recTotal = 0, $recPerPage = 20, $pageID = 0)
    {
        $this->app->loadClass('pager');
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->title = $this->lang->books->mybooks;
        $this->setupCommonVars($pager);
        $this->view->books = $this->books->getBorrowBookList($pager, $this->app->user->account);
        $this->display();
    }

    public function view($id)
    {
            $this->view->title = $this->lang->books->view;
            $this->setupCommonVars();
            $this->view->book = $this->books->getById($id);
            $this->display();
    }

}

