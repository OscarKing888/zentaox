<?php
$config->buildex = new stdclass();
$config->buildex->create = new stdclass();
$config->buildex->edit   = new stdclass();
$config->buildex->create->requiredFields = 'product,name,date,shippingType,svnTagOperator,srcSVNPath';
$config->buildex->edit->requiredFields   = 'product,name,date,shippingType,svnTagOperator,srcSVNPath';

$config->buildex->editor = new stdclass();
$config->buildex->editor->create = array('id' => 'desc', 'tools' => 'simpleTools');
$config->buildex->editor->edit   = array('id' => 'desc', 'tools' => 'simpleTools');
