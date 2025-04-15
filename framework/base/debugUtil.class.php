<?php
/**
 * ZenTaoPHP的baseHelper类。
 * The baseHelper class file of ZenTaoPHP framework.
 *
 * @package framework
 *
 * The author disclaims copyright to this source code. In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */

class debugUtil
{
    // oscar[
    static public function console_log( $data ){
        echo '<script>';
        echo 'console.log('. json_encode( $data ) .')';
        echo '</script>';
    }
    // oscar]
}
