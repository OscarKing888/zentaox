<?php
$sessionString  = $config->requestType == 'PATH_INFO' ? '?' : '&';
$sessionString .= session_name() . '=' . session_id();
?>

<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>

<style>
    .files-list {margin: 0;}
    .files-list > .list-group-item {padding: 0px; border:0px;}
    .files-list > .list-group-item a, .files-list > .list-group-item span{color: #666}
    .files-list > .list-group-item:hover a, .files-list > .list-group-item:hover span{color: #333}
    .files-list > .list-group-item > .right-icon {opacity: 0.01; transition: all 0.3s;}
    .files-list > .list-group-item:hover >
    .right-icon {opacity: 1}
    .files-list .btn-icon > i {font-size:15px}
</style>

<script language='Javascript'>
    $(function(){
        $(".edit").modalTrigger({width:350, type:'iframe'});
    })

    /* Delete a file. */
    function deleteFile(fileID)
    {
        if(!fileID) return;
        hiddenwin.location.href =createLink('file', 'delete', 'fileID=' + fileID);
    }
    /* Download a file, append the mouse to the link. Thus we call decide to open the file in browser no download it. */
    function downloadFile(fileID, extension, imageWidth)
    {
        if(!fileID) return;
        var fileTypes     = 'txt,jpg,jpeg,gif,png,bmp';
        var sessionString = '<?php echo $sessionString;?>';
        var windowWidth   = $(window).width();
        var url           = createLink('file', 'download', 'fileID=' + fileID + '&mouse=left') + sessionString;
        width = (windowWidth > imageWidth) ? ((imageWidth < windowWidth*0.5) ? windowWidth*0.5 : imageWidth) : windowWidth;
        if(fileTypes.indexOf(extension) >= 0)
        {
            $('<a>').modalTrigger({url: url, type: 'iframe', width: width}).trigger('click');
        }
        else
        {
            window.open(url, '_blank');
        }
        return false;
    }

    function downloadthumbFile(fileID, extension, imageWidth)
    {
        if(!fileID) return;
        var fileTypes     = 'txt,jpg,jpeg,gif,png,bmp';
        var sessionString = '<?php echo $sessionString;?>';
        var windowWidth   = $(window).width();
        var url           = createLink('file', 'downloadthumb', 'fileID=' + fileID + '&mouse=left') + sessionString;
        width = (windowWidth > imageWidth) ? ((imageWidth < windowWidth*0.5) ? windowWidth*0.5 : imageWidth) : windowWidth;
        if(fileTypes.indexOf(extension) >= 0)
        {
            $('<a>').modalTrigger({url: url, type: 'iframe', width: width}).trigger('click');
        }
        else
        {
            window.open(url, '_blank');
        }
        return false;
    }

</script>

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
        if ($article->owner == $this->app->user->account) {
            common::printIcon('artstation', 'edit', "id=$article->id", $article);
        }
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


            //phpinfo();
            ?>

            <?php foreach ($files as $file): ?>
                <fieldset>
                    <legend><?php echo "版本 - V." . ($v) . "&nbsp;&nbsp;&nbsp;&nbsp;" . $lang->file->uploadDate . substr($file->addedDate, 0, 10); $v--;?></legend>
                    <div class='content'>
                        <?php
                        $img = html::image($this->createLink('file', 'read', "fileID=$file->id"), "$imgAttr title='$file->title'");
                        if(stripos('jpg|jpeg|gif|png|bmp|psd', $file->extension) !== false)
                        {
                            $imageSize  = getimagesize($file->realPath);
                            $imageWidth = $imageSize ? $imageSize[0] : 0;
                        }

                        echo html::a($this->createLink('file', 'download', "fileID=$file->id") . $sessionString,
                        $img, '_blank', "onclick=\"return downloadFile($file->id, '$file->extension', $imageWidth)\"");

                        echo "<div align='right'>";
                        echo html::a($this->createLink('file', 'download', "fileID=$file->id") . $sessionString,
                            "下载", '_blank',
                            "class='btn' onclick=\"return downloadFileToDisk($file->id, '$file->extension')\"");
                        echo "</div>";

                        //echo html::a($this->createLink('file', 'downloadthumb', "fileID=$file->id") . $sessionString,
                        //    $img, '_blank', "onclick=\"return downloadthumbFile($file->id, '$file->extension', $imageWidth)\"");

                        //$imgPth = $this->createLink('file', 'readthumb', "fileID=$file->id");
                        //echo "<IMG src = '$imgPth'>";
                        //echo html::image($this->createLink('file', 'readthumb', "fileID=$file->id"), "$imgAttr title='$file->title'");
                        ?>
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
                        <th class='w-80px'><?php echo $lang->artstation->like; ?></th>
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
                                echo html::commonButton($lang->artstation->like, "id='like' onclick=\"on_like('$likeBy', '$article->id')\"");
                            }
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <th class='w-80px'><?php echo $lang->file->common; ?></th>
                        <td>
                            <?php echo $this->fetch('file', 'printFiles', array('files' => $article->files, 'fieldset' => 'false'));?>
                        </td>
                    </tr>

                    <tr>
                        <th></th>
                        <td>
                            <?php
                            if ($article->owner == $this->app->user->account) {
                                echo html::a(inlink('edit', "id=$article->id"), $lang->artstation->edit, '', "class='btn'");
                            }
                            echo html::backButton();
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>

            <fieldset>
                <legend><?php echo $lang->artstation->comment; ?></legend>

                <table class='table table-data table-condensed table-borderless'>
                    <?php $l = 1; foreach ($comments as $comment):?>
                    <tr>
                        <td>
                            <?php
                            echo "第 $l 楼&nbsp;&nbsp;" .
                                $comment->date . "&nbsp;&nbsp;" .
                                $allUsers[$comment->owner] . "<br>" .
                                $comment->content . "<br><br>";
                            ++$l;
                            ?>
                        </td>
                    </tr>
                    <?php endforeach;?>
                </table>

                <form method='post'>
                    <table class='table table-form table-borderless'>
                        <tr>
                            <td>
                                <?php echo html::input("imageid", $article->id, "class='hidden form-control text-left' autocomplete='on'"); ?>
                                <?php echo html::textarea('content', '', "rows='5' class='form-control'");?>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">
                                <?php echo html::submitButton(); ?>
                            </td>
                        </tr>
                    </table>
                </form>
            </fieldset>
        </div>
    </div>
</div>

<?php echo html::backButton(); ?>

<?php include '../../common/view/footer.html.php'; ?>
