<?php

namespace App\Service;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Twig\Environment;

class MailerService
{
    public function __construct(
        private ContainerBagInterface $params,
        private MailerInterface $mailer,
        private Environment $twig,
    ) {}

    public function send(array|string $to, string $subject, string $content, ?string $from = null, array|string $cc = [], array|string $bcc = []): bool
    {
        $from = empty($from) ? $this->params->get('mail_from_default') : $from;
        $tos = is_array($to) ? $to : [$to];
        $ccs = is_array($cc) ? $cc : [$cc];
        $bccs = is_array($bcc) ? $bcc : [$bcc];

        try {
            foreach ($tos as $t) {
                $email = (new TemplatedEmail())
                    ->to($t)
                    ->from($from)
                    ->subject($subject)
                    ->htmlTemplate('emails/default.html.twig')
                     ->context(['subject' => $subject, 'content' => $content]);
                if ($ccs && $ccs[0] !== '')  { $email->cc(...$ccs); }
                if ($bccs && $bccs[0] !== '') { $email->bcc(...$bccs); }

                $this->mailer->send($email);
            }

            return true;
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
            return false;
        }
    }
}
