<div class='row-table'>
    <div class='col-main'>
        <div class='main'>

            <?php foreach ($articles as $article): ?>
                <?php

                $file = end($article->files);

                foreach ($article->files as $f) {
                    if ($article->confirmdesign == $f->id) {
                        $file = $f;
                        //echo js::alert("oscar:" . $file->id);
                        break;
                    }
                }

                if (!empty($file)) {
                    $imageSize = getimagesize($file->realPath);
                    $imageWidth = $imageSize ? $imageSize[0] : 256;
                    $imageHeight = $imageSize ? $imageSize[1] : 256;
                    $imgAttr = "";
                    if ($imageWidth > $imageHeight) {
                        $imgAttr = " width='256' ";
                    } else {
                        $imgAttr = " height='256' ";
                    }

                    //echo "w:$imageWidth h:$imageHeight";

                    if ($article->confirmdesign == $file->id) {
                        $imgAttr .= " class='confirmed'";
                    }

                    $img = html::image($this->createLink('file', 'readthumb', "fileID=$file->id"), "$imgAttr title='$file->title'");
                    echo html::a(inlink('view', "id=$article->id"), $img);
                } else {
                    echo html::a(inlink('view', "id=$article->id"),
                        "<img src='error.png'/>");
                }

                if ($this->app->user->account == "admin") {
                    echo html::a(inlink('delete', "id=$article->id"),
                        $lang->artstation->delete);
                }

                ?>

            <?php endforeach; ?>

            <div align="right" class='content'>
                <?php
                $pager->show();
                ?>
            </div>
        </div>
    </div>
</div>
