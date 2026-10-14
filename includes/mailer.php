<?php

function send_email($to, $subject, $body) {

    $log_entry = "[" . date('Y-m-d H:i:s') . "] Sent to: $to | Subject: $subject | Body: $body\n";
    file_put_contents(__DIR__ . '/../mail_log.txt', $log_entry, FILE_APPEND);
    return true;
}
?>
