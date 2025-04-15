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

    public function __construct()
    {
        parent::__construct();

        $this->loadModel('dept');
        $this->loadModel('file');
        $this->loadModel('user');
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
            //$newContent = htmlspecialchars_decode($art->content);
            //error_log("--- $newContent\n" . $art->content);
            //$art->content = $newContent;

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
     * Get article lists.
     * @access public
     * @return array
     */
    public function getListByUser($userid, $pager = null)
    {
        $articles = $this->dao->select('*')
            ->from(TABLE_BLOG)
            ->where('owner')->eq($userid)
            ->andwhere('deleted')->eq(0)
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
            ->add('owner', $this->app->user->account)
            ->stripTags($this->config->blog->editor->create['id'], $this->config->allowedTags)
            ->get();

        //$imagesContent = htmlspecialchars_decode($article->contentimages);

        $articleProced = $this->file->processImgURL($article, $this->config->blog->editor->create['id'], $this->post->uid);

        //$articleProced->content = htmlspecialchars($articleProced->content);

        //$this->logBlog("\n====id:" . $this->post->uid . "\nraw:" . $article->contentimages . "\nbefore:" . $imagesContent . " \n after:"
          //  . $articleProced->contentimages . "\ntools:" . $this->config->blog->editor->create['id']);

        $this->dao->insert(TABLE_BLOG)->data($articleProced)
            ->autoCheck()->batchCheck('owner,content', 'notempty')->exec();

        //error_log('blog.create new:' . $articleProced->contentimages . ' old:' . $article->contentimages);

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
        $article = fixer::input('post')
            //->specialchars($this->config->blog->fields)
            //->stripTags($this->config->blog->editor->edit['id'], $this->config->allowedTags)
            ->get();

       //$article->content = htmlspecialchars_decode($article->content);

        //error_log("##### blog . update content:" . $article->content);
        $article = $this->file->processImgURL($article, $this->config->blog->editor->create['id'], $this->post->uid);

        $this->dao->update(TABLE_BLOG)
            ->data($article)->where('id')->eq($articleID)->exec();
    }

    public function createOrUpdate($text, $product)
    {
        //error_log("+++ blog.createOrUpdate product:$product content:$text");

        $myAccount = $this->app->user->account;
        $oldBlog = $this->dao->select('*, DATE_FORMAT(date, \'%Y-%m-%d\') ')->from(TABLE_BLOG)
            ->where('owner')->eq($myAccount)
            ->andWhere('DATE(date)')->eq(helper::today())
            ->andWhere('product')->eq($product)
            ->andWhere('deleted')->eq(0)
            ->fetch();

        //error_log("+++ blog.createOrUpdate $oldBlog 1 sql:" . $this->dao->get());

        if(empty($oldBlog))
        {
            $newBlog = new stdClass();
            $newBlog->owner = $myAccount;
            $newBlog->date = helper::now();
            //$newBlog->content = htmlspecialchars($text);
            $newBlog->content = $text;
            $newBlog->product = $product;

            $newBlog = $this->file->processImgURL($newBlog, $this->config->blog->editor->create['id'], $this->post->uid);

            $this->dao->insert(TABLE_BLOG)->data($newBlog)
                ->autoCheck()->batchCheck('owner,content', 'notempty')->exec();

            //error_log("+++ blog.createOrUpdate $oldBlog 2 sql:" . $this->dao->get());
        }
        else {
            $blogDat = $oldBlog;
            //$blogDat->content = htmlspecialchars($oldBlog->content . '' . $text);
            $blogDat->content = ($oldBlog->content . "" . $text);
            $blogDat = $this->file->processImgURL($blogDat, $this->config->blog->editor->create['id'], $this->post->uid);

            $this->dao->update(TABLE_BLOG)
                ->data($blogDat)->where('id')->eq($oldBlog->id)->exec();

            //error_log("+++ blog.createOrUpdate $oldBlog 3 sql:" . $this->dao->get());
        }
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

    public function getGroupReport($day, $product, $dept)
    {
        //error_log("oscar: [getGroupReport] day:$day product:$product dept:$dept");

        $deptUsers = $this->dao->select('account')->from(TABLE_USER)
            ->where('dept')->eq($dept)
            //->fetchAll();
            ->fetchAll('account');

        //foreach ($deptUsers as $depu){ error_log('=====>depu:' . $depu->id);        }

        $dptus = array_keys($deptUsers);
        //foreach ($dptus as $depu)        {  error_log('=====depu:' . $depu);        }

        //error_log("=== [getGroupReport]" . $day);
        $articles = $this->dao->select('*')
            ->from(TABLE_BLOG)
            ->where('owner')->in($dptus)
            ->andwhere('deleted')->eq(0)
            ->andWhere('date')->between(date('Y-m-d 00:00:00', strtotime($day)), date('Y-m-d 23:59:59', strtotime($day)))
            //->andwhere('product')->eq($product)
            ->orderBy('date asc')
            ->fetchAll();

        $articles = $this->convertImageURL($articles);
        /*
        foreach ($articles as $art) {
            $art->contentimages = $this->file->setImgSize($art->contentimages, 512);
        }
        //*/
        return $articles;
    }

    public function getProjectReport($day, $product)
    {
        //error_log("=== [getProjectReport]" . $day);
        $articles = $this->dao->select('*')
            ->from(TABLE_BLOG)
            ->where('date')->between(date('Y-m-d 00:00:00', strtotime($day)), date('Y-m-d 23:59:59', strtotime($day)))
            ->andwhere('deleted')->eq(0)
            ->andwhere('product')->eq($product)
            ->orderBy('date asc')
            ->fetchAll();

        $articles = $this->convertImageURL($articles);
        /*
        foreach ($articles as $art) {
            $art->contentimages = $this->file->setImgSize($art->contentimages, 512);
        }
        //*/
        return $articles;
    }

    public function getAllReport($day)
    {
        $articles = $this->dao->select('*')
            ->from(TABLE_BLOG)
            ->where('date')->between(date('Y-m-d 00:00:00', strtotime($day)), date('Y-m-d 23:59:59', strtotime($day)))
            ->andwhere('deleted')->eq(0)
            ->orderBy('date asc')
            ->fetchAll();

        $articles = $this->convertImageURL($articles);

        return $articles;
    }

    public function getUserAbsent($day)
    {
        $userinfo = $this->dao->select('*')
            ->from($this->config->blog->dbnameUserinfo)
            ->where('date')->between(date('Y-m-d 00:00:00', strtotime($day)), date('Y-m-d 23:59:59', strtotime($day)))
            ->fetchAll();

        return $userinfo;
    }

    public function createUserAbsent($userid, $day)
    {
        //error_log("oscar: model + createUserAbsent  userid:$userid   day:$day");

        $data = new stdclass();
        $data->owner = $userid;
        $data->date = $day;
        $data->absent = 1;

        $this->dao->insert($this->config->blog->dbnameUserinfo)->data($data)->exec();
    }

    public function setUserAbsent($userid, $day)
    {
        //error_log("oscar: model * setUserAbsent  userid:$userid   day:$day");

        $this->dao->update($this->config->blog->dbnameUserinfo)
            ->set('absent')->eq(1)
            ->where('owner')->eq($userid)
            ->andWhere('date')->between(date('Y-m-d 00:00:00', strtotime($day)), date('Y-m-d 23:59:59', strtotime($day)))
            ->exec();
    }

    public function removeUserAbsent($userid, $day)
    {
        //error_log("oscar: model - removeUserAbsent  userid:$userid   day:$day");

        $this->dao->update($this->config->blog->dbnameUserinfo)
            ->set('absent')->eq(0)
            ->where('owner')->eq($userid)
            ->andWhere('date')->between(date('Y-m-d 00:00:00', strtotime($day)), date('Y-m-d 23:59:59', strtotime($day)))
            ->exec();
    }
}
