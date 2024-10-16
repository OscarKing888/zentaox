<?php
/**
 * The browse view file of bug module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     bug
 * @version     $Id: browse.html.php 5102 2013-07-12 00:59:54Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php
include '../../common/view/header.html.php';
include '../../common/view/datatable.fix.html.php';
js::set('browseType',    $browseType);
js::set('moduleID',      $moduleID);
js::set('bugBrowseType', ($browseType == 'bymodule' and $this->session->bugBrowseType == 'bysearch') ? 'all' : $this->session->bugBrowseType);
js::set('flow', $this->config->global->flow);
js::set('productID', $productID);
js::set('branch', $branch);
$currentBrowseType = isset($lang->bug->mySelects[$browseType]) && in_array($browseType, array_keys($lang->bug->mySelects)) ? $browseType : '';
?>
<?php if($this->config->global->flow == 'onlyTest'):?>
<div id='featurebar'>
  <ul class='submenu hidden'>
    <li id='moreMenus' class='hidden'>
      <a href='###' class='dropdown-toggle' data-toggle='dropdown'>
        <?php echo $lang->more;?> <span class='caret'></span>
      </a>
      <ul class='dropdown-menu right'>
      </ul>
    </li>
    <li id='bysearchTab'><a href='#'><i class='icon-search icon'></i>&nbsp;<?php echo $lang->bug->byQuery;?></a></li>
    <li class='right'>
      <div class='btn-group' id='createActionMenu'>
        <?php 
        $misc = common::hasPriv('bug', 'create') ? "class='btn btn-primary'" : "class='btn btn-primary disabled'";
        $link = common::hasPriv('bug', 'create') ?  $this->createLink('bug', 'create', "productID=$productID&branch=$branch&extra=moduleID=$moduleID") : '#';
        echo html::a($link, "<i class='icon icon-plus'></i>" . $lang->bug->create, '', $misc);

        $misc = common::hasPriv('bug', 'batchCreate') ? '' : "disabled";
        $link = common::hasPriv('bug', 'batchCreate') ?  $this->createLink('bug', 'batchCreate', "productID=$productID&branch=$branch&projectID=0&moduleID=$moduleID") : '#';
        ?>
        <button type='button' class='btn btn-primary dropdown-toggle <?php echo $misc?>' data-toggle='dropdown'>
          <span class='caret'></span>
        </button>
        <ul class='dropdown-menu right'>
        <?php echo "<li>" . html::a($link, $lang->bug->batchCreate, '', "class='$misc'") . "</li>";?>
        </ul>
      </div>
    </li>
    <li class='right'>
      <?php common::printLink('bug', 'report', "productID=$productID&browseType=$browseType&branchID=$branch&moduleID=$moduleID", "<i class='icon-common-report icon-bar-chart'></i> " . $lang->bug->report->common); ?>
    </li>
    <li class='right'>
      <a href='###' class='dropdown-toggle' data-toggle='dropdown'>
        <i class='icon-download-alt'></i> <?php echo $lang->export ?>
        <span class='caret'></span>
      </a>
      <ul class='dropdown-menu' id='exportActionMenu'>
        <?php 
        $misc = common::hasPriv('bug', 'export') ? "class='export'" : "class=disabled";
        $link = common::hasPriv('bug', 'export') ?  $this->createLink('bug', 'export', "productID=$productID&orderBy=$orderBy") : '#';
        echo "<li>" . html::a($link, $lang->bug->export, '', $misc) . "</li>";
        ?>
      </ul>
    </li>
  </ul>
  <div id='querybox' class='<?php if($browseType =='bysearch') echo 'show';?>'></div>
</div>
<?php else:?>
<div id='featurebar'>
  <ul class='nav'>
    <li>
      <div class='label-angle<?php if($moduleID) echo ' with-close';?>'>
        <?php
        echo $moduleName;
        if($moduleID)
        {
            $removeLink = $browseType == 'bymodule' ? inlink('browse', "productID=$productID&branch=$branch&browseType=$browseType&param=0&orderBy=$orderBy&recTotal=0&recPerPage={$pager->recPerPage}") : 'javascript:removeCookieByKey("bugModule")';
            echo html::a($removeLink, "<i class='icon icon-remove'></i>", '', "class='text-muted'");
        }
        ?>
      </div>
    </li>
    <?php foreach(customModel::getFeatureMenu($this->moduleName, $this->methodName) as $menuItem):?>
    <?php if(isset($menuItem->hidden)) continue;?>
    <?php if($this->config->global->flow == 'onlyTest' and $menuItem->name == 'needconfirm') continue;?>
    <?php $menuBrowseType = strpos($menuItem->name, 'QUERY') === 0 ? 'bySearch' : $menuItem->name;?>
    <?php $param = strpos($menuItem->name, 'QUERY') === 0 ? (int)substr($menuItem->name, 5) : 0; ?>
    <?php if($menuItem->name == 'my'):?>
    <?php
    echo "<li id='statusTab' class='dropdown " . (!empty($currentBrowseType) ? 'active' : '') . "'>";
    echo html::a('javascript:;', $menuItem->text . " <span class='caret'></span>", '', "data-toggle='dropdown'");
    echo "<ul class='dropdown-menu'>";
    foreach ($lang->bug->mySelects as $key => $value)
    {
        echo '<li' . ($key == $currentBrowseType ? " class='active'" : '') . '>';
        echo html::a($this->createLink('bug', 'browse', "productid=$productID&branch=$branch&browseType=$key&param=$param"), $value);
    }
    echo '</ul></li>';
    ?>
    <?php else:?>
    <li id='<?php echo $menuItem->name?>Tab'><?php echo html::a($this->createLink('bug', 'browse', "productid=$productID&branch=$branch&browseType=$menuBrowseType&param=$param"), $menuItem->text)?></li>
    <?php endif;?>
    <?php endforeach;?>
    <li id='bysearchTab'><a href='#'><i class='icon-search icon'></i>&nbsp;<?php echo $lang->bug->byQuery;?></a></li>
  </ul>
  <div class='actions'>
    <div class='btn-group'>
      <div class='btn-group'>
        <button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown'>
          <i class='icon-download-alt'></i> <?php echo $lang->export ?>
          <span class='caret'></span>
        </button>
        <ul class='dropdown-menu' id='exportActionMenu'>
          <?php 
          $misc = common::hasPriv('bug', 'export') ? "class='export'" : "class=disabled";
          $link = common::hasPriv('bug', 'export') ?  $this->createLink('bug', 'export', "productID=$productID&orderBy=$orderBy") : '#';
          echo "<li>" . html::a($link, $lang->bug->export, '', $misc) . "</li>";
          ?>
        </ul>
      </div>
      <div class='btn-group'>
        <?php common::printIcon('bug', 'report', "productID=$productID&browseType=$browseType&branchID=$branch&moduleID=$moduleID"); ?>
      </div>
    </div>
    <div class='btn-group'>
      <div class='btn-group' id='createActionMenu'>
        <?php 
        if(commonModel::isTutorialMode())
        {
            $wizardParams = helper::safe64Encode("productID=$productID&branch=$branch&extra=moduleID=$moduleID");
            echo html::a($this->createLink('tutorial', 'wizard', "module=bug&method=create&params=$wizardParams"), "<i class='icon-plus'></i>" . $lang->bug->create, '', "class='btn btn-primary btn-bug-create'");
        }
        else
        {
            $misc = common::hasPriv('bug', 'create') ? "class='btn btn-primary'" : "class='btn btn-primary disabled'";
            $link = common::hasPriv('bug', 'create') ? $this->createLink('bug', 'create', "productID=$productID&branch=$branch&extra=moduleID=$moduleID") : '#';
            echo html::a($link, "<i class='icon icon-plus'></i>" . $lang->bug->create, '', $misc);
        }

        $misc = common::hasPriv('bug', 'batchCreate') ? '' : "disabled";
        $link = common::hasPriv('bug', 'batchCreate') ?  $this->createLink('bug', 'batchCreate', "productID=$productID&branch=$branch&projectID=0&moduleID=$moduleID") : '#';
        ?>
        <button type='button' class='btn btn-primary dropdown-toggle <?php echo $misc?>' data-toggle='dropdown'>
          <span class='caret'></span>
        </button>
        <ul class='dropdown-menu pull-right'>
        <?php
        echo "<li>" . html::a($link, $lang->bug->batchCreate, '', "class='$misc'") . "</li>";
        ?>
        </ul>
      </div>
    </div>
  </div>
  <div id='querybox' class='<?php if($browseType =='bysearch') echo 'show';?>'></div>
</div>
<?php endif;?>
<div class='side' id='treebox'>
  <a class='side-handle' data-id='bugTree'><i class='icon-caret-left'></i></a>
  <div class='side-body'>
    <div class='panel panel-sm'>
      <div class='panel-heading nobr'>
        <?php echo html::icon($lang->icons['product']);?> <strong><?php echo $branch ? $branches[$branch] : $productName;?></strong>
      </div>
      <div class='panel-body'>
        <?php echo $moduleTree;?>
        <div class='text-right'>
          <?php common::printLink('tree', 'browse', "productID=$productID&view=bug", $lang->tree->manage);?>
        </div>
      </div>
    </div>
  </div>
</div>
<div class='main'>
  <script>setTreeBox();</script>
  <form method='post' id='bugForm'>
    <?php
    $datatableId  = $this->moduleName . ucfirst($this->methodName);
    $useDatatable = (isset($this->config->datatable->$datatableId->mode) and $this->config->datatable->$datatableId->mode == 'datatable');
    $vars         = "productID=$productID&branch=$branch&browseType=$browseType&param=$param&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}";
    if($useDatatable) include '../../common/view/datatable.html.php';

    $setting = $this->datatable->getSetting('bug');
    $widths  = $this->datatable->setFixedFieldWidth($setting);
    $columns = 0;
    ?>
    <table class='table table-condensed table-hover table-striped tablesorter table-fixed <?php echo ($useDatatable ? 'datatable' : 'table-selectable');?>' id='bugList' data-checkable='true' data-fixed-left-width='<?php echo $widths['leftWidth']?>' data-fixed-right-width='<?php echo $widths['rightWidth']?>' data-custom-menu='true' data-checkbox-name='bugIDList[]'>
      <thead>
        <tr>
        <?php
        foreach($setting as $key => $value)
        {
            if($value->show)
            {
                $this->datatable->printHead($value, $orderBy, $vars);
                $columns ++;
            }
        }
        ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach($bugs as $bug):?>
        <tr class='text-center' data-id='<?php echo $bug->id?>'>
          <?php foreach($setting as $key => $value) $this->bug->printCell($value, $bug, $users, $builds, $branches, $modulePairs, $projects, $plans, $stories, $tasks, $useDatatable ? 'datatable' : 'table');?>
        </tr>
        <?php endforeach;?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan='<?php echo $columns;?>'>
            <?php if(!empty($bugs)):?>
            <div class='table-actions clearfix'>
              <?php echo html::selectButton();?>
              <div class='btn-group dropup'>
                <?php
                $actionLink = $this->createLink('bug', 'batchEdit', "productID=$productID&branch=$branch");
                $misc       = common::hasPriv('bug', 'batchEdit') ? "onclick=\"setFormAction('$actionLink')\"" : "disabled='disabled'";
                echo html::commonButton($lang->edit, $misc);
                ?>
                <button type='button' class='btn dropdown-toggle' data-toggle='dropdown'><span class='caret'></span></button>
                <ul class='dropdown-menu'>
                  <?php
                  $class = "class='disabled'";
                  $actionLink = $this->createLink('bug', 'batchConfirm');
                  $misc = common::hasPriv('bug', 'batchConfirm') ? "onclick=\"setFormAction('$actionLink', 'hiddenwin')\"" : $class;
                  if($misc) echo "<li>" . html::a('javascript:;', $lang->bug->confirmBug, '', $misc) . "</li>";

                  $actionLink = $this->createLink('bug', 'batchClose');
                  $misc = common::hasPriv('bug', 'batchClose') ? "onclick=\"setFormAction('$actionLink', 'hiddenwin')\"" : $class;
                  if($misc) echo "<li>" . html::a('javascript:;', $lang->bug->close, '', $misc) . "</li>";

                  $actionLink = $this->createLink('bug', 'batchActivate', "productID=$productID&branch=$branch");
                  $misc = common::hasPriv('bug', 'batchActivate') ? "onclick=\"setFormAction('$actionLink')\"" : $class;
                  if($misc) echo "<li>" . html::a('javascript:;', $lang->bug->activate, '', $misc) . "</li>";

                  if(common::hasPriv('bug', 'batchChangeBranch') and $this->session->currentProductType != 'normal')
                  {
                      $withSearch = count($branches) > 8;
                      echo "<li class='dropdown-submenu'>";
                      echo html::a('javascript:;', $lang->product->branchName[$this->session->currentProductType], '', "id='branchItem'");
                      echo "<div class='dropdown-menu" . ($withSearch ? ' with-search':'') . "'>";
                      echo "<ul class='dropdown-list'>";
                      foreach($branches as $branchID => $branchName)
                      {
                          $actionLink = $this->createLink('bug', 'batchChangeBranch', "branchID=$branchID");
                          echo "<li class='option' data-key='$branchID'>" . html::a('#', $branchName, '', "onclick=\"setFormAction('$actionLink', 'hiddenwin')\"") . "</li>";
                      }
                      echo '</ul>';
                      if($withSearch) echo "<div class='menu-search'><div class='input-group input-group-sm'><input type='text' class='form-control' placeholder=''><span class='input-group-addon'><i class='icon-search'></i></span></div></div>";
                      echo '</div></li>';
                  }

                  if(common::hasPriv('bug', 'batchChangeModule'))
                  {
                      $withSearch = count($modules) > 8;
                      echo "<li class='dropdown-submenu'>";
                      echo html::a('javascript:;', $lang->bug->moduleAB, '', "id='moduleItem'");
                      echo "<div class='dropdown-menu" . ($withSearch ? ' with-search':'') . "'>";
                      echo '<ul class="dropdown-list">';
                      foreach($modules as $moduleId => $module)
                      {
                          $actionLink = $this->createLink('bug', 'batchChangeModule', "moduleID=$moduleId");
                          echo "<li class='option' data-key='$moduleID'>" . html::a('#', $module, '', "onclick=\"setFormAction('$actionLink','hiddenwin')\"") . "</li>";
                      }
                      echo "</ul>";
                      if($withSearch) echo "<div class='menu-search'><div class='input-group input-group-sm'><input type='text' class='form-control' placeholder=''><span class='input-group-addon'><i class='icon-search'></i></span></div></div>";
                      echo '</div></li>';
                  }
                  else
                  {
                      echo '<li>' . html::a('javascript:;', $lang->bug->moduleAB, '', $class) . '</li>';
                  }

                  $misc = common::hasPriv('bug', 'batchResolve') ? "id='resolveItem'" : '';
                  if($misc)
                  {
                      echo "<li class='dropdown-submenu'>" . html::a('javascript:;', $lang->bug->resolve,  '', $misc);
                      echo "<ul class='dropdown-menu'>";
                      unset($lang->bug->resolutionList['']);
                      unset($lang->bug->resolutionList['duplicate']);
                      unset($lang->bug->resolutionList['tostory']);
                      foreach($lang->bug->resolutionList as $key => $resolution)
                      {
                          $actionLink = $this->createLink('bug', 'batchResolve', "resolution=$key");
                          if($key == 'fixed')
                          {
                              $withSearch = count($builds) > 4;
                              echo "<li class='dropdown-submenu'>";
                              echo html::a('javascript:;', $resolution, '', "id='fixedItem'");
                              echo "<div class='dropdown-menu" . ($withSearch ? ' with-search':'') . "'>";
                              echo '<ul class="dropdown-list">';
                              unset($builds['']);
                              foreach($builds as $key => $build)
                              {
                                  $actionLink = $this->createLink('bug', 'batchResolve', "resolution=fixed&resolvedBuild=$key");
                                  echo "<li class='option' data-key='$key'>";
                                  echo html::a('javascript:;', $build, '', "onclick=\"setFormAction('$actionLink','hiddenwin')\"");
                                  echo "</li>";
                              }
                              echo "</ul>";
                              if($withSearch) echo "<div class='menu-search'><div class='input-group input-group-sm'><input type='text' class='form-control' placeholder=''><span class='input-group-addon'><i class='icon-search'></i></span></div></div>";
                              echo '</div></li>';
                          }
                          else
                          {
                              echo '<li>' . html::a('javascript:;', $resolution, '', "onclick=\"setFormAction('$actionLink','hiddenwin')\"") . '</li>';
                          }
                      }
                      echo '</ul></li>';
                  }
                  else
                  {
                      echo "<li>" . html::a('javascript:;', $lang->bug->resolve,  '', $class);
                  }

                  $canBatchAssignTo = common::hasPriv('bug', 'batchAssignTo');
                  if($canBatchAssignTo && count($bugs))
                  {
                      $withSearch = count($memberPairs) > 10;
                      $actionLink = $this->createLink('bug', 'batchAssignTo', "productID={$productID}&type=product");
                      echo html::select('assignedTo', $memberPairs, '', 'class="hidden"');
                      echo "<li class='dropdown-submenu'>";
                      echo html::a('javascript::', $lang->bug->assignedTo, 'id="assignItem"');
                      echo "<div class='dropdown-menu" . ($withSearch ? ' with-search':'') . "'>";
                      echo '<ul class="dropdown-list">';
                      foreach ($memberPairs as $key => $value)
                      {
                          if(empty($key)) continue;
                          echo "<li class='option' data-key='$key'>" . html::a("javascript:$(\"#assignedTo\").val(\"$key\");setFormAction(\"$actionLink\",\"hiddenwin\")", $value, '', '') . '</li>';
                      }
                      echo "</ul>";
                      if($withSearch) echo "<div class='menu-search'><div class='input-group input-group-sm'><input type='text' class='form-control' placeholder=''><span class='input-group-addon'><i class='icon-search'></i></span></div></div>";
                      echo "</div></li>";
                  }
                  else
                  {
                      echo "<li>" . html::a('javascript:;', $lang->bug->assignedTo,  '', $class);
                  }
                  ?>
                </ul>
              </div>
            </div>
            <?php
                if(common::hasPriv('bug', 'batchChangePriority'))
                {
                    $actionLink = $this->createLink('bug', 'batchChangePriority');
                    $priList = (array)$this->lang->bug->priList;
                    echo "<div class='btn-group dropup'>";
                    echo "<button id='bugBatchChangePriority' type='button' class='btn dropdown-toggle' data-toggle='dropdown'>" . $lang->bug->batchChangePriority . "<span class='caret'></span></button>";
                    echo "<ul class='dropdown-menu' id='bugBatchChangePriorityMenu'>";
                        echo html::select('pri', $priList, '', 'class="hidden"');

                        echo '<ul class="dropdown-list">';
                            foreach ($priList as $key => $value) {
                            if (empty($key)) continue;
                            echo "<li class='option' data-key='$key'>" . html::a("javascript:$(\"#pri\").val(\"$key\");setFormAction(\"$actionLink\", \"hiddenwin\" )", $value, '', '') . '</li>';
                            }
                            echo "</ul>";
                        //if ($withSearch) echo "<div class='menu-search'><div class='input-group input-group-sm'><input type='text' class='form-control' placeholder=''><span class='input-group-addon'><i class='icon-search'></i></span></div></div>";
                        echo "</div></li>";
                        echo "</ul>";
                }

                if(common::hasPriv('bug', 'batchChangeSeverity'))
                {
                    $actionLink = $this->createLink('bug', 'batchChangeSeverity');
                    $valueList = (array)$this->lang->bug->severityList;
                    echo "<div class='btn-group dropup'>";
                    echo "<button id='bugbatchChangeSeverity' type='button' class='btn dropdown-toggle' data-toggle='dropdown'>" . $lang->bug->batchChangeSeverity . "<span class='caret'></span></button>";
                    echo "<ul class='dropdown-menu' id='bugbatchChangeSeverity'>";
                    echo html::select('severity', $valueList, '', 'class="hidden"');

                    echo '<ul class="dropdown-list">';
                    foreach ($valueList as $key => $value) {
                        if (empty($key)) continue;
                        echo "<li class='option' data-key='$key'>" . html::a("javascript:$(\"#severity\").val(\"$key\");setFormAction(\"$actionLink\", \"hiddenwin\" )", $value, '', '') . '</li>';
                    }
                    echo "</ul>";
                    //if ($withSearch) echo "<div class='menu-search'><div class='input-group input-group-sm'><input type='text' class='form-control' placeholder=''><span class='input-group-addon'><i class='icon-search'></i></span></div></div>";
                    echo "</div></li>";
                    echo "</ul>";
                }



                if(common::hasPriv('bug', 'batchAssignToEx'))
                {
                    echo "<div class='btn-group dropup'>";
                    echo "<button id='bugBatchAssignTo' type='button' class='btn dropdown-toggle' data-toggle='dropdown'>" . $lang->bug->assignedTo . "<span class='caret'></span></button>";
                    echo "<ul class='dropdown-menu' id='bugBatchAssignToMenu'>";
                    echo html::select('assignedToEx', $memberPairs, '', 'class="hidden"');

                    $deptPairs = array();
                    foreach ($depts as $key => $val) {
                        $deptPairs[$key] = $val;
                    }

                    foreach ($deptWithUsers as $key => $value) {
                        if (empty($key)) continue;
                        echo '<li class="dropdown-submenu">';
                        echo html::a('javascript:;', $deptPairs[$key], '', "id='dept-id-$key'");
                        echo '<ul class="dropdown-menu">';
                        foreach($value as $account => $realName)
                        {
                            $actionLink = $this->createLink('bug', 'batchAssignToEx', "projectID=$projectID&type=product");
                            echo "<li class='option' data-key='$account'>" . html::a("javascript:$(\"#assignedToEx\").val(\"$account\");setFormAction(\"$actionLink\", \"hiddenwin\");", $realName, '', '') . '</li>';
                        }
                        echo '</li>';
                        echo '</ul>';
                    }

                    echo "</ul>";
                    echo "</div></li>";
                    echo "</ul>";
                }


                ?>
            <?php endif;?>
            <div class='text-right'><?php $pager->show();?></div>
          </td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script>
$('#' + bugBrowseType + 'Tab').addClass('active');
$('#module' + moduleID).addClass('active'); 
<?php if($browseType == 'bysearch'):?>
$shortcut = $('#QUERY<?php echo (int)$param;?>Tab');
if($shortcut.size() > 0)
{
    $shortcut.addClass('active');
    $('#bysearchTab').removeClass('active');
    $('#querybox').removeClass('show');
}
<?php endif;?>
<?php $this->app->loadConfig('qa', '', false);?>
<?php if(isset($config->qa->homepage) and $config->qa->homepage != 'browse' and $config->global->flow == 'full'):?>
$(function(){$('#modulemenu .nav li:last').after("<li class='right'><a style='font-size:12px' href='javascript:setHomepage(\"qa\", \"browse\")'><i class='icon icon-cog'></i> <?php echo $lang->homepage?></a></li>")});
<?php endif;?>
</script>
<?php if($config->global->flow == 'onlyTest'):?>
<style>
.nav > li > .btn-group > a, .nav > li > .btn-group > a:hover, .nav > li > .btn-group > a:focus{background: #1a4f85; border-color: #164270;}
.outer.with-side #featurebar {background: none; border: none; line-height: 0; margin: 0; min-height: 0; padding: 0; }
#querybox #searchform{border-bottom: 1px solid #ddd; margin-bottom: 20px;}
</style>
<?php endif;?>
<?php include '../../common/view/footer.html.php';?>
