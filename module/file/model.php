<?php
/**
 * The model file of file module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     file
 * @version     $Id: model.php 4976 2013-07-02 08:15:31Z wyd621@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php

class fileModel extends model
{
    public $savePath = '';
    public $webPath = '';
    public $now = 0;

    /**
     * Construct function.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->now = time();
        $this->setSavePath();
        $this->setWebPath();
    }

    /**
     * Get files of an object.
     *
     * @param  string $objectType
     * @param  string $objectID
     * @param  string $extra
     * @access public
     * @return array
     */
    public function getByObject($objectType, $objectID, $extra = '')
    {
        $files = $this->dao->select('*')->from(TABLE_FILE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq((int)$objectID)
            ->andWhere('extra')->ne('editor')
            ->beginIF($extra)->andWhere('extra')->eq($extra)
            ->orderBy('id')
            ->fetchAll('id');
        foreach ($files as $file) {
            $realPathName = $this->getRealPathName($file->pathname);
            $file->realPath = $this->savePath . $realPathName;
            $file->webPath = $this->webPath . $realPathName;
        }
        return $files;
    }

    /**
     * Get info of a file.
     *
     * @param  int $fileID
     * @access public
     * @return object
     */
    public function getById($fileID)
    {
        $file = $this->dao->findById($fileID)->from(TABLE_FILE)->fetch();

        $realPathName = $this->getRealPathName($file->pathname);
        $file->realPath = $this->savePath . $realPathName;
        $file->webPath = $this->webPath . $realPathName;
        return $file;
    }

    /**
     * Save upload.
     *
     * @param  string $objectType
     * @param  string $objectID
     * @param  string $extra
     * @param  string $filesName
     * @param  string $labelsName
     * @access public
     * @return array
     */
    public function saveUpload($objectType = '', $objectID = '', $extra = '', $filesName = 'files', $labelsName = 'labels')
    {
        $fileTitles = array();
        $now = helper::today();
        $files = $this->getUpload($filesName, $labelsName);

        //error_log("oscar: saveUpload============");
        foreach ($files as $id => $file) {
            //error_log("oscar: objType:$objectType file:" . $file['pathname']);
            if ($file['size'] == 0) continue;
            if (!move_uploaded_file($file['tmpname'], $this->savePath . $this->getSaveName($file['pathname']))) return false;

            $file = $this->compressImage($file);

            $file['objectType'] = $objectType;
            $file['objectID'] = $objectID;
            $file['addedBy'] = $this->app->user->account;
            $file['addedDate'] = $now;
            $file['extra'] = $extra;
            unset($file['tmpname']);
            $this->dao->insert(TABLE_FILE)->data($file)->exec();
            $fileTitles[$this->dao->lastInsertId()] = $file['title'];

            /*
            // oscar
            foreach ($file as $k => $item) {
                echo "<br> k:$k v:$item <br>";
            }
            $realPathName = $this->getRealPathName($file['pathname']);
            $file->realPath = $this->savePath . $realPathName;
            $file->webPath = $this->webPath . $realPathName;
            $this->checkAndGenThumb($file);
            // oscar
            //*/

        }

        return $fileTitles;
    }

    public function getHDThumbFile($file)
    {
        return $file->realPath . "_thumb_hd.jpg";
    }

    public function getThumbFile($file)
    {
        if (stripos($file->extension, 'gif') !== false) {
            return $file->realPath . "_thumb.gif";
        }

        return $file->realPath . "_thumb.jpg";
    }

    public function checkAndGenThumb($file)
    {
        $thumbImgPath = $this->getThumbFile($file);
        $thumbImgHDPath = $this->getHDThumbFile($file);


        $path = $file->realPath;
        //error_log("checkAndGenThumb:$path");
        $new_width = 256;

        if (!file_exists($path)) {
            echo "file not found:$path thum:$thumbImgPath thumHD:$thumbImgHDPath";
            exit;
        }

        if (stripos($file->extension, 'psd') !== false) {

            $Img4KW = 3840;
            $Img4KH = 2150;

            //foreach($psdImg->getImageProperties("*") as $k => $v) print("$k: $v<br/>\n");
            if (!file_exists($thumbImgPath)) {
                $psdImg = new Imagick($path);
                $psdImg->setIteratorIndex(0);
                $w = $psdImg->getImageWidth();
                if($new_width > $w)
                {
                    $new_width = $w;
                }

                //$new_width = max(256, $new_width);

                //$psdImg = new Imagick($path);
                //$psdImg->setImageIndex(0);
                $psdImg->setIteratorIndex(0);
                $psdImg->stripImage(); //去除图片信息
                $psdImg->setImageCompressionQuality(80); //图片质量
                //error_log("======= imgW:$new_width");
                $psdImg->thumbnailImage($new_width, 0);
                $psdImg->writeImage($thumbImgPath);
                $psdImg->destroy();
                //echo $psdImg;
            }

            //echo js::alert("$file->extension");

            //echo js::alert("psd:$thumbImgHDPath");
            //error_log("oscar =======psd:$thumbImgHDPath");

            if (!file_exists($thumbImgHDPath)) {
                $psdImg = new Imagick($path);
                $psdImg->setIteratorIndex(0);

                $imageSize  = getimagesize($file->realPath);
                $imageWidth = $imageSize ? $imageSize[0] : 0;

                $new_width = $psdImg->getImageWidth();
                $new_height = $psdImg->getImageHeight();

                $new_width = $imageSize ? $imageSize[0] : 0;
                $new_height = $imageSize ? $imageSize[1] : 0;

                $w = $new_width;
                $h = $new_height;

                //error_log("oscar: file:$path w:$w h:$h newW:$new_width newH:$new_height 4kw:$Img4KW 4kh:$Img4KH");

                if ($w > $Img4KW) {
                    $new_width = $Img4KW;
                }

                if ($h > $Img4KH)
                {
                    $new_height = $Img4KH;
                }

                //error_log("oscar: w:$w h:$h newW:$new_width newH:$new_height");


                //$psdImg->stripImage(); //去除图片信息
                $psdImg->setImageCompressionQuality(100); //图片质量

                if($w >= $h)
                {
                    //$psdImg->thumbnailImage($new_width, 0);
                    $psdImg->scaleImage($new_width, 0);
                }
                else
                {
                    //$psdImg->thumbnailImage(0, $new_height);
                    $psdImg->scaleImage(0, $new_height);
                }

                //$psdImg->thumbnailImage($w, $h, true);

                $psdImg->writeImage($thumbImgHDPath);
                $psdImg->destroy();
                //echo $psdImg;
            }
        } elseif (stripos($file->extension, 'gif') !== false) {
            if (!file_exists($thumbImgPath)) {

                $image = new Imagick($path);
                $w = $image->getImageWidth();
                $h = $image->getImageHeight();

                // 最多50像素，取最大边匹配
                $wc = (int)floor($w / 50);
                $hc = (int)floor($h / 50);

                $dc = $wc;
                if($wc < $hc)
                {
                    $dc = $hc;
                }

                $dc = max($dc, 1);

                $dw =  $w / $dc;
                $dh = $h / $dc;

                //error_log("oscar: ____ proc gif:$thumbImgPath src:$path");
                //error_log("oscar: ____ proc divide:$dc w:$w h:$h  dw:$dw dh:$dh");

                //$image = $image->coalesceImages();
                //$newImg = new Imagick();
                $newImg = $image;

                /*
                $i = true;
                while($image->hasNextImage()) {
                    if($i)
                    {
                        //$frame->thumbnailImage($w, $h);
                        $newImg->addImage($image);
                        //$newImg->removeImage();
                        //$i = false;
                        error_log("oscar:_____ add image:$image");
                    }
                    $image->nextImage();
                    $i = !$i;
                }


                //*/

                /*
                $newImg = $newImg->coalesceImages();
                foreach ($newImg as $frame) {
                    $frame->thumbnailImage($dw, $dh, true);
                }
                //*/

                // 抽帧，最多25帧，按1/3取最少帧数
                //*
                $newImg = $newImg->coalesceImages();
                $canvas = new Imagick();
                $m1frames = 25;
                $m1 = (int)round($newImg->getNumberImages() / $m1frames);
                $m2frames = $newImg->getNumberImages() / 3;
                if($m1frames < $m2frames)
                {
                    $m = $m1;
                }
                else
                {
                    $m = 3;
                }

                //error_log("oscar: t:".$newImg->getNumberImages()." m:$m  m1:$m1 m1f:$m1frames m2f:$m2frames");

                $i = 0;
                foreach($newImg as $frame){
                    if($i % $m == 0) {
                        $img = new Imagick();
                        $img->readImageBlob($frame);
                        $img->thumbnailImage($dw, $dh, true);

                        $canvas->addImage($img);
                        $canvas->setImageDelay($img->getImageDelay());
                    }
                    ++$i;
                }
                $newImg = $canvas;
                //*/

                $newImg = $newImg->optimizeImageLayers();
                $newImg->writeImages($thumbImgPath, true);
                $newImg->destroy();
            }
        } elseif (!file_exists($thumbImgPath)) {
            //$height = 0;
            $new_name = $thumbImgPath;
            //$tmp_name = "test.jpg";

            list($width, $height) = getimagesize($path);
            //echo "w:$width h:$height<br>";

            if($new_width > $width)
            {
                $new_width = $width;
                $new_height = $height;
            }
            else{
                if($width < $height)
                {
                    // 以高度为256最大
                    $new_height = $new_width;
                    $new_width = abs($new_height * $width/$height);
                }
                else{
                    $new_height = abs($new_width * $height / $width);
                }
            }

            $image_p = imagecreatetruecolor($new_width, $new_height);

            $image = null;

            if (stripos('bmp', $file->extension) !== false) {
                $image = imagecreatefrombmp($path);
            } elseif (stripos('gif', $file->extension) !== false) {
                //$image = imagecreatefromgif($path);
            } elseif (stripos('png', $file->extension) !== false) {
                $image = imagecreatefrompng($path);
            } elseif (stripos('psd', $file->extension) !== false) {
                //$image = imagecreatefrompsd($path);
            } else {
                $image = imagecreatefromjpeg($path);
            }

            imagecopyresampled($image_p, $image, 0, 0, 0, 0,
                $new_width, $new_height, $width, $height);

            imagejpeg($image_p, $new_name);

            //echo "type:$imgType saveResult:$ok $new_name<br>";

            //$imgPth = $this->createLink('file', 'downloadthumb', "fileID=$file->id");
            //echo "w:$new_width h:$new_height<br>path:$path <br>path:$thumbImgPath <br> imgPth:$imgPth<br>";
        }
    }

    /**
     * Get counts of uploaded files.
     *
     * @access public
     * @return int
     */
    public function getCount()
    {
        return count($this->getUpload());
    }

    /**
     * Get info of uploaded files.
     *
     * @param  string $htmlTagName
     * @param  string $labelsName
     * @access public
     * @return array
     */
    public function getUpload($htmlTagName = 'files', $labelsName = 'labels')
    {
        $files = array();
        if (!isset($_FILES[$htmlTagName])) return $files;

        $this->app->loadClass('purifier', true);
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.DefinitionImpl', null);
        $purifier = new HTMLPurifier($config);

        /* If the file var name is an array. */
        if (is_array($_FILES[$htmlTagName]['name'])) {
            //error_log("oscar:     ======== is_array");
            extract($_FILES[$htmlTagName]);
            foreach ($name as $id => $filename) {
                if (empty($filename)) continue;
                if (!validater::checkFileName($filename)) continue;

                $title = isset($_POST[$labelsName][$id]) ? $_POST[$labelsName][$id] : '';
                $file['extension'] = $this->getExtension($filename);
                $file['pathname'] = $this->setPathName($id, $file['extension']);
                //echo "set pathName1:" . $file['pathname'];
                $file['title'] = !empty($title) ? htmlspecialchars($title) : str_replace('.' . $file['extension'], '', $filename);
                $file['title'] = $purifier->purify($file['title']);
                $file['size'] = $size[$id];
                $file['tmpname'] = $tmp_name[$id];
                $files[] = $file;
            }
        } else {
            //error_log("     ======== !is_array");
            if (empty($_FILES[$htmlTagName]['name'])) return $files;
            extract($_FILES[$htmlTagName]);
            if (!validater::checkFileName($name)) return array();;
            $title = isset($_POST[$labelsName][0]) ? $_POST[$labelsName][0] : '';
            $file['extension'] = $this->getExtension($name);
            $file['pathname'] = $this->setPathName(0, $file['extension']);
            //echo "set pathName2:$file->pathname";
            $file['title'] = !empty($title) ? htmlspecialchars($title) : substr($name, 0, strpos($name, $file['extension']) - 1);
            $file['title'] = $purifier->purify($file['title']);
            $file['size'] = $size;
            $file['tmpname'] = $tmp_name;
            return array($file);
        }
        return $files;
    }

    /**
     * get uploaded file from zui.uploader.
     *
     * @param  string $htmlTagName
     * @access public
     * @return array
     */
    public function getUploadFile($htmlTagName = 'file')
    {
        if (!isset($_FILES[$htmlTagName]) || empty($_FILES[$htmlTagName]['name'])) return;

        $this->app->loadClass('purifier', true);
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.DefinitionImpl', null);
        $purifier = new HTMLPurifier($config);

        extract($_FILES[$htmlTagName]);
        if (!validater::checkFileName($name)) return array();
        if ($this->post->name) $name = $this->post->name;

        $file = array();
        $file['id'] = 0;
        $file['extension'] = $this->getExtension($name);
        $file['title'] = !empty($_POST['label']) ? htmlspecialchars($_POST['label']) : substr($name, 0, strpos($name, $file['extension']) - 1);
        $file['title'] = $purifier->purify($file['title']);
        $file['size'] = $_POST['size'];
        $file['tmpname'] = $tmp_name;
        $file['uuid'] = $_POST['uuid'];
        $file['pathname'] = $this->setPathName(0, $file['extension']);
        $file['chunkpath'] = 'chunks' . DS . 'f_' . $file['uuid'] . '.' . $file['extension'] . '.part';
        $file['chunks'] = isset($_POST['chunks']) ? intval($_POST['chunks']) : 0;
        $file['chunk'] = isset($_POST['chunk']) ? intval($_POST['chunk']) : 0;

        if (stripos($this->config->file->allowed, ',' . $file['extension'] . ',') === false) {
            $file['pathname'] = $file['pathname'] . '.notAllowed';
        }

        return $file;
    }

    /**
     * Save uploaded file from zui.uploader.
     *
     * @param  int $file
     * @param  int $uid
     * @access public
     * @return array|bool
     */
    public function saveUploadFile($file, $uid)
    {
        $uploadFile = array();

        $tmpFilePath = $this->app->getTmpRoot() . 'uploadfiles/';
        if (!is_dir($tmpFilePath)) mkdir($tmpFilePath, 0777, true);

        $tmpFileSavePath = $tmpFilePath . $uid . '/';
        if (!is_dir($tmpFileSavePath)) mkdir($tmpFileSavePath);

        $fileName = basename($file['pathname']);
        $fileName = strpos($fileName, '.') === false ? $fileName : substr($fileName, 0, strpos($fileName, '.'));
        $file['realpath'] = $tmpFileSavePath . $fileName;

        error_log("xxxxx: file.model.saveUploadFile fileName:$fileName");

        if ($file['chunks'] > 1) {
            $tmpFileChunkPath = $tmpFilePath . $file['chunkpath'];
            if (!file_exists($tmpFileChunkPath)) mkdir(dirname($tmpFileChunkPath));

            if ($file['chunk'] > 0) {
                $fileChunk = fopen($tmpFileChunkPath, 'a+b');
                $tmpChunkFile = fopen($file['tmpname'], 'rb');
                while ($buff = fread($tmpChunkFile, 4069)) {
                    fwrite($fileChunk, $buff);
                }
                fclose($fileChunk);
                fclose($tmpChunkFile);
            } else {
                if (!move_uploaded_file($file['tmpname'], $tmpFileChunkPath)) return false;
            }

            if ($file['chunk'] == ($file['chunks'] - 1)) {
                rename($tmpFileChunkPath, $file['realpath']);

                $uploadFile['extension'] = $file['extension'];
                $uploadFile['pathname'] = $file['pathname'];
                $uploadFile['title'] = $file['title'];
                $uploadFile['realpath'] = $file['realpath'];
                $uploadFile['size'] = $file['size'];
            }
        } else {
            if (!move_uploaded_file($file['tmpname'], $file['realpath'])) return false;

            $uploadFile['extension'] = $file['extension'];
            $uploadFile['pathname'] = $file['pathname'];
            $uploadFile['title'] = $file['title'];
            $uploadFile['realpath'] = $file['realpath'];
            $uploadFile['size'] = $file['size'];
        }

        return $uploadFile;
    }

    /**
     * Get extension of a file.
     *
     * @param  string $filename
     * @access public
     * @return string
     */
    public function getExtension($filename)
    {
        $extension = trim(strtolower(pathinfo($filename, PATHINFO_EXTENSION)));
        if (empty($extension) or stripos(",{$this->config->file->dangers},", ",{$extension},") !== false) return 'txt';
        if (empty($extension) or stripos(",{$this->config->file->allowed},", ",{$extension},") === false) return 'txt';
        if ($extension == 'php') return 'txt';
        return $extension;
    }

    /**
     * Get save name.
     *
     * @param  string $pathName
     * @access public
     * @return string
     */
    public function getSaveName($pathName)
    {
        $saveName = strpos($pathName, '.') === false ? $pathName : substr($pathName, 0, strpos($pathName, '.'));
        return $saveName;
    }

    /**
     * Get real path name.
     *
     * @param  string $pathName
     * @access public
     * @return string
     */
    public function getRealPathName($pathName)
    {
        $realPath = $this->savePath . $pathName;
        if (file_exists($realPath)) return $pathName;

        return $this->getSaveName($pathName);
    }

    /**
     * Get export tpl.
     *
     * @param  string $module
     * @access public
     * @return object
     */
    public function getExportTemplate($module)
    {
        return $this->dao->select('id,title,content,public')->from(TABLE_USERTPL)
            ->where('type')->eq("export$module")
            ->andwhere('account', $markLeft = true)->eq($this->app->user->account)
            ->orWhere('public')->eq('1')
            ->markRight(1)
            ->orderBy('id')
            ->fetchAll();
    }

    /**
     * Save export template.
     *
     * @param  string $module
     * @access public
     * @return int
     */
    public function saveExportTemplate($module)
    {
        $template = fixer::input('post')
            ->add('account', $this->app->user->account)
            ->add('type', "export$module")
            ->join('content', ',')
            ->get();

        $condition = "`type`='export$module' and account='{$this->app->user->account}'";
        $this->dao->insert(TABLE_USERTPL)->data($template)->batchCheck('title, content', 'notempty')->check('title', 'unique', $condition)->exec();
        return $this->dao->lastInsertId();
    }

    /**
     * Set path name of the uploaded file to be saved.
     *
     * @param  int $fileID
     * @param  string $extension
     * @access public
     * @return string
     */
    public function setPathName($fileID, $extension)
    {
        $sessionID = session_id();
        $randString = substr($sessionID, mt_rand(0, strlen($sessionID) - 5), 3);
        return date('Ym/dHis', $this->now) . $fileID . mt_rand(0, 10000) . $randString . '.' . $extension;
    }

    /**
     * Set save path.
     *
     * @access public
     * @return void
     */
    public function setSavePath()
    {
        $savePath = $this->app->getAppRoot() . "www/data/upload/{$this->app->company->id}/" . date('Ym/', $this->now);
        if (!file_exists($savePath)) {
            @mkdir($savePath, 0777, true);
            touch($savePath . 'index.html');
        }
        $this->savePath = dirname($savePath) . '/';
    }

    /**
     * Set the web path of upload files.
     *
     * @access public
     * @return void
     */
    public function setWebPath()
    {
        $this->webPath = $this->app->getWebRoot() . "data/upload/{$this->app->company->id}/";
    }

    /**
     * Insert the set image size code.
     *
     * @param  string $content
     * @param  int $maxSize
     * @access public
     * @return string
     */
    public function setImgSize($content, $maxSize = 0)
    {
        if (empty($content)) return $content;

        $readLinkReg = str_replace(array('%fileID%', '/', '.', '?'), array('[0-9]+', '\/', '\.', '\?'), helper::createLink('file', 'read', 'fileID=(%fileID%)', '\w+'));

        $content = preg_replace('/ src="(' . $readLinkReg . ')" /', ' onload="setImageSize(this,' . $maxSize . ')" src="$1" ', $content);
        $content = preg_replace('/ src="{([0-9]+)(\.(\w+))?}" /', ' onload="setImageSize(this,' . $maxSize . ')" src="' . helper::createLink('file', 'read', "fileID=$1", "$3") . '" ', $content);
        return str_replace(' src="data/upload', ' onload="setImageSize(this,' . $maxSize . ')" src="data/upload', $content);
    }

    /**
     * Replace a file.
     *
     * @access public
     * @return bool
     */
    public function replaceFile($fileID, $postName = 'upFile')
    {
        if ($files = $this->getUpload($postName)) {
            $file = $files[0];
            $filePath = $this->dao->select('pathname')->from(TABLE_FILE)->where('id')->eq($fileID)->fetch();
            $pathName = $filePath->pathname;
            $realPathName = $this->savePath . $this->getRealPathName($pathName);
            if (!is_dir(dirname($realPathName))) mkdir(dirname($realPathName));
            move_uploaded_file($file['tmpname'], $realPathName);

            $file['pathname'] = $pathName;
            $file = $this->compressImage($file);

            $fileInfo = new stdclass();
            $fileInfo->addedBy = $this->app->user->account;
            $fileInfo->addedDate = helper::now();
            $fileInfo->size = $file['size'];
            $this->dao->update(TABLE_FILE)->data($fileInfo)->where('id')->eq($fileID)->exec();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Paste image in kindeditor at firefox and chrome.
     *
     * @param  string $data
     * @access public
     * @return string
     */
    public function pasteImage($data, $uid = '')
    {
        if (empty($data)) return '';
        $data = str_replace('\"', '"', $data);

        $dataLength = strlen($data);
        if (ini_get('pcre.backtrack_limit') < $dataLength) ini_set('pcre.backtrack_limit', $dataLength);
        preg_match_all('/<img src="(data:image\/(\S+);base64,(\S+))".*\/>/U', $data, $out);
        foreach ($out[3] as $key => $base64Image) {
            $extension = strtolower($out[2][$key]);
            if (!in_array($extension, $this->config->file->imageExtensions)) die();
            $imageData = base64_decode($base64Image);

            $file['extension'] = $extension;
            $file['pathname'] = $this->setPathName($key, $file['extension']);
            $file['size'] = strlen($imageData);
            $file['addedBy'] = $this->app->user->account;
            $file['addedDate'] = helper::today();
            $file['title'] = basename($file['pathname']);

            file_put_contents($this->savePath . $this->getSaveName($file['pathname']), $imageData);
            $this->dao->insert(TABLE_FILE)->data($file)->exec();
            $fileID = $this->dao->lastInsertID();
            if ($uid) $_SESSION['album'][$uid][] = $fileID;

            $data = str_replace($out[1][$key], helper::createLink('file', 'read', "fileID=$fileID", $file['extension']), $data);
        }

        return $data;
    }

    /**
     * Parse CSV.
     *
     * @param  string $fileName
     * @access public
     * @return array
     */
    public function parseCSV($fileName)
    {
        $content = file_get_contents($fileName);
        /* Fix bug #890. */
        $content = str_replace("\x82\x32", "\x10", $content);
        $lines = explode("\n", $content);

        $col = -1;
        $row = 0;
        $data = array();
        foreach ($lines as $line) {
            $line = trim($line);
            $markNum = substr_count($line, '"') - substr_count($line, '\"');
            if (substr($line, -1) != ',' and (($markNum % 2 == 1 and $col != -1) or ($markNum % 2 == 0 and substr($line, -2) != ',"' and $col == -1))) $line .= ',';
            $line = str_replace(',"",', ',,', $line);
            $line = str_replace(',"",', ',,', $line);
            $line = preg_replace_callback('/(\"{2,})(\,+)/U', array($this, 'removeInterference'), $line);
            $line = str_replace('""', '"', $line);

            /* if only one column then line is the data. */
            if (strpos($line, ',') === false and $col == -1) {
                $data[$row][0] = trim($line, '"');
            } else {
                /* if col is not -1, then the data of column is not end. */
                if ($col != -1) {
                    $pos = strpos($line, '",');
                    if ($pos === false) {
                        $data[$row][$col] .= "\n" . $line;
                        $data[$row][$col] = str_replace('&comma;', ',', trim($data[$row][$col], '"'));
                        continue;
                    } else {
                        $data[$row][$col] .= "\n" . substr($line, 0, $pos + 1);
                        $data[$row][$col] = trim(str_replace('&comma;', ',', trim($data[$row][$col], '"')));
                        $line = substr($line, $pos + 2);
                        $col++;
                    }
                }

                if ($col == -1) $col = 0;
                /* explode cols with delimiter. */
                while ($line) {
                    /* the cell has '"', the delimiter is '",'. */
                    if ($line{0} == '"') {
                        $pos = strpos($line, '",');
                        if ($pos === false) {
                            $data[$row][$col] = $line;
                            /* if line is not empty, then the data of cell is not end. */
                            if (strlen($line) >= 1) continue 2;
                            $line = '';
                        } else {
                            $data[$row][$col] = substr($line, 0, $pos + 1);
                            $line = substr($line, $pos + 2);
                        }
                        $data[$row][$col] = str_replace('&comma;', ',', trim($data[$row][$col], '"'));
                    } else {
                        /* the delimiter default is ','. */
                        $pos = strpos($line, ',');
                        /* if line is not delimiter, then line is the data of cell. */
                        if ($pos === false) {
                            $data[$row][$col] = $line;
                            $line = '';
                        } else {
                            $data[$row][$col] = substr($line, 0, $pos);
                            $line = substr($line, $pos + 1);
                        }
                    }

                    $data[$row][$col] = trim(str_replace('&comma;', ',', trim($data[$row][$col], '"')));
                    $col++;
                }
            }
            $row++;
            $col = -1;
        }

        return $data;
    }

    /**
     * Remove interference for parse csv.
     *
     * @param  array $matchs
     * @access private
     * @return string
     */
    private function removeInterference($matchs)
    {
        return str_replace('""', '"', $matchs[1]) . str_replace(',', '&comma;', $matchs[2]);
    }

    /**
     * Process editor.
     *
     * @param  object $data
     * @param  string $editorList
     * @access public
     * @return object
     */
    public function processImgURL($data, $editorList, $uid = '')
    {
        if (is_string($editorList)) $editorList = explode(',', str_replace(' ', '', $editorList));
        if (empty($editorList)) return $data;

        $readLinkReg = helper::createLink('file', 'read', 'fileID=(%fileID%)', '(%viewType%)');
        $readLinkReg = str_replace(array('%fileID%', '%viewType%', '?', '/'), array('[0-9]+', '\w+', '\?', '\/'), $readLinkReg);
        $imageIdList = array();
        foreach ($editorList as $editorID) {
            if (empty($editorID) or empty($data->$editorID)) continue;

            $imgURL = $this->config->requestType == 'GET' ? '{$2.$1}' : '{$1.$2}';

            $content = $this->pasteImage($data->$editorID, $uid);
            if ($content) $data->$editorID = $content;
            $data->$editorID = preg_replace("/ src=\"$readLinkReg\" /", ' src="' . $imgURL . '" ', $data->$editorID);
            $data->$editorID = preg_replace("/ src=\"" . htmlspecialchars($readLinkReg) . "\" /", ' src="' . $imgURL . '" ', $data->$editorID);

            preg_match_all('/ src="{([0-9]+)\.\w+}"/', $data->$editorID, $matchs);
            if ($matchs[1]) {
                foreach ($matchs[1] as $imageID) $imageIdList[$imageID] = $imageID;
            }
        }

        if (!empty($_SESSION['album'][$uid])) {
            foreach ($_SESSION['album'][$uid] as $i => $imageID) {
                if (isset($imageIdList[$imageID])) $_SESSION['album']['used'][$uid][$imageID] = $imageID;
            }
        }
        return $data;
    }

    /**
     * Compress image
     *
     * @param  array $file
     * @access public
     * @return array
     */
    public function compressImage($file)
    {
        if (!extension_loaded('gd') or !function_exists('imagecreatefromjpeg')) return $file;

        $pathName = $file['pathname'];
        $fileName = $this->savePath . $this->getSaveName($pathName);
        $suffix = $file['extension'];
        $lowerSuffix = strtolower($suffix);

        if (!in_array($lowerSuffix, $this->config->file->image2Compress)) return $file;

        $quality = 85;
        $newSuffix = '.jpg';
        $compressedName = str_replace($suffix, $newSuffix, $pathName);

        $res = $lowerSuffix == '.bmp' ? $this->imagecreatefrombmp($fileName) : imagecreatefromjpeg($fileName);
        imagejpeg($res, $fileName, $quality);

        $file['pathname'] = $compressedName;
        $file['extension'] = ltrim($newSuffix, '.');
        $file['size'] = filesize($fileName);
        return $file;
    }

    /**
     * Read 24bit BMP files
     * Author: de77
     * Licence: MIT
     * Webpage: de77.com
     * Version: 07.02.2010
     * Source : https://github.com/acustodioo/pic/blob/master/imagecreatefrombmp.function.php
     *
     * @param  string $filename
     * @access public
     * @return resource
     */
    public function imagecreatefrombmp($filename)
    {
        $f = fopen($filename, "rb");

        //read header    
        $header = fread($f, 54);
        $header = unpack('c2identifier/Vfile_size/Vreserved/Vbitmap_data/Vheader_size/' .
            'Vwidth/Vheight/vplanes/vbits_per_pixel/Vcompression/Vdata_size/' .
            'Vh_resolution/Vv_resolution/Vcolors/Vimportant_colors', $header);

        if ($header['identifier1'] != 66 or $header['identifier2'] != 77)
            return false;

        if ($header['bits_per_pixel'] != 24)
            return false;

        $wid2 = ceil((3 * $header['width']) / 4) * 4;

        $wid = $header['width'];
        $hei = $header['height'];

        $img = imagecreatetruecolor($header['width'], $header['height']);

        //read pixels
        for ($y = $hei - 1; $y >= 0; $y--) {
            $row = fread($f, $wid2);
            $pixels = str_split($row, 3);

            for ($x = 0; $x < $wid; $x++) {
                imagesetpixel($img, $x, $y, $this->dwordize($pixels[$x]));
            }
        }
        fclose($f);
        return $img;
    }

    /**
     * Dwordize for imagecreatefrombmp
     *
     * @param  streing $str
     * @access private
     * @return int
     */
    private function dwordize($str)
    {
        $a = ord($str[0]);
        $b = ord($str[1]);
        $c = ord($str[2]);
        return $c * 256 * 256 + $b * 256 + $a;
    }

    /**
     * Update objectID.
     *
     * @param  int $uid
     * @param  int $objectID
     * @param  string $objectType
     * @access public
     * @return bool
     */
    public function updateObjectID($uid, $objectID, $objectType)
    {
        if (empty($uid)) return true;

        $data = new stdclass();
        $data->objectID = $objectID;
        $data->objectType = $objectType;
        $data->extra = 'editor';
        if (isset($_SESSION['album'][$uid]) and $_SESSION['album'][$uid]) {
            $this->dao->update(TABLE_FILE)->data($data)->where('id')->in($_SESSION['album'][$uid])->exec();
            return !dao::isError();
        }
    }

    /**
     * Revert real src.
     *
     * @param  object $data
     * @param  string $fields
     * @access public
     * @return object
     */
    public function replaceImgURL($data, $fields)
    {
        if (is_string($fields)) $fields = explode(',', str_replace(' ', '', $fields));
        foreach ($fields as $field) {
            if (empty($field) or empty($data->$field)) continue;
            $data->$field = preg_replace('/ src="{([0-9]+)(\.(\w+))?}" /', ' src="' . helper::createLink('file', 'read', "fileID=$1", "$3") . '" ', $data->$field);
        }
        return $data;

    }

    /**
     * Auto delete useless image.
     *
     * @param  int $uid
     * @access public
     * @return void
     */
    public function autoDelete($uid)
    {
        if (!empty($_SESSION['album'][$uid])) {
            foreach ($_SESSION['album'][$uid] as $i => $imageID) {
                if (!isset($_SESSION['album']['used'][$uid][$imageID])) {
                    $file = $this->getById($imageID);
                    $this->dao->delete()->from(TABLE_FILE)->where('id')->eq($imageID)->exec();
                    @unlink($file->realPath);
                }
            }
            unset($_SESSION['album'][$uid]);
        }
    }
}
