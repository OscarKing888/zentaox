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

class timelineModel extends model
{

    public function __construct()
    {
        parent::__construct();

        $this->loadModel('file');
    }

    /**
     * Get article lists.
     *
     * @access public
     * @return array
     */
    public function getList($pager = null)
    {
        //$articles = $this->dao->select('*')
        //->from(TABLE_BLOG)
        //->where('owner')->eq($this->app->user->account)
        //->andwhere('deleted')->eq(0)
        //->orderBy('date desc')->page($pager)->fetchAll();

        //return $this->convertImageURL($articles);
        return $this->getListByUser($this->app->user->account, $pager);
    }

    function convertImageURL($arr)
    {
        $artr = array();
        $i = 0;
        foreach ($arr as $art) {
            //$art = html_entity_decode($art);
            $art = $this->file->replaceImgURL($art, $this->config->blog->imageContentFieldName);
            $art->contentimages = htmlspecialchars_decode($art->contentimages);
            $artr[$i] = $art;
            $i++;
        }
        return $artr;
    }

    public function getDeletedList($pager = null)
    {
        $articles = $this->dao->select('*')
            ->from(TABLE_BLOG)
            ->where('owner')->eq($this->app->user->account)
            ->andwhere('deleted')->eq(1)
            ->orderBy('date desc')->page($pager)->fetchAll();

        return $this->convertImageURL($articles);
    }


    /**
     * Get an article.
     *
     * @param  int $id
     * @access public
     * @return object
     */
    public function getById($id)
    {
        $content = $this->dao->findById($id)->from(TABLE_BLOG)->fetch();

        $art = ($content);
        $art = $this->file->replaceImgURL($art, $this->config->blog->imageContentFieldName);
        $art->contentimages = htmlspecialchars_decode($art->contentimages);

        return $art;
    }

    public function logBlog($log)
    {
        if (!$this->config->blog->debug)
            return false;

        if (!is_writable($this->app->getLogRoot()))
            return false;

        $file = $this->app->getLogRoot() . 'blog.' . date('Ymd') . '.log.php';
        if (!is_file($file)) $log = "<?php\n die();\n" . $log . "\n";

        $fp = fopen($file, "a");
        fwrite($fp, $log);
        fclose($fp);
    }

    /**
     * Create an article.
     *
     * @access public
     * @return void
     */
    public function create()
    {
        $article = fixer::input('post')->specialchars($this->config->blog->fields)
            //->add('date', date('Y-m-d H:i:s'))
            //->add('owner', $this->app->user->account)
            ->stripTags($this->config->timeline->editor->create['id'], $this->config->allowedTags)
            ->get();

        $article2 = htmlspecialchars_decode($article);

        $articleProced = $this->file->processImgURL($article, $this->config->blog->editor->create['id'], $this->post->uid);


        //$this->logBlog("\n====id:" . $this->post->uid . "\nraw:" . $article->contentimages . "\nbefore:" . $article2->contentimages . " \n after:"
          //  . $articleProced->contentimages . "\ntools:" . $this->config->blog->editor->create['id']);

        $this->dao->insert(TABLE_TIMELINE)->data($articleProced)
            ->autoCheck()->batchCheck('title', 'notempty')->exec();

        return $this->dao->lastInsertID();
    }

    /**
     * Update an article.
     *
     * @param  int $articleID
     * @access public
     * @return void
     */
    public function update($articleID)
    {
        $article = fixer::input('post')->specialchars($this->config->blog->fields)
            ->stripTags($this->config->blog->editor->edit['id'], $this->config->allowedTags)
            ->get();

        $this->dao->update(TABLE_BLOG)
            ->data($article)->where('id')->eq($articleID)->exec();
    }

    /**
     * Delete an article.
     *
     * @param  int $id
     * @param  null $table
     * @access public
     * @return void
     */
    public function delete($id)
    {
        //delete(TABLE_BLOG, $id);
        $this->dao->update(TABLE_BLOG)
            ->set('deleted')->eq(1)->where('id')->eq($id)->exec();
        //$this->dao->delete()->from($this->dbname)->where('id')->eq($id)->exec();
    }

    public function restore($id, $table = null)
    {
        $this->dao->update(TABLE_BLOG)
            ->set('deleted')->eq(0)->where('id')->eq($id)->exec();
    }

    public function ajaxGetTimelineEvents()
    {
        $articles = $this->dao->select('*')
            ->from(TABLE_TIMELINE)
            ->where('deleted')->eq(0)
            ->orderBy('datebegin asc')->fetchAll();

        return $this->convertImageURL($articles);
    }

}
