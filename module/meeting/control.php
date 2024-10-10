<?php

/**
 * The control file of todo module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     todo
 * @version     $Id: control.php 4976 2013-07-02 08:15:31Z wyd621@gmail.com $
 * @link        http://www.zentao.net
 */
class meeting extends control
{
    /**
     * Construct function, load model of task, bug, my.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->app->loadClass('date');
        //$this->loadModel('task');
        //$this->loadModel('bug');
        //$this->loadModel('my')->setMenu();
    }

    public function index($type = 'all', $account = '', $status = 'all', $orderBy = "createDate_desc", $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        //dao::$debug_log_sql = false;

        /* Save session. */
        $uri = $this->app->getURI(true);
        $this->session->set('meetingList', $uri);
        //error_log("meetingList:$uri");

        /*
        if($this->app->viewType != 'json')
        {
            $this->session->set('todoList', $uri);
            $this->session->set('bugList',  $uri);
            $this->session->set('taskList', $uri);
        }

        $this->view->title      = $this->lang->my->common . $this->lang->colon . $this->lang->my->todo;
        $this->view->position[] = $this->lang->my->todo;



        $this->view->times        = date::buildTimeList($this->config->todo->times->begin, $this->config->todo->times->end, $this->config->todo->times->delta);
        $this->view->time         = date::now();
        $this->view->members      = $this->loadModel('user')->getPairs();
        $this->view->importFuture = ($type != 'today');
    */
        $this->app->loadClass('pager', $static = true);
        if ($this->app->getViewType() == 'mhtml') $recPerPage = 10;
        $pager = pager::init($recTotal, $recPerPage, $pageID);
        $sort = $this->loadModel('common')->appendOrder($orderBy);

        $this->view->date = (int)$type == 0 ? date(DT_DATE1) : date(DT_DATE1, strtotime($type));
        $this->view->type = $type;

        $this->view->recTotal     = $recTotal;
        $this->view->recPerPage   = $recPerPage;
        $this->view->pageID       = $pageID;
        $this->view->status       = $status;
        $this->view->account      = $this->app->user->account;
        $this->view->orderBy      = $orderBy;// == 'date_desc,status,begin,id_desc' ? '' : $orderBy;

        $this->view->importFuture = ($type != 'today');

        $this->view->pager = $pager;
        $this->view->meetings = $this->meeting->getList($type, $account, $status, 0, $pager, $sort);
        $this->view->users = $this->loadModel('group')->getUserPairs($this->config->meeting->assignToGroupName);

