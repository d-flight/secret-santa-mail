<?php

declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require_once realpath(__DIR__ . '/vendor/autoload.php');
require_once realpath(__DIR__ . '/Sender.php');

$mailer = new PHPMailer(true);

//Server settings
// $mailer->SMTPDebug = SMTP::DEBUG_SERVER;                   // Enable verbose debug output
$mailer->isSMTP();                                            // Send using SMTP
$mailer->Host       = '';                                     // Set the SMTP server to send through
$mailer->SMTPAuth   = true;                                   // Enable SMTP authentication
$mailer->Username   = '';                                     // SMTP username
$mailer->Password   = '';                                     // SMTP password
$mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
$mailer->Port       = 465;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

// Mail settings
$mailer->isHTML(false);
$mailer->setFrom('j.doe@gmail.com', 'John Doe');
$mailer->Subject = 'Secret Santa via Mail';

// The message you want people to receive.
// Variables that are being replaced:
// %name% will be replaced with the full name of the recipient. For "<John Doe> j.doe@gmail.com" that would be "John Doe"
// %target will be replaced with the target the recipient has for secret santa. For "<John Doe> j.doe@gmail.com" that would be "John Doe, j.doe@gmail.com"
$template = <<<EOL
Hi %name%,

you're secret santa for: %target%

Best, John
Also: this mail has been sent automatically.
EOL;

// Everybody that takes part in secret santa
$everybody = [
    // '<Mason Johnston> m.johnston@gmail.com',
    // '<Cody Dawson> cody.dawson@hotmail.com',
    // '<Bethany Davies> daviesbeth@icloud.com',
];

(new Sender($mailer, $template))->send(...$everybody);