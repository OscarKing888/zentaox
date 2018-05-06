<?php

$lang->menu->pmshelp     = 'Help|pmshelp|index';

/*oscar:Blog menu*/
$lang->pmshelp = new stdclass();
$lang->pmshelp->menu = new stdclass();
$lang->pmshelp->menu->index        = '首页|pmshelp|index';


/* pipeline menu order. */
$lang->menuOrder[-1]  = 'pmshelp';
$lang->pmshelp->menuOrder[5]  = 'index';
