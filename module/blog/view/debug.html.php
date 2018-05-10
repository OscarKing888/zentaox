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

<?php if($this->config->blog->debug):?>
===========
<?php echo $this->post->uid; ?>
<br>
<br>
<?php echo $this->moduleName; ?>
<br>
<?php echo $this->app->user->account; ?>
<br>

===========

    <thead>
    <?php foreach ($depts as $dept): ?>
        <th><?php echo $dept ?></th>
    <?php endforeach; ?>
    </thead>

    <?php echo $showstat; echo html::radio('showstat', $lang->blog->showstatList, $showstat ? "show" : "hide");?>

<?php endif;?>
