<?php
/**
 * The html template file of index method of pipeline module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
?>
<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<?php include 'debug.html.php'; ?>

<div id='titlebar'>
    <div class='title' align="center">
        <b>PMS游戏研发管理系统使用指南</b>
    </div>
</div>

<div class='row-table'>
    <div class='col-main'>
        <div class='main'>


            <table class='table'>
                <thead>

                <tr>
                    <th>我是组员🐑</th>
                    <td>
                        <li><b>任务：</b><?php echo html::a(helper::createLink('my', 'task', ''), "我的任务"); ?></li>
                        <li><b>BUG：</b><?php echo html::a(helper::createLink('my', 'bug', ''), "我的BUG"); ?></li>
                        <li>记录工作中美好的回忆：<?php echo html::a(helper::createLink('Blog', 'create',""), "Blog"); ?>📷</li>
                    </td>
                </tr>
                <tr>
                    <th width="180">福利<br>👫👬👭</th>
                    <td>
                        <ul>
                            <li>借书📖：<?php echo html::a(helper::createLink('books', 'index',""), "图书"); ?></li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <th>我是主管、组长🐏</th>
                    <td>
                        <li>管理主任务进度、跨部门协作：<?php echo html::a(helper::createLink('project', 'task', "projectID=$project&type=mydept"), "我的部门任务"); ?></li>
                        <li><b>创建子任务 </b><i class="icon-plus-sign"></i></li>
                        <li><b>指派子任务</b>👉到组员</li>
                        <li>负责管理🗣子任务务进度</li>
                    </td>
                </tr>
                <tr>
                    <th width="180">我是主策🦌</th>
                    <td>
                        <ul>
                            <li>管理游戏：<?php echo html::a(helper::createLink('tree', 'browse',"projectID=$project&story=story"), "模块"); ?></li>
                            <li>管理游戏：<?php echo html::a(helper::createLink('product', 'browse'), "需求"); ?></li>
                            <li>管理：<?php echo html::a(helper::createLink('pipeline', 'index'), "Pipeline"); ?></li>
                            <li><b>指派需求</b>👉到策划组员</li>
                            <li>负责<b>跟踪</b>👣需求进度</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <th>我是策划🐐</th>
                    <td>
                        <li>创建<b>主任务</b>：<?php echo html::a(helper::createLink('project', 'story',"projectID=$project"), "创建主任务"); ?><i class="icon-flag"></i></li>
                        <li>负责<b>跟踪</b>👣需求相关<font color="red"><b>主任务</b></font>进度</li>
                    </td>
                </tr>
                <tr>
                    <th>我是原画🌆🌄</th>
                    <td>
                        <li>完成设计稿后<b>上传PSD</b>：<?php echo html::a(helper::createLink('Artstation', 'create', ''), "创建作品"); ?></li>
                        <li>设计交流：<?php echo html::a(helper::createLink('Artstation', 'index', ''), "Art Station"); ?></li>
                    </td>
                </tr>
                <tr>
                    <th>我是PM\PA<br>🦅🚑🚒🚛🚜</th>
                    <td>
                        <li><b>跟踪</b>🐾整体任务进度，提醒延期任务：<?php echo html::a(helper::createLink('project', 'projectBlueprint', "projectID=$project"), "项目蓝图"); ?></li>
                        <li>管理产品：<?php echo html::a(helper::createLink('tree', 'browse',"projectID=$project&story=story"), "模块"); ?></li>
                        <li>管理产品：<?php echo html::a(helper::createLink('product', 'browse'), "需求"); ?></li>
                        <li>管理：<?php echo html::a(helper::createLink('pipeline', 'index'), "Pipeline"); ?></li>

                        <li>管理PMS：<?php echo html::a(helper::createLink('pipeline', 'groupleaders', ''), "设置组长"); ?></li>
                    </td>
                </tr>
                </thead>
            </table>


        </div>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
