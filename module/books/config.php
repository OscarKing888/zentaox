<?php
$config->books = new stdClass();
$config->books->adminAccounts = array('admin'=>'admin','oldwestdoor'=>'oldwestdoor','zhangyi'=>'张懿');

$config->books->editor = new stdclass();
$config->books->editor->create     = array('id' => 'desc', 'tools' => 'simpleTools');
$config->books->editor->edit     = array('id' => 'desc', 'tools' => 'simpleTools');


$config->books->fields = 'bookName,type,desc,price';
$config->books->imageContentFieldName = "desc";

$config->books->borrowFields = 'bookid,reader,borrowDays';

define('TABLE_BOOKS',                   'gamebooks');
define('TABLE_BOOKSBORROWLOG',         'gamebooksborrowlog');

$config->books->typeList[0] = '未分类';
$config->books->typeList[1] = '设计类';
$config->books->typeList[2] = '艺术类';
$config->books->typeList[3] = '技术类';

$config->books->borrowLongDays = '长期';