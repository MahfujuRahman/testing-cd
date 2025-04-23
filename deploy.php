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

// Run the deploy shell script as 'ajmain'
$repoPath = '/www/wwwroot/test.mirpurianscafe.com';
$branch = 'main'; // or master

// Log current user for debugging
file_put_contents('user.txt', shell_exec('whoami'));

// Log webhook trigger time
file_put_contents("$repoPath/deploy-log.txt", "Webhook triggered at: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// Execute the deploy shell script
$command = "sudo -u ajmain /home/ajmain/deploy.sh";
exec($command, $output, $result);

// Log the output and result of the command
file_put_contents("$repoPath/deploy-log.txt", "Output: " . implode("\n", $output) . "\nResult: $result\n", FILE_APPEND);

// Optional: Display output for debugging (remove in production)
echo "<pre>" . implode("\n", $output) . "</pre>";

?>