        $this->display();
    }

    /**
     * Batch create todo
     *
     * @param  string $date
     * @param  string $account
     * @access public
     * @return void
     */
    public function batchCreate($date = 'today', $account = '')
    {
        if ($date == 'today') $date = date(DT_DATE1, time());

        if (!empty($_POST)) {
            $this->meeting->batchCreate();
            if (dao::isError()) die(js::error(dao::getError()));

            /* Locate the browser. */
            $date = str_replace('-', '', $this->post->date);
            if ($date == '') {
                $date = 'future';
            } else if ($date == date('Ymd')) {
                $date = 'today';
            }
            die(js::locate($this->createLink('meeting', 'index', "type=all"), 'parent'));
        }

        /* Set Custom*/
        foreach (explode(',', $this->config->meeting->list->customBatchCreateFields) as $field) $customFields[$field] = $this->lang->meeting->$field;
        $this->view->customFields = $customFields;
        $this->view->showFields = $this->config->meeting->custom->batchCreateFields;

        $this->view->title = $this->lang->meeting->common . $this->lang->colon . $this->lang->meeting->batchCreate;
        //$this->view->position[] = $this->lang->meeting->common;
        //$this->view->position[] = $this->lang->meeting->batchCreate;
        //$this->view->date       = (int)$date == 0 ? $date : date('Y-m-d', strtotime($date));
        //$this->view->times      = date::buildTimeList($this->config->meeting->times->begin, $this->config->meeting->times->end, $this->config->meeting->times->delta);
        //$this->view->time       = date::now();
        $this->view->users = $this->loadModel('group')->getUserPairs($this->config->meeting->assignToGroupName);

        $this->display();
    }

    /**
     * Edit a todo.
     *
     * @param  int $todoID
     * @access public
     * @return void
     */
    public function edit($todoID)
    {
        if (!empty($_POST)) {
            $changes = $this->meeting->update($todoID);
            if (dao::isError()) die(js::error(dao::getError()));
            if ($changes) {
                $actionID = $this->loadModel('action')->create('meeting', $todoID, 'edited');
                $this->action->logHistory($actionID, $changes);
            }
            if (isonlybody()) die(js::closeModal('parent.parent'));
            die(js::locate(inlink('view', "todoID=$todoID"), 'parent'));
        }

        /* Judge a private todo or not, If private, die. */
        $meeting = $this->meeting->getById($todoID);
        //if($meeting->private and $this->app->user->account != $meeting->account) die('private');

        //$meeting->date = date("Y-m-d", strtotime($meeting->date));
        $this->view->title = $this->lang->meeting->description;
        $this->view->todo = $meeting;
        $this->view->users = $this->loadModel('group')->getUserPairs($this->config->meeting->assignToGroupName);
        $this->display();
    }

    /**
     * Batch edit todo.
     *
     * @param  string $from example:myTodo, todoBatchEdit.
     * @param  string $type
     * @param  string $account
     * @param  string $status
     * @access public
     * @return void
     */
    public function batchEdit($from = '')
    {
        $type = 'all';
        $account = '';
        $status = 'all';

        /*
        if (!empty($_POST)) {
            var_dump($_POST);
        }
        //*/

        //error_log("_____________________________ meeting::batchEdit from:$from");

        if ($from == 'edit') {
            /* Initialize vars. */
            $editedTodos = array();
            $todoIDList = array();

            if ($account == '') $account = $this->app->user->account;
            $allTodos = $this->meeting->getList($type, $account, $status);
            if ($this->post->meetingIDList) {
                $todoIDList = $this->post->meetingIDList;
            }

            /*
            foreach ($todoIDList as $idx) {
                error_log("idx:$idx");
            }
            */

            /* Initialize meetings whose need to edited. */
            foreach ($allTodos as $meeting) {
                //error_log("meeting :$meeting->id  $meeting->description");
                if (in_array($meeting->id, $todoIDList)) {
                    $editedTodos[$meeting->id] = $meeting;
                }
            }

            /* Set Custom*/
            foreach (explode(',', $this->config->meeting->list->customBatchEditFields) as $field) $customFields[$field] = $this->lang->meeting->$field;
            $this->view->customFields = $customFields;
            $this->view->showFields = $this->config->meeting->custom->batchEditFields;

            /* Assign. */
            $title = $this->lang->meeting->common . $this->lang->colon . $this->lang->meeting->batchEdit;
            $this->view->editedTodos = $editedTodos;
            $this->view->title = $title;
            $this->view->users = $this->loadModel('group')->getUserPairs($this->config->meeting->assignToGroupName);

            $this->display();
        }
        else if ($from == 'updateBatchEdit' && !empty($_POST)) {
            $allChanges = $this->meeting->batchUpdate();
            foreach ($allChanges as $todoID => $changes) {
                if (empty($changes)) continue;

                $actionID = $this->loadModel('action')->create('meeting', $todoID, 'edited');
                $this->action->logHistory($actionID, $changes);
            }

            die(js::locate($this->session->meetingList, 'parent'));

            //die(js::locate(inlink('index', ""), 'parent'));
        }
    }

    /**
     * View a todo.
     *
     * @param  int $todoID
     * @param  string $from my|company
     * @access public
     * @return void
     */
    public function view($todoID, $from = 'company')
    {
        $meeting = $this->meeting->getById($todoID, true);
        if (!$meeting) die(js::error($this->lang->notFound) . js::locate('back'));

        /* Save the session. */
        //$this->session->set('taskList', $this->app->getURI(true));
        //$this->session->set('bugList',  $this->app->getURI(true));


        $this->view->title = "{$this->lang->meeting->common} #$meeting->id $meeting->description";
        $this->view->position[] = $this->lang->meeting->view;
        $this->view->todo = $meeting;
        $this->view->users = $this->loadModel('group')->getUserPairs($this->config->meeting->assignToGroupName);
        $this->view->actions = $this->loadModel('action')->getList('meeting', $todoID);

        $this->display();
    }

    /**
     * Delete a todo.
     *
     * @param  int $todoID
     * @param  string $confirm yes|no
     * @access public
     * @return void
     */
    public function delete($todoID, $confirm = 'no')
    {
        if ($confirm == 'no') {
            echo js::confirm($this->lang->meeting->confirmDelete, $this->createLink('meeting', 'delete', "todoID=$todoID&confirm=yes"));
            exit;
        } else {
            $this->dao->delete()->from(TABLE_MEETING)->where('id')->eq($todoID)->exec();
            $this->loadModel('action')->create('meeting', $todoID, 'erased');

            /* if ajax request, send result. */
            if ($this->server->ajax) {
                if (dao::isError()) {
                    $response['result'] = 'fail';
                    $response['message'] = dao::getError();
                } else {
                    $response['result'] = 'success';
                    $response['message'] = '';
                }
                $this->send($response);
            }
            die(js::locate($this->session->meetingList, 'parent'));
        }
    }

    /**
     * Finish a todo.
     *
     * @param  int $todoID
     * @access public
     * @return void
     */
    public function finish($todoID)
    {
        $meeting = $this->meeting->getById($todoID);
        if ($meeting->status != 'done') $this->meeting->finish($todoID);

        if (isonlybody()) die(js::reload('parent.parent'));
        die(js::reload('parent'));
    }

    public function activate($todoID)
    {
        $meeting = $this->meeting->getById($todoID);
        if ($meeting->status == 'done') $this->meeting->activate($todoID);

        if (isonlybody()) die(js::reload('parent.parent'));
        die(js::reload('parent'));
    }

    /**
     * Batch finish meetings.
     *
     * @access public
     * @return void
     */
    public function batchFinish()
    {
        if (!empty($_POST['meetingIDList'])) {
            foreach ($_POST['meetingIDList'] as $todoID) {
                $meeting = $this->meeting->getById($todoID);
                if ($meeting->status != 'done') $this->meeting->finish($todoID);
            }
            die(js::reload('parent'));
        }
    }
}
