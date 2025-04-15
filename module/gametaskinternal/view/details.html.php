
<?php
/**
 * The html template file of index method of pipeline module of ZenTaoPHP.
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
<?php include 'debug.html.php'; ?>
<?php
include '../../common/view/chart.html.php';
include '../../common/view/datepicker.html.php';
include '../../common/view/datatable.fix.html.php';
?>

<?php include "headerversion.html.php"; ?>
<?php include "taskdatatable.html.php"; ?>

<?php include '../../common/view/footer.html.php'; ?>
<?php
//echo js::alert("datatableId:" . $this->view->datatableId . "module:$module method:$method mode:$mode");
?>
