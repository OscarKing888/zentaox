

<div class='sub-featurebar'>
    <ul class='nav'>
        <?php foreach($versions as $key => $v):?>
        <?php
        echo "<li id='assignedToTab_' . $key>" . html::a(inlink($methodName, "orderBy=$orderBy&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID&matchVer=$key"), $v) . "</li>";
        ?>
        <?php endforeach;?>
    </ul>
</div>