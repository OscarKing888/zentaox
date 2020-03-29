
<div id='titlebar'>
    <div class='heading'>
        <span class='prefix'><?php echo html::icon($lang->icons['story']); ?> <strong><?php echo $story->id; ?></strong></span>
        <strong style='color: <?php echo $story->color; ?>'><?php echo $story->title; ?></strong><span class='red'><?php echo " - " . $users[$story->assignedTo]; ?></span>
    </div>
</div>