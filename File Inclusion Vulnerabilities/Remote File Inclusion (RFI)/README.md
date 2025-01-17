# PHP Reverse Shell

## Overview

This repository contains a PHP script for establishing a reverse shell. It is intended for ethical hacking, penetration testing, and educational purposes only. The script enables a user to connect back to a listening server and execute commands as the web server user.

**DISCLAIMER:**
This tool is provided for legal purposes only. Users take full responsibility for any actions performed using this tool. The author accepts no liability for any damages caused. Ensure you have proper authorization before using this tool in any environment.

---

## Features

- Establishes a reverse TCP connection to a hardcoded IP address and port.
- Provides a shell running with the privileges of the user executing the script (e.g., Apache user).
- Configurable IP address and port for flexibility.
- Compatible with PHP 4.3+ and 5+ (some features may require additional PHP extensions).

---

## How It Works

1. **Reverse TCP Connection**: 
   - The script connects back to a specified IP and port using PHP's `fsockopen()` function.
2. **Shell Execution**: 
   - It spawns an interactive shell (`/bin/sh -i`) and sends the input/output over the TCP connection.
3. **Non-blocking Streams**:
   - Uses PHP's `stream_set_blocking()` to ensure smooth communication without blocking.
4. **Daemonization**:
   - Attempts to daemonize the PHP process to avoid leaving zombie processes.

---

## Usage

### 1. Modify Script Parameters
Update the following variables in the script:
```php
$ip = '127.0.0.1';  // Replace with your listener's IP
$port = 1234;       // Replace with your desired port
