<?php
/**
 * The html template file of index method of blog module of ZenTaoPHP.
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

<div id='titlebar'>
    <div class='heading'>
        <span class='prefix'><?php echo html::icon($lang->icons['task']); ?>
            <strong><?php echo $article->id; ?></strong>
        </span>
        <strong style='color: <?php echo $task->color; ?>'>
            <?php echo $article->title; ?>
        </strong>
        <?php if ($article->deleted): ?>
            <span class='label label-danger'><?php echo $lang->artstation->deleted; ?></span>
        <?php endif; ?>
    </div>
    <div class='actions'>
        <?php

        $actionLinks = '';

        ob_start();

        echo "<div class='btn-group'>";
        common::printIcon('artstation', 'edit', "id=$article->id", $article);
        echo '</div>';

        $actionLinks = ob_get_contents();
        ob_end_clean();
        echo $actionLinks;
        ?>
    </div>
</div>


<div class='row-table'>
    <div class='col-main'>
        <div class='main'>

            <?php
            $files = array_reverse($article->files);
            $v = count($files);
            ?>

            <?php foreach ($files as $file): ?>
                <fieldset>
                    <legend><?php echo "版本 - V." . ($v); $v--;?></legend>
                    <div class='content'>
                        <?php  echo html::image($this->createLink('file', 'read', "fileID=$file->id"), "$imgAttr title='$file->title'"); ?>
                    </div>
                </fieldset>
            <?php endforeach; ?>

        </div>
    </div>

    <div class='col-side'>
        <div class='main main-side'>
            <fieldset>
                <legend><?php echo $lang->artstation->legendBasic; ?></legend>
                <table class='table table-data table-condensed table-borderless'>
                    <tr>
                        <th class='w-80px'><?php echo $lang->artstation->title; ?></th>
                        <td><?php echo $article->title; ?></td>
                    </tr>

                    <tr>
                        <th class='w-80px'><?php echo $lang->artstation->owner; ?></th>
                        <td><?php echo $allUsers[$article->owner]; ?></td>
                    </tr>

                    <tr>
                        <th class='w-80px'><?php echo $lang->artstation->createDate; ?></th>
                        <td><?php echo $article->createDate; ?></td>
                    </tr>

                    <tr>
                        <th class='w-80px'><?php echo $lang->product->name; ?></th>
                        <td><?php echo $allProducts[$article->product]; ?></td>
                    </tr>

                    <tr>
                        <th class='w-80px'><?php echo $lang->artstation->type; ?></th>
                        <td><?php echo $lang->artstation->typeList[$article->type]; ?></td>
                    </tr>

                    <tr>
                    <th class='w-80px'><?php echo $lang->artstation->requirement; ?></th>
                    <td><?php echo $article->requirement; ?></td>
                    </tr>


                    <tr>
                        <th class='w-80px'><?php echo $lang->artstation->tags; ?></th>
                        <td><?php echo $article->tags; ?></td>
                    </tr>

                    <tr>
                        <th class='w-80px'><?php echo $lang->artstation->content; ?></th>
                        <td><?php echo $article->content; ?></td>
                    </tr>

                    <tr>
                        <th class='w-80px'><?php echo $lang->artstation->Like; ?></th>
                        <td>
                            <?php
                            echo !empty($article->likes) ? count($article->likes) : 0;
                            $likeBy = $this->app->user->account;

                            /*
                            echo "likeBy:$likeBy";
                            foreach ($article->likes as $k => $like) {
                                echo " like_key:$k";
                            }
                            //*/

                            if(!array_key_exists($likeBy, $article->likes))
                            {
                                echo html::commonButton($lang->artstation->Like, "id='like' onclick=\"on_like('$likeBy', '$article->id')\"");
                            }
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <th><?php echo "操作" ?></th>
                        <td>
                            <?php
                            if ($article->owner == $this->app->user->account) {
                                echo html::a(inlink('edit', "id=$article->id"), $lang->artstation->edit);
                            }
                            echo html::backButton();
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
    </div>
</div>

<?php echo html::backButton(); ?>

<?php include '../../common/view/footer.html.php'; ?>
