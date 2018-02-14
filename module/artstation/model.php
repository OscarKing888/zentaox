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

class artstationModel extends model
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
        return $this->getListByUser($this->app->user->account, $pager);
    }

    function convertImageURL($arr)
    {
        $artr = array();

        foreach ($arr as $art) {
            //$art = html_entity_decode($art);
            $art = $this->file->replaceImgURL($art, $this->config->artstation->imageContentFieldName);
            $art->contentimages = htmlspecialchars_decode($art->contentimages);
            $artr[$art->id] = $art;
        }
        return $artr;
    }

    public function getDeletedList($pager = null)
    {
        $articles = $this->dao->select('*')
            ->from(TABLE_ARTSTATION)
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
            ->from(TABLE_ARTSTATION)
            ->where('owner')->eq($userid)
           ->andwhere('deleted')->eq(0)
            ->orderBy('id desc')->page($pager)->fetchAll();

        //return $this->convertImageURL($articles);


        $lst = $this->convertImageURL($articles);

        $retArr = array();
        foreach ($lst as $l) {
            $l->files = $this->loadModel('file')->getByObject('artstation', $l->id);
            $retArr[$l->id] = $l;
            /*
            error_log("****************** artid:$l->id owner:$l->owner");
            foreach ($l->files as $file) {
                    error_log(" files:$file->pathname title:$file->title");
            }
            //*/
        }

        return $retArr;
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
        $content = $this->dao->findById($id)->from(TABLE_ARTSTATION)->fetch();

        $art = ($content);
        $art = $this->file->replaceImgURL($art, $this->config->artstation->imageContentFieldName);
        $art->contentimages = htmlspecialchars_decode($art->contentimages);

        return $art;
    }

    public function logBlog($log)
    {
        if (!$this->config->artstation->debug)
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
        $article = fixer::input('post')->specialchars($this->config->artstation->fields)
            ->add('createDate', helper::now())
            ->add('owner', $this->app->user->account)
            ->stripTags($this->config->artstation->editor->create['id'], $this->config->allowedTags)
            ->remove('files,labels')
            ->get();

        //$article2 = htmlspecialchars_decode($article);

        //$articleProced = $this->file->processImgURL($article, $this->config->artstation->editor->create['id'], $this->post->uid);

        $this->dao->insert(TABLE_ARTSTATION)->data($article)
            ->autoCheck()->batchCheck('owner', 'notempty')->exec();

        $imageID = $this->dao->lastInsertID();

        error_log("oscar: **** imageID:$imageID" );

        //if(!dao::isError())
        {
            $this->file->updateObjectID($this->post->uid, $imageID, 'artstation');
            $this->file->saveUpload('artstation', $imageID);
        }
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
        $article = fixer::input('post')->specialchars($this->config->artstation->fields)
            ->stripTags($this->config->artstation->editor->edit['id'], $this->config->allowedTags)
            ->get();

        $this->dao->update(TABLE_ARTSTATION)
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
        //delete(TABLE_ARTSTATION, $id);
        $this->dao->update(TABLE_ARTSTATION)
            ->set('deleted')->eq(1)->where('id')->eq($id)->exec();
        //$this->dao->delete()->from($this->dbname)->where('id')->eq($id)->exec();
    }

    public function restore($id, $table = null)
    {
        $this->dao->update(TABLE_ARTSTATION)
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
            ->from(TABLE_ARTSTATION)
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
            ->from(TABLE_ARTSTATION)
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
            ->from(TABLE_ARTSTATION)
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
            ->from(TABLE_ARTSTATIONUserinfo)
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

        $this->dao->insert(TABLE_ARTSTATIONUserinfo)->data($data)->exec();
    }

    public function setUserAbsent($userid, $day)
    {
        //error_log("oscar: model * setUserAbsent  userid:$userid   day:$day");

        $this->dao->update(TABLE_ARTSTATIONUserinfo)
            ->set('absent')->eq(1)
            ->where('owner')->eq($userid)
            ->andWhere('date')->between(date('Y-m-d 00:00:00', strtotime($day)), date('Y-m-d 23:59:59', strtotime($day)))
            ->exec();
    }

    public function removeUserAbsent($userid, $day)
    {
        //error_log("oscar: model - removeUserAbsent  userid:$userid   day:$day");

        $this->dao->update(TABLE_ARTSTATIONUserinfo)
            ->set('absent')->eq(0)
            ->where('owner')->eq($userid)
            ->andWhere('date')->between(date('Y-m-d 00:00:00', strtotime($day)), date('Y-m-d 23:59:59', strtotime($day)))
            ->exec();
    }
}
