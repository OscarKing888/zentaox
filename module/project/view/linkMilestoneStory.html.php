<?php
/**
 * The link story view of project module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     project
 * @version     $Id: linkstory.html.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/tablesorter.html.php';?>
<div id='titlebar'>
  <div class='heading' style='margin-bottom: 15px'>
    <span class='prefix'><?php echo html::icon($lang->icons['story']);?></span>
    <strong><small><?php echo html::icon($lang->icons['link']);?></small> <?php echo $title?></strong>
  </div>
  <div class='actions'><?php echo html::a($this->server->http_referer, '<i class="icon-goback icon-level-up icon-large icon-rotate-270"></i> ' . $lang->goback, '', "class='btn'")?></div>
  <div id='querybox' class='show'></div>
</div>
<form method='post' class='form-condensed' id='linkStoryForm'>
  <table class='table tablesorter table-fixed table-selectable' id='linkStoryList'> 
    <thead>
    <tr>
      <th class='w-id'><?php echo $lang->idAB;?></th>
      <th class='w-pri'><?php echo $lang->priAB;?></th>
      <th class='w-150px'><?php echo $lang->story->product;?></th>
      <th class='w-150px'><?php echo $lang->story->module;?></th>
      <th><?php echo $lang->story->title;?></th>
      <th class='w-120px'><?php echo $lang->story->plan;?></th>
      <th class='w-80px'><?php echo $lang->story->stage;?></th>
      <?php if($productType != 'normal'):?>
      <th class='w-80px'><?php echo $lang->product->branchName[$productType];?></th>
      <?php endif;?>
      <th class='w-user'><?php echo $lang->openedByAB;?></th>
        <th class='w-80px'> <?php echo $lang->assignedToAB ?></th>

        <th title='<?php echo $lang->story->taskCount ?>'
            class='w-30px'><?php echo $lang->story->taskCountAB; ?></th>
        <th title='<?php echo $lang->story->bugCount ?>'
            class='w-30px'><?php echo $lang->story->bugCountAB; ?></th>
        <th title='<?php echo $lang->story->caseCount ?>'
            class='w-30px'><?php echo $lang->story->caseCountAB; ?></th>
        <th class='w-120px'>  <?php echo $lang->task->progress;?></th>
        <th class='w-120px'>  <?php echo $lang->task->progressOfStory;?></th>
      <th class='w-80px'><?php echo $lang->story->estimateAB;?></th>
    </tr>
    </thead>
    <tbody>
    <?php $storyCount = 0;?>

    <?php
//        foreach($milestoneStories as $mstory):
//            echo "<li>milestoneStories:" . $mstory . '</li>';
//        endforeach;
//
//        foreach($allStories as $story):
//            echo "<li>story:" . $story->id . ' isset:' . isset($milestoneStories[$story->id]) . '</li>';
//        endforeach;
     ?>

    <?php foreach($prjStories as $k => $story):?>

        <?php
        if(isset($milestoneStories[$story->id]))
        {
            //echo js::alert("skip:" . $story->id);
            continue;
        }

        if($story->project != $project)
        {
            //continue;
        }

        ?>

    <?php $storyLink = $this->createLink('story', 'view', "storyID=$story->id");?>
    <tr class='text-center'>
      <td class='cell-id'>
        <input type='checkbox' name='stories[]'  value='<?php echo $story->id;?>'/> 
        <?php echo html::hidden("products[$story->id]", $story->product);?>
        <?php echo html::a($storyLink, sprintf('%03d', $story->id));?>
      </td>
      <td><span class='<?php echo 'pri' . zget($lang->story->priList, $story->pri, $story->pri)?>'><?php echo zget($lang->story->priList, $story->pri, $story->pri);?></span></td>
      <td class='text-left' title='<?php echo $products[$story->product]->name?>'><?php echo html::a($this->createLink('product', 'browse', "productID=$story->product&branch=$story->branch"), $products[$story->product]->name, '_blank');?></td>
      <td class='text-left' title='<?php echo zget($modules, $story->module, '')?>'><?php echo zget($modules, $story->module, '')?></td>
      <td class='text-left nobr' title="<?php echo $story->title?>"><?php echo html::a($storyLink, $story->title);?></td>
      <td title='<?php echo $story->planTitle;?>'><?php echo $story->planTitle;?></td>
      <td><?php echo zget($lang->story->stageList, $story->stage);?></td>
      <?php if($productType != 'normal'):?>
      <td><?php if(isset($branchGroups[$story->product][$story->branch])) echo $branchGroups[$story->product][$story->branch];?></td>
      <?php endif;?>
      <td><?php echo zget($users, $story->openedBy, $story->openedBy);?></td>
        <td><?php echo $users[$story->assignedTo]; ?></td>
        <td class='linkbox'>
            <?php
            $tasksLink = $this->createLink('story', 'tasks', "storyID=$story->id&projectID=$project->id");
            $storyTasks[$story->id] > 0 ? print(html::a($tasksLink, $storyTasks[$story->id], '', 'class="iframe"')) : print(0);
            ?>
        <td>
            <?php
            $bugsLink = $this->createLink('story', 'bugs', "storyID=$story->id&projectID=$project->id");
            $storyBugs[$story->id] > 0 ? print(html::a($bugsLink, $storyBugs[$story->id], '', 'class="iframe"')) : print(0);
            ?>
        </td>
        <td>
            <?php
            $casesLink = $this->createLink('story', 'cases', "storyID=$story->id&projectID=$project->id");
            $storyCases[$story->id] > 0 ? print(html::a($casesLink, $storyCases[$story->id], '', 'class="iframe"')) : print(0);
            ?>
        </td>
        <td>
            <div class=<?php echo $story->taskProgress == 100 ? 'progressCompleted' : 'progressInCompleted' ?> >
                <?php echo $story->taskProgress . "%";?>
            </div>
        </td>

        <td>
            <div class=<?php echo $story->storyProgress == 100 ? 'progressCompleted' : 'progressInCompleted' ?> >
                <?php echo $story->storyProgress . "%";?>
            </div>
        </td>
      <td><?php echo $story->estimate;?></td>
    </tr>
    <?php $storyCount ++;?>
    <?php endforeach;?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan='<?php echo $productType == 'normal' ? '8' :'9';?>' class='text-left'>
          <div class='table-actions clearfix'>
          <?php 
          if($storyCount)
          {
              echo html::selectButton() . html::submitButton();
          }
          else
          {
              echo "<div class='text'>" . $lang->project->whyNoStories . '</div>';
          }
          ?>
          </div>
        </td>
      </tr>
    </tfoot>
  </table>
</form>

<script type='text/javascript'>
$(function()
{
    ajaxGetSearchForm()
    setTimeout(function(){fixedTheadOfList('#linkStoryList')}, 500);
    setTimeout(function(){fixedTfootAction('#linkStoryForm')}, 500);
});
</script>
<?php include '../../common/view/footer.html.php';?>
