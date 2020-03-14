<?php
/**
 * The model file of dept module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     dept
 * @version     $Id: model.php 4210 2013-01-22 01:06:12Z zhujinyonging@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php

class deptModel extends model
{
    /**
     * Get a department by id.
     *
     * @param  int $deptID
     * @access public
     * @return object
     */
    public function getByID($deptID)
    {
        return $this->dao->findById($deptID)->from(TABLE_DEPT)->fetch();
    }

    /**
     * Build the query.
     *
     * @param  int $rootDeptID
     * @access public
     * @return string
     */
    public function buildMenuQuery($rootDeptID)
    {
        $rootDept = $this->getByID($rootDeptID);
        if (!$rootDept) {
            $rootDept = new stdclass();
            $rootDept->path = '';
        }

        return $this->dao->select('*')->from(TABLE_DEPT)
            ->beginIF($rootDeptID > 0)->where('path')->like($rootDept->path . '%')->fi()
            ->orderBy('grade desc, `order`')
            ->get();
    }

    /**
     * Get option menu of departments.
     *
     * @param  int $rootDeptID
     * @access public
     * @return array
     */
    public function getOptionMenu($rootDeptID = 0)
    {
        $deptMenu = array();
        $stmt = $this->dbh->query($this->buildMenuQuery($rootDeptID));
        $depts = array();
        while ($dept = $stmt->fetch()) $depts[$dept->id] = $dept;

        foreach ($depts as $dept) {
            $parentDepts = explode(',', $dept->path);
            $deptName = '/';
            foreach ($parentDepts as $parentDeptID) {
                if (empty($parentDeptID)) continue;
                $deptName .= $depts[$parentDeptID]->name . '/';
            }
            $deptName = rtrim($deptName, '/');
            $deptName .= "|$dept->id\n";

            if (isset($deptMenu[$dept->id]) and !empty($deptMenu[$dept->id])) {
                if (isset($deptMenu[$dept->parent])) {
                    $deptMenu[$dept->parent] .= $deptName;
                } else {
                    $deptMenu[$dept->parent] = $deptName;;
                }
                $deptMenu[$dept->parent] .= $deptMenu[$dept->id];
            } else {
                if (isset($deptMenu[$dept->parent]) and !empty($deptMenu[$dept->parent])) {
                    $deptMenu[$dept->parent] .= $deptName;
                } else {
                    $deptMenu[$dept->parent] = $deptName;
                }
            }
        }

        $topMenu = @array_pop($deptMenu);
        $topMenu = explode("\n", trim($topMenu));
        $lastMenu[] = '/';
        foreach ($topMenu as $menu) {
            if (!strpos($menu, '|')) continue;
            list($label, $deptID) = explode('|', $menu);
            $lastMenu[$deptID] = $label;
        }
        return $lastMenu;
    }

    /**
     * Get the treemenu of departments.
     *
     * @param  int $rootDeptID
     * @param  string $userFunc
     * @param  int $param
     * @access public
     * @return string
     */
    public function getTreeMenu($rootDeptID = 0, $userFunc, $param = 0)
    {
        $deptMenu = array();
        $stmt = $this->dbh->query($this->buildMenuQuery($rootDeptID));
        while ($dept = $stmt->fetch()) {
            $linkHtml = call_user_func($userFunc, $dept, $param);

            if (isset($deptMenu[$dept->id]) and !empty($deptMenu[$dept->id])) {
                if (!isset($deptMenu[$dept->parent])) $deptMenu[$dept->parent] = '';
                $deptMenu[$dept->parent] .= "<li>$linkHtml";
                $deptMenu[$dept->parent] .= "<ul>" . $deptMenu[$dept->id] . "</ul>\n";
            } else {
                if (isset($deptMenu[$dept->parent]) and !empty($deptMenu[$dept->parent])) {
                    $deptMenu[$dept->parent] .= "<li>$linkHtml\n";
                } else {
                    $deptMenu[$dept->parent] = "<li>$linkHtml\n";
                }
            }
            $deptMenu[$dept->parent] .= "</li>\n";
        }

        $lastMenu = "<ul class='tree tree-lines'>" . @array_pop($deptMenu) . "</ul>\n";
        return $lastMenu;
    }

    /**
     * Update dept.
     *
     * @param  int $deptID
     * @access public
     * @return void
     */
    public function update($deptID)
    {
        $dept = fixer::input('post')->get();
        $self = $this->getById($deptID);
        $parent = $this->getById($this->post->parent);
        $childs = $this->getAllChildId($deptID);
        $dept->grade = $parent ? $parent->grade + 1 : 1;
        $dept->path = $parent ? $parent->path . $deptID . ',' : ',' . $deptID . ',';
        $this->dao->update(TABLE_DEPT)->data($dept)->autoCheck()->check('name', 'notempty')->where('id')->eq($deptID)->exec();
        $this->dao->update(TABLE_DEPT)->set('grade = grade + 1')->where('id')->in($childs)->andWhere('id')->ne($deptID)->exec();
        $this->dao->update(TABLE_DEPT)->set('manager')->eq($this->post->manager)->where('id')->in($childs)->andWhere('manager')->eq('')->exec();
        $this->dao->update(TABLE_DEPT)->set('manager')->eq($this->post->manager)->where('id')->in($childs)->andWhere('manager')->eq($self->manager)->exec();
        $this->fixDeptPath();
    }

    /**
     * Create the manage link.
     *
     * @param  int $dept
     * @access public
     * @return string
     */
    public function createManageLink($dept)
    {
        $linkHtml = $dept->name;
        if (common::hasPriv('dept', 'edit')) $linkHtml .= ' ' . html::a(helper::createLink('dept', 'edit', "deptid={$dept->id}"), $this->lang->edit, '', 'data-toggle="modal" data-type="ajax"');
        if (common::hasPriv('dept', 'browse')) $linkHtml .= ' ' . html::a(helper::createLink('dept', 'browse', "deptid={$dept->id}"), $this->lang->dept->manageChild);
        if (common::hasPriv('dept', 'delete')) $linkHtml .= ' ' . html::a(helper::createLink('dept', 'delete', "deptid={$dept->id}"), $this->lang->delete, 'hiddenwin');
        if (common::hasPriv('dept', 'updateOrder')) $linkHtml .= ' ' . html::input("orders[$dept->id]", $dept->order, 'style="width:30px;text-align:center"');
        return $linkHtml;
    }

    /**
     * Create the member link.
     *
     * @param  int $dept
     * @access public
     * @return string
     */
    public function createMemberLink($dept)
    {
        $linkHtml = html::a(helper::createLink('company', 'browse', "dept={$dept->id}"), $dept->name, '_self', "id='dept{$dept->id}'");
        return $linkHtml;
    }

    /**
     * Create the group manage members link.
     *
     * @param  int $dept
     * @param  int $groupID
     * @access public
     * @return string
     */
    public function createGroupManageMemberLink($dept, $groupID)
    {
        return html::a(helper::createLink('group', 'managemember', "groupID=$groupID&deptID={$dept->id}"), $dept->name, '_self', "id='dept{$dept->id}'");
    }

    /**
     * Get sons of a department.
     *
     * @param  int $deptID
     * @access public
     * @return array
     */
    public function getSons($deptID)
    {
        return $this->dao->select('*')->from(TABLE_DEPT)->where('parent')->eq($deptID)->orderBy('`order`')->fetchAll();
    }

    /**
     * Get all childs.
     *
     * @param  int $deptID
     * @access public
     * @return array
     */
    public function getAllChildId($deptID)
    {
        if ($deptID == 0) return array();
        $dept = $this->getById($deptID);
        $childs = $this->dao->select('id')->from(TABLE_DEPT)->where('path')->like($dept->path . '%')->fetchPairs();
        return array_keys($childs);
    }

    /**
     * Get parents.
     *
     * @param  int $deptID
     * @access public
     * @return array
     */
    public function getParents($deptID)
    {
        if ($deptID == 0) return array();
        $path = $this->dao->select('path')->from(TABLE_DEPT)->where('id')->eq($deptID)->fetch('path');
        $path = substr($path, 1, -1);
        if (empty($path)) return array();
        return $this->dao->select('*')->from(TABLE_DEPT)->where('id')->in($path)->orderBy('grade')->fetchAll();
    }

    /**
     * Update order.
     *
     * @param  int $orders
     * @access public
     * @return void
     */
    public function updateOrder($orders)
    {
        foreach ($orders as $deptID => $order) $this->dao->update(TABLE_DEPT)->set('`order`')->eq($order)->where('id')->eq($deptID)->exec();
    }

    /**
     * Manage childs.
     *
     * @param  int $parentDeptID
     * @param  string $childs
     * @access public
     * @return void
     */
    public function manageChild($parentDeptID, $childs)
    {
        $parentDept = $this->getByID($parentDeptID);
        if ($parentDept) {
            $grade = $parentDept->grade + 1;
            $parentPath = $parentDept->path;
        } else {
            $grade = 1;
            $parentPath = ',';
        }

        $i = 1;
        foreach ($childs as $deptID => $deptName) {
            if (empty($deptName)) continue;
            if (is_numeric($deptID)) {
                $dept->name = strip_tags($deptName);
                $dept->parent = $parentDeptID;
                $dept->grade = $grade;
                $dept->order = $this->post->maxOrder + $i * 10;
                $this->dao->insert(TABLE_DEPT)->data($dept)->exec();
                $deptID = $this->dao->lastInsertID();
                $childPath = $parentPath . "$deptID,";
                $this->dao->update(TABLE_DEPT)->set('path')->eq($childPath)->where('id')->eq($deptID)->exec();
                $i++;
            } else {
                $deptID = str_replace('id', '', $deptID);
                $this->dao->update(TABLE_DEPT)->set('name')->eq(strip_tags($deptName))->where('id')->eq($deptID)->exec();
            }
        }
    }

    /**
     * Get users of a deparment.
     *
     * @param  int $deptID
     * @access public
     * @return array
     */
    public function getUsers($deptID, $pager = null, $orderBy = 'id')
    {
        return $this->dao->select('*')->from(TABLE_USER)
            ->where('deleted')->eq(0)
            ->beginIF($deptID)->andWhere('dept')->in($deptID)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get user pairs of a department.
     *
     * @param  int $deptID
     * @access public
     * @return array
     */
    public function getDeptUserPairs($deptID = 0)
    {
        $childDepts = $this->getAllChildID($deptID);
        return $this->dao->select('account, realname')->from(TABLE_USER)
            ->where('deleted')->eq(0)
            ->beginIF($deptID)->andWhere('dept')->in($childDepts)->fi()
            ->orderBy('account')
            ->fetchPairs();
    }

    public function getDeptUserPairsWithoutChilds($deptID = 0)
    {
        return $this->dao->select('account, realname')->from(TABLE_USER)
            ->where('deleted')->eq(0)
            ->beginIF($deptID)->andWhere('dept')->eq($deptID)->fi()
            ->orderBy('account')
            ->fetchPairs();
    }

    /**
     * Delete a department.
     *
     * @param  int $deptID
     * @param  null $null compatible with that of model::delete()
     * @access public
     * @return void
     */
    public function delete($deptID, $null = null)
    {
        $this->dao->delete()->from(TABLE_DEPT)->where('id')->eq($deptID)->exec();
    }

    /**
     * Fix dept path.
     *
     * @access public
     * @return void
     */
    public function fixDeptPath()
    {
        /* Get all depts grouped by parent. */
        $groupDepts = $this->dao->select('id, parent')->from(TABLE_DEPT)->fetchGroup('parent', 'id');
        $depts = array();

        /* Cycle the groupDepts until it has no item any more. */
        while (count($groupDepts) > 0) {
            $oldCounts = count($groupDepts);    // Record the counts before processing.
            foreach ($groupDepts as $parentDeptID => $childDepts) {
                /* If the parentDept doesn't exsit in the depts, skip it. If exists, compute it's child depts. */
                if (!isset($depts[$parentDeptID]) and $parentDeptID != 0) continue;
                if ($parentDeptID == 0) {
                    $parentDept = new stdclass();
                    $parentDept->grade = 0;
                    $parentDept->path = ',';
                } else {
                    $parentDept = $depts[$parentDeptID];
                }

                /* Compute it's child depts. */
                foreach ($childDepts as $childDeptID => $childDept) {
                    $childDept->grade = $parentDept->grade + 1;
                    $childDept->path = $parentDept->path . $childDept->id . ',';
                    $depts[$childDeptID] = $childDept;    // Save child dept to depts, thus the child of child can compute it's grade and path.
                }
                unset($groupDepts[$parentDeptID]);    // Remove it from the groupDepts.
            }
            if (count($groupDepts) == $oldCounts) break;   // If after processing, no dept processed, break the cycle.
        }

        /* Save depts to database. */
        foreach ($depts as $dept) {
            $this->dao->update(TABLE_DEPT)->data($dept)->where('id')->eq($dept->id)->exec();
        }
    }

    /**
     * Get data structure
     * @param  integer $rootDeptID
     * @access public
     * @return object
     */
    public function getDataStructure($rootDeptID = 0)
    {
        $tree = array_values($this->getSons($rootDeptID));
        $users = $this->loadModel('user')->getPairs('noletter|noclosed|nodeleted');
        if (count($tree)) {
            foreach ($tree as $node) {
                $node->managerName = $users[$node->manager];
                $children = $this->getDataStructure($node->id);
                if (count($children)) {
                    $node->children = $children;
                    $node->actions = array('delete' => false);
                }
            }
        }
        return $tree;
    }

    //oscar:
    public function setupDeptWithUsers($view)
    {
        $depts = $this->dao->select('id,name')->from(TABLE_DEPT)->fetchPairs();

        $deptUsers = array();

        $this->loadModel('user');
        $allUsers = $this->user->getPairs('nodeleted|noclosed');

        foreach($depts as $k => $v)
        {
            $deptUserList = $this->dao->select('account, realname')->from(TABLE_USER)
                ->where('deleted')->eq(0)
                ->andWhere('dept')->eq($k)
                ->orderBy('account')
                ->fetchPairs('account');

            foreach($deptUserList as $account => $user)
            {
                $firstLetter = ucfirst(substr($account, 0, 1)) . ':';
                //if(strpos($params, 'noletter') !== false) $firstLetter =  '';
                $deptUserList[$account] =  $firstLetter . $user;
            }

            $deptUsers[$k] = $deptUserList;
        }

        $view->deptWithUsers = $deptUsers;

        /*
        foreach ($deptUsers as $k => $values) {
            error_log("=== dump dept users:$k values:$values");
            foreach($values as $ku => $u)
            {
                error_log(" k:$ku u:$u");
            }
        }
        //*/
    }

    public function setupDeptUsers($view, $account, $dept)
    {
        $myDepts = array($dept);

        $deptUsers = $this->getDeptUserPairs($dept);

        $leaders = $this->dao->select('dept,username')->from(TABLE_GAMEGROUPLEADERS)
            ->orderBy('dept asc')
            ->fetchPairs();

        $leadersPair = array();

        $this->loadModel('user');
        $allUsers = $this->user->getPairs('nodeleted|noclosed');

        foreach ($leaders as $key => $leader) {

            if($leader != "admin")
            {
                $leadersPair[$leader] = $allUsers[$leader];
            }

            if ($leader == $account) {
                array_push($myDepts, $key);
                $deptUsers += $this->getDeptUserPairs($key);

                /*
                error_log("oscar: $key : $leader deptUsers:" . count($deptUsers));
                foreach ($deptUsers as $deptUser) {
                    error_log("     oscar:$deptUser");
                }
                //*/
            }
        }

        $deptUsers = array_unique($deptUsers);
        $myDepts = array_unique($myDepts);

        foreach ($deptUsers as $act => $user) {
            $firstLetter = ucfirst(substr($act, 0, 1)) . ':';
            $deptUsers[$act] = $firstLetter . $user;
        }


        ksort($leadersPair);
        $view->leaders = $leadersPair;
        $view->deptLeaders = $leaders;
        $view->dept = $dept;
        $view->user = $account;
        if($this->app->user->account == 'dengdapeng'
        //|| $this->app->user->account == 'chenwang'
        )
        {
            $artDepts = $this->getAllChildId($this->app->user->dept);
            $artLeaders = array();
            foreach ($artDepts as $k) {
                if(array_key_exists($k, $leaders))
                {
                    $artLeaders[$leaders[$k]] = $allUsers[$leaders[$k]];
                }
            }
            ksort($artLeaders);

            $deptUsers = $this->getDeptUserPairsWithoutChilds($this->app->user->dept);
            foreach ($deptUsers as $act => $user) {
                $firstLetter = ucfirst(substr($act, 0, 1)) . ':';
                $deptUsers[$act] = $firstLetter . $user;
            }

            //$deptUsers = array_unique($deptUsers);
            sort($deptUsers);
            $deptUsers = array_merge($artLeaders, $deptUsers);
            $view->deptUsers = $deptUsers;
        }
        else
        {
            $view->deptUsers = $deptUsers;
        }
        $view->depts = $this->getOptionMenu();

        /*
        foreach ($leadersPair as $l) {
            error_log("     leaders:$l");
        }
        //*/

        return $myDepts;
    }
    //oscar:
}
