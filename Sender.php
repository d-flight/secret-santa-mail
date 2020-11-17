<?php

declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Sender {
    private PHPMailer $mailer;
    private string $template;

    public function __construct(PHPMailer $mailer, string $template)
    {
        $this->mailer = $mailer;
        $this->template = $template;
    }

    public function send(string ...$everybody): void
    {
        self::sending();
        
        foreach ($this->generateRandomPairs(...$everybody) as $recipient => $target) {
            $this->sendMail($recipient, $target);
        }

        self::goodbye();
    }

    /** @return array<string, string> */
    private function generateRandomPairs(string ...$addresses): array
    {
        $map = [];
        $recipients = $addresses;
        
        foreach ($addresses as $sender) {
            
            do {
                $random = \mt_rand(0, \count($recipients) -1);
                $recipient = $recipients[$random] ?? null;
    
                // if we have only one recipient left and it's the same as the
                // last address, start from scratch
                if ($recipient === $sender && 1 === \count($recipients)) {
                    return $this->generateRandomPairs(...$addresses);
                }
    
            } while (null === $recipient || $recipient == $sender);
    
            $map[$sender] = $recipient;
            \array_splice($recipients, $random, 1);
        }
    
        return $map;
    }

    private function sendMail(string $recipient, string $target): void
    {
        $mail = clone $this->mailer;

        [$recipientName, $recipientMail] = self::splitAddress($recipient);
        $mail->addAddress($recipientMail, $recipientName);
    
        // replace placeholders
        [$targetName, $targetMail] = self::splitAddress($target);
        $text = $this->template;
        $text = \str_replace('%name%', $recipientName, $text);
        $text = \str_replace('%target%', "$targetName, $targetMail", $text);
        $mail->Body = $text;
    
        $mail->send();
    }

    /**
     * Split an address <Name> name@mail.tld
     * Into name and address
     * 
     * @return array<string> ['Name', 'name@mail.tld']
     */
    private static function splitAddress(string $address): array
    {
        \preg_match('/\<([^>]+)\>\s*(.*)/i', $address, $matches);
        
        return [
            $matches[1],
            $matches[2]
        ];
    }

    private static function sending(): void
    {
        echo PHP_EOL . "Sending secret santa mails... ";
    }

    private static function goodbye(): void
    {
        echo "DONE.";
    }
}