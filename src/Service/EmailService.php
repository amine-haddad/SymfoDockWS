<?php
// src/Service/EmailService.php
namespace App\Service;

use App\Entity\Episode;
use App\Entity\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class EmailService extends AbstractController
{
    private $mailer;
    private $twig;

    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendNewProgramEmail(Program $program)
    {
        $email = (new Email())
            ->from($this->getParameter('mailer_from'))
            ->to('recipient@example.com')
            ->subject('Une nouvelle Série vient d\'être publiée  !')
            ->html($this->renderView('program/newProgramEmail.inky.twig', ['program' => $program]));

        $this->mailer->send($email);
    }

    public function sendNewEpisodeEmail(Episode $episode)
    {
        $email = (new Email())
            ->from($this->getParameter('mailer_from'))
            ->to('recipient@example.com')
            ->subject('Un nouvelle épisode vient d\'être publiée  !')
            ->html($this->renderView('episode/newEpisodeEmail.inky.twig', ['episode' => $episode]));

        $this->mailer->send($email);
    }
}