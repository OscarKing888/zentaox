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
        //$this->view->articles = $this->pipeline->getList($pager);
        $this->view->pager    = $pager;
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
            $step->type   = 'item';
            $step->desc   = '';
            $step->expect = '';
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
        $this->view->title = $this->lang->pipeline->add;
        $this->view->depts = $this->dept->getOptionMenu();
        $this->view->steps            = $steps;

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
        $this->display();
    }

    public function restorepipeline($id)
    {
        $this->pipeline->restore($id);
        $this->locate(inlink('restore'));
    }

}
