<?php

$secret = '12345678'; // Same secret as GitHub webhook

$headers = getallheaders();
$rawPost = file_get_contents("php://input");

if (!isset($headers['X-Hub-Signature-256'])) {
    http_response_code(403);
    exit('Missing signature');
}

$hash = 'sha256=' . hash_hmac('sha256', $rawPost, $secret);
if (!hash_equals($hash, $headers['X-Hub-Signature-256'])) {
    http_response_code(403);
    exit('Invalid signature');
}

// Define paths
$repoPath = '/www/wwwroot/test.mirpurianscafe.com';
$userFile = "$repoPath/user.txt";
$logFile = "$repoPath/deploy-log.txt";

// Ensure files exist and have write permission
@touch($userFile);
@touch($logFile);
@chmod($userFile, 0666); // Allow web user to write
@chmod($logFile, 0666);

// Log current user
file_put_contents($userFile, shell_exec('whoami'));

// Log webhook trigger time
file_put_contents($logFile, "Webhook triggered at: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// Execute the deploy shell script
$command = "sudo -u ajmain /home/ajmain/deploy.sh";
exec($command, $output, $result);

// Log the output and result
file_put_contents($logFile, "Output: " . implode("\n", $output) . "\nResult: $result\n", FILE_APPEND);

// Display output (for debugging)
echo "<pre>" . implode("\n", $output) . "</pre>";

?>
