<?php

declare(strict_types=1);

namespace App\Util;

use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bridge\Twig\Mime\WrappedTemplatedEmail;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailSender
{
    private string $mailerDsn;
    private string $mailerFromMail;
    private string $subject;
    private string $send_to;
    
    private Environment $twig;
    private LoggerInterface $logger;

    public function __construct(Environment $twig, LoggerInterface $logger)
    {
        $this->mailerDsn = $_ENV['MAILER_DSN'];
        $this->mailerFromMail = $_ENV['MAILER_FROM_MAIL'];
        $this->mailerFromName = $_ENV['MAILER_FROM_NAME'];
        $this->twig = $twig;
        $this->logger = $logger;
    }

    public function sendHtml(string $email, string $html, string $subject, ?string $text = null, ?string $from = null)
    {
        $fromAddress = $from ? new Address($this->mailerFromMail, $this->mailerFromName) : new Address($this->mailerFromMail);
        $this->send_to = $email;
        $this->subject = $subject;

        $message = (new Email())
            ->from($fromAddress)
            ->to($this->send_to)
            ->subject($this->subject)
            ->html($html);
        if (!empty($text)) {
            $message->text($text);
        }
        $this->send($message);
    }

    public function sendTwig(string $email, string $template, string $subject, array $context, string $from = null, string $reply = null)
    {
        $fromAddress = $from ? new Address($this->mailerFromMail, $this->mailerFromName) : new Address($this->mailerFromMail);
        $this->send_to = $email;
        $this->subject = $subject;

        $message = (new TemplatedEmail())
            ->from($fromAddress)
            ->to($this->send_to)
            ->subject($this->subject);

        if (!empty($reply)) {
            $message->replyTo($reply);
        }

        $context['email'] = new WrappedTemplatedEmail($this->twig, $message);
        $message->html($this->twig->render($template, $context));

        $this->send($message);
    }

    private function send(Email $message)
    {
        $transport = Transport::fromDsn($this->mailerDsn);
        $mailer = new Mailer($transport);

        $mailer->send($message);

        $this->logger->info('MailSender - email sent (subject: '.$message->getSubject().', to: '.$message->getTo()[0]->getAddress().', from: '.$message->getFrom()[0]->getAddress().')');
    }
}
