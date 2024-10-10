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
        //$this->loadModel('story');
    }

    /**
     * Get article lists.
     *
     * @access public
     * @return array
     */
    public function getList($pager = null)
    {
        $articles = $this->dao->select('*')
            ->from(TABLE_ARTSTATION)
            ->where('deleted')->eq(0)
            ->orderBy('id desc')
            ->page($pager)
            ->fetchAll();

        return $this->convertImageURL($articles);
    }

    public function getByStoryID($storyID)
    {
        $articles = $this->dao->select('*')
            ->from(TABLE_ARTSTATION)
            ->where('deleted')->eq(0)
            ->andWhere('story')->eq($storyID)
            ->orderBy('id desc')
            ->fetchAll();

        return $this->convertImageURL($articles);
    }

    function convertImageURL($arr)
    {
        $artr = array();

        foreach ($arr as $art) {
            //$art = html_entity_decode($art);
            //$art = $this->file->replaceImgURL($art, $this->config->artstation->imageContentFieldName);
            //$art->contentimages = htmlspecialchars_decode($art->contentimages);
            $art->files = $this->loadModel('file')->getByObject('artstation', $art->id);

            $artr[$art->id] = $art;
        }

        return $artr;
    }

    public function getDeletedList($pager = null)
    {
        $articles = $this->dao->select('*')
            ->from(TABLE_ARTSTATION)
            //->where('owner')->eq($this->app->user->account)
            ->where('deleted')->eq(1)
            ->orderBy('id desc')->page($pager)->fetchAll();

        return $this->convertImageURL($articles);
    }


    public function getListByUser($userid, $pager = null)
    {
        $articles = $this->dao->select('*')
            ->from(TABLE_ARTSTATION)
            ->where('owner')->eq($userid)
            ->andwhere('deleted')->eq(0)
            ->orderBy('id desc')->page($pager)->fetchAll();

        //return $this->convertImageURL($articles);


        return $this->convertImageURL($articles);
    }

    public function getComments($imageid)
    {
        $comments = $this->dao->select('*')
            ->from(TABLE_ARTSTATION_COMMENT)
            ->where('imageid')->eq($imageid)
            ->andwhere('deleted')->eq(0)
            ->orderBy('id asc')
            ->fetchAll();

        return $comments;
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
        $art = $this->dao->select()->from(TABLE_ARTSTATION)
            ->where('id')->eq($id)
            ->fetch();

        $art->files = $this->loadModel('file')->getByObject('artstation', $art->id);
        $art->likes = $this->dao->select("user,imageId")->from(TABLE_ARTSTATION_LIKE)
            ->where('imageId')->eq($id)
            ->fetchPairs();

        if($art->story > 0)
        {
            $this->loadModel('story');
            $art->storydat = $this->story->getByID($art->story);
        }

        return $art;
    }

    public function like($user, $imageid)
    {
        $lk = $this->dao->select()->from(TABLE_ARTSTATION_LIKE)
            ->where('imageId')->eq($imageid)
            ->andWhere('user')->eq($user)
            ->fetch();

        if(!empty($lk))
        {
            error_log("oscar: already liked by user:$user with:$imageid");
            return;
        }

        $data = new stdclass();
        $data->user = $user;
        $data->imageId = $imageid;
        $this->dao->insert(TABLE_ARTSTATION_LIKE)->data($data)->exec();
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
            //->add('owner', $this->app->user->account)
            ->stripTags($this->config->artstation->editor->create['id'], $this->config->allowedTags)
            ->remove('files,labels')
            ->get();

        //$article2 = htmlspecialchars_decode($article);

        //$articleProced = $this->file->processImgURL($article, $this->config->artstation->editor->create['id'], $this->post->uid);

        $article->tags = artstationModel::filterTags($article->tags);

        $this->dao->insert(TABLE_ARTSTATION)->data($article)
            ->autoCheck()->batchCheck('owner', 'notempty')->exec();

        $imageID = $this->dao->lastInsertID();

        //error_log("oscar: **** imageID:$imageID" );

        //if(!dao::isError())
        {
            $this->file->updateObjectID($this->post->uid, $imageID, 'artstation');
            $this->file->saveUpload('artstation', $imageID);
        }
        return $imageID;
    }

    public static function filterTags($tags)
    {
        $tags = str_ireplace(' ', '', $tags);
        $tags = str_ireplace('，', ',', $tags);
        $tags = validater::filterTrojan($tags);
        return $tags;
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
            ->remove('files,labels')
            ->get();

        $article->tags = artstationModel::filterTags($article->tags);

        $this->dao->update(TABLE_ARTSTATION)
            ->data($article)->where('id')->eq($articleID)->exec();

        $this->file->updateObjectID($this->post->uid, $articleID, 'artstation');
        $this->file->saveUpload('artstation', $articleID);
    }

    public function confirmtomodeling($articleID, $confirmToModelingFileID)
    {
        $this->dao->update(TABLE_ARTSTATION)
            ->set('confirmdesign')->eq($confirmToModelingFileID)
            ->set('confirmdate')->eq(helper::now())
            ->where('id')->eq($articleID)->exec();
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

    public function search($pager, $product, $type, $tags, $owner)
    {
        $articles = $this->dao->select('*')
            ->from(TABLE_ARTSTATION)
            ->where('deleted')->eq(0)
            ->beginIF($product > 0)->andWhere('product')->eq($product)->fi()
            ->beginIF($type >= 0)->andWhere('type')->eq($type)->fi()
            ->beginIF($owner != '' )->andWhere('owner')->eq($owner)->fi()
            ->beginIF($tags != '' )->andWhere('tags')->like('%' . $tags . '%')->fi()
            ->orderBy('id desc')
            ->page($pager)
            ->fetchAll();

        $articles = $this->convertImageURL($articles);

        return $articles;
    }

}
