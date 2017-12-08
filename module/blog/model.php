<?php
/**
 * The model file of blog module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 * 
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
?>
<?php
class blogModel extends model
{
    public $dbname;

    public function __construct()
    {
        parent::__construct();
        $this->dbname = "gameblog";
    }
    /**
     * Get article lists.
     * 
     * @access public
     * @return array
     */
    public function getList($pager = null)
    {
        return $this->dao->select('*')
            ->from($this->dbname)
            ->where('owner')->eq($this->app->user->account)
            ->andwhere('deleted')->eq(0)
            ->orderBy('date desc')->page($pager)->fetchAll();
        //return getListByUser($this->app->user->account, $pager);
    }

    /**
     * Get article lists.
     * @access public
     * @return array
     */
    public function getListByUser($userid, $pager = null)
    {
        return $this->dao->select('*')
            ->from($this->dbname)
            ->where('owner')->eq($userid)
            ->orderBy('date desc')->page($pager)->fetchAll();
    }

    /**
     * Get an article.
     * 
     * @param  int    $id 
     * @access public
     * @return object
     */
    public function getById($id)
    {
        return $this->dao->findById($id)->from($this->dbname)->fetch();
    }

    /**
     * Create an article.
     * 
     * @access public
     * @return void
     */
    public function create()
    {
        $article = fixer::input('post')->specialchars('product,content,date')
            //->add('date', date('Y-m-d H:i:s'))
            ->add('owner', $this->app->user->account)
            ->get();
        $this->dao->insert($this->dbname)->data($article)->autoCheck()->batchCheck('owner,content', 'notempty')->exec();
        return $this->dao->lastInsertID();
    }

    /**
     * Update an article.
     * 
     * @param  int    $articleID 
     * @access public
     * @return void
     */
    public function update($articleID)
    {
        $article = fixer::input('post')->specialchars('product, content,date')->get();
        $this->dao->update($this->dbname)->data($article)->where('id')->eq($articleID)->exec();
    }

    /**
     * Delete an article.
     * 
     * @param  int     $id 
     * @param  null    $table 
     * @access public
     * @return void
     */
    public function delete($id, $table = null)
    {
        $this->dao->update($this->dbname)->set('deleted')->eq(1)->where('id')->eq($id)->exec();
        //$this->dao->delete()->from($this->dbname)->where('id')->eq($id)->exec();
    }
}
