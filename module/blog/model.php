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
        //->from($this->config->blog->dbname)
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
            ->from($this->config->blog->dbname)
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
            ->from($this->config->blog->dbname)
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
        $content = $this->dao->findById($id)->from($this->config->blog->dbname)->fetch();

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

        $article2 = htmlspecialchars_decode($article);

        $articleProced = $this->file->processImgURL($article, $this->config->blog->editor->create['id'], $this->post->uid);


        $this->logBlog("\n====id:" . $this->post->uid . "\nraw:" . $article->contentimages . "\nbefore:" . $article2->contentimages . " \n after:"
            . $articleProced->contentimages . "\ntools:" . $this->config->blog->editor->create['id']);

        $this->dao->insert($this->config->blog->dbname)->data($articleProced)
            ->autoCheck()->batchCheck('owner,content', 'notempty')->exec();

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

        $this->dao->update($this->config->blog->dbname)
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
        //delete($this->config->blog->dbname, $id);
        $this->dao->update($this->config->blog->dbname)
            ->set('deleted')->eq(1)->where('id')->eq($id)->exec();
        //$this->dao->delete()->from($this->dbname)->where('id')->eq($id)->exec();
    }

    public function restore($id, $table = null)
    {
        $this->dao->update($this->config->blog->dbname)
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
            ->from($this->config->blog->dbname)
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
            ->from($this->config->blog->dbname)
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
            ->from($this->config->blog->dbname)
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
