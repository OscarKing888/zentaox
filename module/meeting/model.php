<?php
/**
 * The model file of meeting module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     meeting
 * @version     $Id: model.php 5035 2013-07-06 05:21:58Z wyd621@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php
class meetingModel extends model
{
    /**
     * Create batch meeting
     * 
     * @access public
     * @return void
     */
    public function batchCreate()
    {
        $meetings = fixer::input('post')->get();
        for($i = 0; $i < $this->config->meeting->batchCreate; $i++)
        {
            if($meetings->descs[$i] != '')
            {
                $meeting          = new stdclass();
                $meeting->createDate = helper::now();
                $meeting->pri     = $meetings->pris[$i];
                $meeting->description    = $meetings->descs[$i];
                $meeting->assignedTo    = $meetings->assignedTos[$i];
                $meeting->deadline    = $meetings->deadlines[$i];
                $meeting->status  = "wait";

                //error_log("################# oscar: add meet:$meeting->description");

                $this->dao->insert(TABLE_MEETING)->data($meeting)->autoCheck()->exec();
                if(dao::isError()) 
                {
                    echo js::error(dao::getError());
                    die(js::reload('parent'));
                }
                $todoID = $this->dao->lastInsertID();
                $this->loadModel('score')->create('meeting', 'create', $todoID);
                $this->loadModel('action')->create('meeting', $todoID, 'opened');
            }
            else
            {
                unset($meetings->pris[$i]);
                unset($meetings->descs[$i]);
                unset($meetings->assignedTo[$i]);
                unset($meetings->deadline[$i]);
            }
        }
    }

    /**
     * update a meeting.
     * 
     * @param  int    $todoID 
     * @access public
     * @return void
     */
    public function update($todoID)
    {
        $oldTodo = $this->dao->findById((int)$todoID)->from(TABLE_MEETING)->fetch();
        if(in_array($oldTodo->type, array('bug', 'task', 'story'))) $oldTodo->name = '';
        $pst = fixer::input('post')
            //->cleanInt('date, pri, begin, end, private')
            //->setIF(in_array($oldTodo->type, array('bug', 'task', 'story')), 'name', '')
            //->setIF($this->post->date  == false, 'date', '2030-01-01')
            //->setIF($this->post->begin == false, 'begin', '2400')
            //->setIF($this->post->end   == false, 'end', '2400')
            //->setDefault('private', 0)
            //->stripTags($this->config->meeting->editor->edit['id'], $this->config->allowedTags)
            //->remove('uid')
            ->get();



        $meeting = new stdClass();
        $meeting->pri     = $pst->pri;
        $meeting->description     = $pst->description;
        $meeting->assignedTo     = $pst->assignedTo;
        $meeting->deadline     = $pst->deadline;
        $meeting->status     = $pst->status;

        //$meeting = $this->loadModel('file')->processImgURL($meeting, $this->config->meeting->editor->edit['id'], $this->post->uid);
        $this->dao->update(TABLE_MEETING)->data($meeting)
            ->autoCheck()
            //->check($this->config->meeting->edit->requiredFields, 'notempty')
            ->where('id')->eq($todoID)
            ->exec();

        if(!dao::isError())
        {
            //$this->file->updateObjectID($this->post->uid, $todoID, 'meeting');
            return common::createChanges($oldTodo, $meeting);
        }
    }

    /**
     * Batch update todos.
     * 
     * @access public
     * @return array
     */
    public function batchUpdate()
    {
        $meetings      = array();
        $allChanges = array();
        $pst       = fixer::input('post')->get();
        $todoIDList = $this->post->meetingIDList ? $this->post->meetingIDList : array();

        /*
        error_log("************ meeting batchUpdate:" . count($todoIDList));
        foreach ($pst as $k => $v) {
            error_log("pst:$k => $v");
        }
        //*/

        if(!empty($todoIDList))
        {
            /* Initialize todos from the post data. */
            foreach($todoIDList as $todoID)
            {
                $meeting = new stdclass();
                $meeting          = new stdclass();
                $meeting->createDate = helper::now();
                $meeting->pri     = $pst->pris[$todoID];
                $meeting->description    = $pst->descs[$todoID];
                $meeting->assignedTo    = $pst->assignedTos[$todoID];
                $meeting->deadline    = $pst->deadlines[$todoID];
                $meeting->status    = $pst->statuss[$todoID];
                $meetings[$todoID] = $meeting;
            }

            $oldTodos = $this->dao->select('*')->from(TABLE_MEETING)->where('id')->in(array_keys($meetings))->fetchAll('id');
            foreach($meetings as $todoID => $meeting)
            {
                $oldTodo = $oldTodos[$todoID];
                //if($oldTodo->type == 'bug' or $oldTodo->type == 'task') $oldTodo->name = '';
                $this->dao->update(TABLE_MEETING)->data($meeting)
                    ->autoCheck()
                    ->batchCheck($this->config->meeting->edit->requiredFields, 'notempty')
                    ->where('id')->eq($todoID)
                    ->exec();

                if($oldTodo->status != 'done' and $meeting->status == 'done') $this->loadModel('action')->create('meeting', $todoID, 'finished', '', 'done');

                if(!dao::isError()) 
                {
                    $allChanges[$todoID] = common::createChanges($oldTodo, $meeting);
                }
                else
                {
                    die(js::error('meeting#' . $todoID . dao::getError(true)));
                }
            }
        }

        return $allChanges;
    }

    /**
     * Change the status of a meeting.
     * 
     * @param  string $todoID 
     * @param  string $status 
     * @access public
     * @return void
     */
    public function finish($todoID)
    {
        $this->dao->update(TABLE_MEETING)->set('status')->eq('done')->where('id')->eq((int)$todoID)->exec();
        $this->loadModel('action')->create('meeting', $todoID, 'finished', '', 'done');
        return;
    }

    public function activate($todoID)
    {
        $this->dao->update(TABLE_MEETING)->set('status')->eq('wait')->where('id')->eq((int)$todoID)->exec();
        $this->loadModel('action')->create('meeting', $todoID, 'done', '', 'wait');
        return;
    }

    /**
     * Get info of a meeting.
     * 
     * @param  int    $todoID 
     * @param  bool   $setImgSize
     * @access public
     * @return object|bool
     */
    public function getById($todoID, $setImgSize = false)
    {
        $meeting = $this->dao->findById((int)$todoID)->from(TABLE_MEETING)->fetch();
        if(!$meeting) return false;
        //$meeting = $this->loadModel('file')->replaceImgURL($meeting, 'desc');
        //if($setImgSize) $meeting->description = $this->file->setImgSize($meeting->description);
        //if($meeting->type == 'story') $meeting->name = $this->dao->findById($meeting->idvalue)->from(TABLE_STORY)->fetch('title');
        //if($meeting->type == 'task')  $meeting->name = $this->dao->findById($meeting->idvalue)->from(TABLE_TASK)->fetch('name');
        //if($meeting->type == 'bug')   $meeting->name = $this->dao->findById($meeting->idvalue)->from(TABLE_BUG)->fetch('title');
        //$meeting->date = str_replace('-', '', $meeting->date);
        return $meeting;
    }

    /**
     * Get meeting list of a user.
     * 
     * @param  date   $date 
     * @param  string $account 
     * @param  string $status   all|today|thisweek|lastweek|before, or a date.
     * @param  int    $limit    
     * @access public
     * @return void
     */
    public function getList($date = 'today', $account = '', $status = 'all', $limit = 0, $pager = null, $orderBy="createDate")
    {
        //error_log("=== meeting getList date:$date account:$account status:$status");
        $this->app->loadClass('date');
        $meetings = array();
        $date = strtolower($date);

        if($date == 'today') 
        {
            $begin = date::today();
            $end   = $begin;
        }
        elseif($date == 'yesterday') 
        {
            $begin = date::yesterday();
            $end   = $begin;
        }
        elseif($date == 'thisweek')
        {
            extract(date::getThisWeek());
        }
        elseif($date == 'lastweek')
        {
            extract(date::getLastWeek());
        }
        elseif($date == 'thismonth')
        {
            extract(date::getThisMonth());
        }
        elseif($date == 'lastmonth')
        {
            extract(date::getLastMonth());
        }
        elseif($date == 'thisseason')
        {
            extract(date::getThisSeason());
        }
        elseif($date == 'thisyear')
        {
            extract(date::getThisYear());
        }
        elseif($date == 'future')
        {
            $begin = '2030-01-01';
            $end   = $begin;
        }
        elseif($date == 'all')
        {
            $begin = '1970-01-01';
            $end   = '2109-01-01';
        }
        elseif($date == 'before')
        {
            $begin = '1970-01-01';
            //$end   = date::yesterday();
            $end = '2030-01-01';
        }
        else
        {
            $begin = $end = $date;
        }

        if($account == '')   $account = $this->app->user->account;

        $meetings = $this->dao->select('*')->from(TABLE_MEETING)
            //->where('account')->eq($account)
            ->where('deadline')->ge($begin)
            ->andWhere('deadline')->le($end)
            ->beginIF($status != 'all' and $status != 'undone')->andWhere('status')->in($status)->fi()
            ->beginIF($status == 'undone')->andWhere('status')->ne('done')->fi()
            ->orderBy($orderBy)
            //->beginIF($limit > 0)->limit($limit)->fi()
            ->page($pager)
            ->fetchAll();

        return $meetings;
    }

    /**
     * Judge an action is clickable or not.
     * 
     * @param  object    $meeting 
     * @param  string    $action 
     * @access public
     * @return bool
     */
    public static function isClickable($meeting, $action)
    {
        $action = strtolower($action);

        if($action == 'finish') return $meeting->status != 'done';

        return true;
    }
}
