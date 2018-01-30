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

class booksModel extends model
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
    public function getBookList($pager = null, $matchDeleted)
    {
        $books = $this->dao->select('*')
            ->from(TABLE_BOOKS)
            ->where('deleted')->eq($matchDeleted)
            ->orderBy('id asc')
            ->page($pager)
            ->fetchAll();

        return $this->convertImageURL($books);
    }

    public function getBorrowBookList($pager = null, $matchAccount='')
    {
        $logs = $this->dao->select()
            ->from(TABLE_BOOKSBORROWLOG)
            ->where('returned')->eq(0)
            ->beginIF($matchAccount != '')->andWhere('reader')->eq($matchAccount)->fi()
            ->fetchAll('bookid');

        $books = $this->dao->select('*')
            ->from(TABLE_BOOKS)
            ->where('id')->in(array_keys($logs))
            ->orderBy('id asc')
            ->page($pager)
            ->fetchAll();

        return $this->convertImageURL($books);
    }

    public function getBorrowLogList()
    {
        $logs = $this->dao->select('*')
            ->from(TABLE_BOOKSBORROWLOG)
            ->where('returned')->eq(0)
            ->fetchAll();

        $bookLogs = array();
        foreach ($logs as $log) {
            $bookLogs[$log->bookid] = $log;
        }

        return $bookLogs;
    }

    function convertImageURL($arr)
    {
        $artr = array();
        $i = 0;
        foreach ($arr as $art) {
            //$art = html_entity_decode($art);
            $art = $this->file->replaceImgURL($art, $this->config->books->imageContentFieldName);
            $art->contentimages = htmlspecialchars_decode($art->contentimages);
            $artr[$i] = $art;
            $i++;
        }
        return $artr;
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
        $book = $this->dao->findById($id)->from(TABLE_BOOKS)->fetch();
        $book = $this->file->replaceImgURL($book, $this->config->books->imageContentFieldName);
        $book->desc = htmlspecialchars_decode($book->desc);

        return $book;
    }



    /**
     * Create an article.
     *
     * @access public
     * @return void
     */
    public function create()
    {
        $book = fixer::input('post')->specialchars($this->config->books->fields)
            ->stripTags($this->config->books->editor->create['id'], $this->config->allowedTags)
            ->add('registerDate', helper::now())
            ->get();

        $bookProcessed = $this->file->processImgURL($book, $this->config->books->editor->create['id'], $this->post->uid);

        $this->dao->insert(TABLE_BOOKS)
            ->data($bookProcessed)
            ->autoCheck()
            ->batchCheck('bookName', 'notempty')
            ->exec();

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
        $article = fixer::input('post')->specialchars($this->config->books->fields)
            ->stripTags($this->config->books->editor->edit['id'], $this->config->allowedTags)
            ->get();

        $this->dao->update(TABLE_BOOKS)
            ->data($article)->where('id')->eq($articleID)->exec();
    }

    public function borrow()
    {
        $borrowLog = fixer::input('post')->specialchars($this->config->books->borrowFields)
            ->add('borrowDate', helper::now())
            ->get();

        $this->dao->insert(TABLE_BOOKSBORROWLOG)
            ->data($borrowLog)
            ->autoCheck()
            ->batchCheck('bookid,reader', 'notempty')
            ->exec();
    }

    public function returnBook($id)
    {
        $borrowLog = fixer::input('post')->specialchars($this->config->books->borrowFields)
            ->add('borrowDate', helper::now())
            ->get();

        $this->dao->update(TABLE_BOOKSBORROWLOG)
            ->set('returned')->eq(1)
            ->set('returnDate')->eq(helper::now())
            ->where('bookid')->eq($id)
            ->exec();
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
        $this->dao->update(TABLE_BOOKS)
            ->set('deleted')->eq(1)->where('id')->eq($id)->exec();
    }

    public function restore($id, $table = null)
    {
        $this->dao->update(TABLE_BOOKS)
            ->set('deleted')->eq(0)->where('id')->eq($id)->exec();
    }

}
