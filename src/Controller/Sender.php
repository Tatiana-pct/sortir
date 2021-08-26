<?php

namespace App\Controller;

use App\Entity\Participant;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class Sender
{

    /**
     * @var MailerInterface
     */
    protected $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendNewUserNotificationToAdmin(Participant $user):void
    {
        //file_put_contents('debug.txt', $user->getEmail());
        $email = new Email();
        $email->from('account@Sortir.com')
                ->to('admin@Sortir.com')
                ->subject('New account created on SortirV2.com')
                ->html('<h1> New Account !</h1>email : ' .$user->getEmail());

        $this->mailer->send($email);
    }

}