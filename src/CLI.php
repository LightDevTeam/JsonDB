#!/usr/bin/php
<?php

// Include any helper functions or classes needed

// Check for the command and execute corresponding functionality
if ($argc < 2) {
    echo "Usage: php CLI.php <command> [options]\n";
    echo "Get help: https://github.com/LightDevTeam/JsonDB/wiki/CLI\n";
    exit(1);
}

// Parse the command and options
$command = strtolower($argv[1]);

switch ($command) {
    case 'user':
        handleUserCommand();
        break;
    case 'state':
        displayUserCount();
        break;
    default:
        echo "Command '$command' not recognized.\n";
        exit(1);
}

// Functions to handle different commands
function handleUserCommand() {
    global $argc, $argv;

    if ($argc < 3) {
        echo "Usage: php CLI.php user <subcommand> [options]\n";
        exit(1);
    }

    $subcommand = strtolower($argv[2]);

    switch ($subcommand) {
        case 'create':
            handleUserCreateCommand();
            break;
        case 'delete':
            handleUserDeleteCommand();
            break;
        case 'change-password':
            handleChangePasswordCommand();
            break;
        case 'change-jurisdiction':
            handleChangeJurisdictionCommand();
            break;
        default:
            echo "User subcommand '$subcommand' not recognized.\n";
            exit(1);
    }
}

function handleUserCreateCommand() {
    global $argc, $argv;

    // Default values
    $username = null;
    $jurisdiction = null;
    $password = null;

    // Parse options
    for ($i = 3; $i < $argc; $i++) {
        switch ($argv[$i]) {
            case '-u':
                if ($i + 1 < $argc) {
                    $username = $argv[++$i];
                } else {
                    echo "Missing username value for option -u.\n";
                    exit(1);
                }
                break;
            case '-j':
                if ($i + 1 < $argc) {
                    $jurisdiction = $argv[++$i];
                } else {
                    echo "Missing jurisdiction value for option -j.\n";
                    exit(1);
                }
                break;
            case '-p':
                if ($i + 1 < $argc) {
                    $password = $argv[++$i];
                } else {
                    echo "Missing password value for option -p.\n";
                    exit(1);
                }
                break;
            default:
                echo "Unknown option '{$argv[$i]}'.\n";
                exit(1);
        }
    }

    // Validate required parameters
    if (!$username || !$jurisdiction || !$password) {
        echo "Missing required options.\n";
        exit(1);
    }

    // Implement user creation logic
    createUser($username, $jurisdiction, $password);
}

function handleUserDeleteCommand() {
    global $argc, $argv;

    // Ensure correct number of arguments
    if ($argc !== 5 || $argv[3] !== '-u') {
        echo "Usage: php CLI.php user delete -u <username>\n";
        exit(1);
    }

    $username = $argv[4];

    // Implement user deletion logic
    deleteUser($username);
}

function handleChangePasswordCommand() {
    global $argc, $argv;

    // Default values
    $username = null;
    $newPassword = null;

    // Parse options
    for ($i = 3; $i < $argc; $i++) {
        switch ($argv[$i]) {
            case '-u':
                if ($i + 1 < $argc) {
                    $username = $argv[++$i];
                } else {
                    echo "Missing username value for option -u.\n";
                    exit(1);
                }
                break;
            case '-p':
                if ($i + 1 < $argc) {
                    $newPassword = $argv[++$i];
                } else {
                    echo "Missing new password value for option -p.\n";
                    exit(1);
                }
                break;
            default:
                echo "Unknown option '{$argv[$i]}'.\n";
                exit(1);
        }
    }

    // Validate required parameters
    if (!$username || !$newPassword) {
        echo "Missing required options.\n";
        exit(1);
    }

    // Implement change password logic
    changeUserPassword($username, $newPassword);
}

function handleChangeJurisdictionCommand() {
    global $argc, $argv;

    // Default values
    $username = null;
    $newJurisdiction = null;

    // Parse options
    for ($i = 3; $i < $argc; $i++) {
        switch ($argv[$i]) {
            case '-u':
                if ($i + 1 < $argc) {
                    $username = $argv[++$i];
                } else {
                    echo "Missing username value for option -u.\n";
                    exit(1);
                }
                break;
            case '-j':
                if ($i + 1 < $argc) {
                    $newJurisdiction = $argv[++$i];
                } else {
                    echo "Missing new jurisdiction value for option -j.\n";
                    exit(1);
                }
                break;
            default:
                echo "Unknown option '{$argv[$i]}'.\n";
                exit(1);
        }
    }

    // Validate required parameters
    if (!$username || !$newJurisdiction) {
        echo "Missing required options.\n";
        exit(1);
    }

    // Implement change jurisdiction logic
    changeUserJurisdiction($username, $newJurisdiction);
}

// Functions to interact with user.inf file
function createUser($username, $jurisdiction, $password) {
    $file = fopen('user.inf', 'a');
    if ($file) {
        $hashedPassword = hash('sha256', $password);
        fwrite($file, "$username:$jurisdiction:$hashedPassword\n");
        fclose($file);
        echo "User created successfully.\n";
    } else {
        echo "Failed to open user.inf file for writing.\n";
        exit(1);
    }
}

function deleteUser($username) {
    $file = file('user.inf');
    if ($file) {
        $out = fopen('user.inf', 'w');
        foreach($file as $line) {
            if(strpos($line, "$username:") === 0) continue;
            fwrite($out, $line);
        }
        fclose($out);
        echo "User deleted successfully.\n";
    } else {
        echo "Failed to open user.inf file for deleting.\n";
        exit(1);
    }
}

function changeUserPassword($username, $newPassword) {
    $file = file('user.inf');
    if ($file) {
        $out = fopen('user.inf', 'w');
        foreach($file as $line) {
            if(strpos($line, "$username:") === 0){
                list($user, $jurisdiction, $oldPassword) = explode(':', $line);
                $hashedPassword = hash('sha256', $newPassword);
                $line = "$user:$jurisdiction:$hashedPassword\n";
            }
            fwrite($out, $line);
        }
        fclose($out);
        echo "Password changed successfully.\n";
    } else {
        echo "Failed to open user.inf file for password change.\n";
        exit(1);
    }
}

function changeUserJurisdiction($username, $newJurisdiction) {
    $file = file('user.inf');
    if ($file) {
        $out = fopen('user.inf', 'w');
        foreach($file as $line) {
            if(strpos($line, "$username:") === 0){
                list($user, $oldJurisdiction, $password) = explode(':', $line);
                $line = "$user:$newJurisdiction:$password\n";
            }
            fwrite($out, $line);
        }
        fclose($out);
        echo "Jurisdiction changed successfully.\n";
    } else {
        echo "Failed to open user.inf file for jurisdiction change.\n";
        exit(1);
    }
}

function displayUserCount() {
    $file = file('user.inf');
    if ($file) {
        $userCount = count($file);
        echo "Total users: $userCount\n";
    } else {
        echo "Failed to open user.inf file.\n";
        exit(1);
    }
}

?>
