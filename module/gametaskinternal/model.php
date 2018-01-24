<?php
/**
 * The model file of gametaskinternal module of ZenTaoPHP.
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

class gametaskinternalModel extends model
{

    public function __construct()
    {
        parent::__construct();

        $this->loadModel('dept');
        $this->loadModel('file');
        $this->loadModel('product');
        $this->loadModel('user');
        $this->loadModel('group');
    }

    public function printCell($col, $task, $users, $depts, $versions, $mode = 'table', $restore = false)
    {
        //echo "printCell=$id<br>";

        $canView  = true;//common::hasPriv('gametaskinternal', 'view');
        $taskLink = helper::createLink('gametaskinternal', 'view', "taskID=$task->id");
        $account  = $this->app->user->account;
        $id       = $col->id;
        if($col->show)
        {
            $class = '';
            if($id == 'status') $class .= ' task-' . $task->status;
            if($id == 'id')     $class .= ' cell-id';
            if($id == 'name')   $class .= ' text-left';
            if($id == 'deadline' and isset($task->delay)) $class .= ' delayed';
            //if($id == 'assignedTo' && $task->assignedTo == $account) $class .= ' red';

            if($id == 'assignedTo' && empty($task->assignedTo)) $class .= ' red';
            if($id == 'count' && ($task->count > 1)) $class .= ' red';
            if($id == 'completed' && ($task->completed)) $class .= ' green';
            if($id == 'closed' && ($task->closed)) $class .= ' green';


            $title = '';
            if($id == 'name')  $title = " title='{$task->name}'";
            if($id == 'story') $title = " title='{$task->storyTitle}'";

            echo "<td class='" . $class . "'" . $title . ">";
            switch($id)
            {
                case 'id':
                    if($mode == 'table') echo "<input type='checkbox' name='taskIDList[{$task->id}]' value='{$task->id}'/> ";
                    echo $canView ? html::a($taskLink, sprintf('%03d', $task->id)) : sprintf('%03d', $task->id);
                    break;
                case 'pri':
                    echo "<span class='pri" .$task->pri . "'>";
                    echo $task->pri == '0' ? '' : $task->pri;
                    echo "</span>";
                    break;
                case 'version':
                    echo zget($versions, $task->version);
                    break;
                case 'dept':
                    echo zget($depts, $task->dept);
                    break;
                case 'assignedTo':
                    echo $task->assignedTo
                        ? $users[$task->assignedTo]
                        : $this->lang->gametaskinternal->assignedToNull;
                    break;
                case 'owner':
                    echo zget($users, $task->owner);
                    break;
                case 'workhour':
                    echo $task->workhour;
                    break;
                case 'title':
                    echo $task->title;
                    break;
                case 'count':
                    echo $task->count;
                    break;
                case 'width':
                    if($task->sizeWidth > 0)
                    {
                        echo $task->sizeWidth;
                    }else {
                        echo "";
                    }
                    break;
                case 'height':
                    if($task->sizeHeight > 0)
                    {
                        echo $task->sizeHeight;
                    }else {
                        echo "";
                    }
                    break;

                case 'desc':
                    echo $task->desc;
                    break;
                case 'srcResPath':
                    echo $task->srcResPath;
                    break;
                case 'gameResPath':
                    echo $task->gameResPath;
                    break;
                case 'completed':
                    echo $task->completed ? $this->lang->gametaskinternal->completed : $this->lang->gametaskinternal->incomplete;
                    break;
                case 'closed':
                    echo $task->closed ? $this->lang->gametaskinternal->closed : $this->lang->gametaskinternal->unclose;
                    break;


                case 'deadline':
                    if(substr($task->deadline, 0, 4) > 0) echo substr($task->deadline, 5, 6);
                    break;
                case 'finishedBy':
                    echo zget($users, $task->finishedBy);
                    break;
                case 'finishedDate':
                    echo substr($task->finishedDate, 5, 11);
                    break;
                case 'canceledBy':
                    echo zget($users, $task->canceledBy);
                    break;
                case 'canceledDate':
                    echo substr($task->canceledDate, 5, 11);
                    break;
                case 'closedBy':
                    echo zget($users, $task->closedBy);
                    break;
                case 'closedDate':
                    echo substr($task->closedDate, 5, 11);
                    break;
                case 'actions':
                    common::printIcon('gametaskinternal', 'finish', "taskID=$task->id", '', 'list', '', 'hiddenwin', 'iframe', true);
                    common::printIcon('gametaskinternal', 'close',  "taskID=$task->id", '', 'list', '', 'hiddenwin', 'iframe', true);
                    common::printIcon('gametaskinternal', 'edit',   "taskID=$task->id", '', 'list');
                    common::printIcon('gametaskinternal', 'delete', "taskid=$task->id", '', 'list');
                    common::printIcon('gametaskinternal', 'restoreTask', "taskid=$task->id", '', 'list', 'play');
                    break;
            }
            echo '</td>';
        }
    }
}
