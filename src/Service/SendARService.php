<?php

namespace App\Service;

use Psr\Log\LoggerInterface;


class SendARService
{

    private $mailerService;
    private $logger;

    public function __construct(MailerService $mailerService, LoggerInterface $logger)
    {
        $this->mailerService = $mailerService;
        $this->logger = $logger;
    }


    public function sendAR($nobon, $formattedDate, $to)
    {
        $this->logger->info("Starting sendAR process for $nobon at $formattedDate");

        // Encodage des paramètres de l'URL
        $nobonEncoded = urlencode($nobon);
        $formattedDateEncoded = urlencode($formattedDate);

        // Construction de l'URL
        $url = "http://10.211.19.120/ar/edition_pdf_AR_ARBACONNECT.php?NOBON=$nobonEncoded&DATE=$formattedDateEncoded";

        try {
            // Utilisation de file_get_contents pour télécharger le fichier
            $pdfContent = file_get_contents($url);

            if ($pdfContent === false) {
                throw new \Exception("Failed to download PDF from $url.");
            }

            // Chemin où enregistrer le fichier PDF téléchargé (fichier supprimé après envoi)
            $pdfPath = "/var/www/ArbaConnect/public/pdf/ar/$nobon.pdf";

            // Chemin où le fichier PDF est sauvegardé pendant 90 jours TODO: créer script ou service pour supprimmer quand sup à 90jours
            $savePdfPath = "/home/dave/Documents/ArbaConnect/save/pdf/ar_dl/$nobon.pdf";




            // Enregistrement du fichier PDF
            if (file_put_contents($pdfPath, $pdfContent) === false) {
                throw new \Exception("Failed to save PDF to $pdfPath.");
            }

            // Enregistrement du fichier PDF
            if (file_put_contents($savePdfPath, $pdfContent) === false) {
                throw new \Exception("Failed to save PDF to $savePdfPath.");
            }


            $from = 'informatique@arba.coop';


            $subject = 'ARBA | votre AR de commande modifié';
            $html = '<p>Bonjour,<br><br>
                        Veuillez trouver ci-joint votre AR de commande modifié <br><br><p>';

            $attachmentPath = "/var/www/ArbaConnect/public/pdf/ar/$nobon.pdf";
            $this->mailerService->sendMailWithAttachment($from, $to, $subject, $html, $attachmentPath);
            $this->logger->info("Mail sent successfully to $to");
            // création d'un message flash pour avertir de la modification

            // Suppression du fichier PDF après l'envoi de l'email
            // if (file_exists($pdfPath)) {
            //     unlink($pdfPath);
            // }
        } catch (\Exception $e) {
            $this->logger->error("Error in sendAR process: " . $e->getMessage());
        }
    }




    // public function sendAR2($nobon, $to)
    // {


    //     // Encodage des paramètres de l'URL
    //     $nobonEncoded = urlencode($nobon);


    //     // Construction de l'URL
    //     $url = "http://10.211.19.120/ar/edition_pdf_AR.php?NOBON=$nobonEncoded";

    //     try {
    //         // Utilisation de file_get_contents pour télécharger le fichier
    //         $pdfContent = file_get_contents($url);

    //         if ($pdfContent === false) {
    //             throw new \Exception("Failed to download PDF from $url.");
    //         }

    //         // Chemin où enregistrer le fichier PDF téléchargé (fichier supprimé après envoi)
    //         $pdfPath = "/var/www/ArbaConnect/public/pdf/ar/$nobon.pdf";






    //         // Enregistrement du fichier PDF
    //         if (file_put_contents($pdfPath, $pdfContent) === false) {
    //             throw new \Exception("Failed to save PDF to $pdfPath.");
    //         }





    //         $from = 'informatique@arba.coop';
    //         // TODO: changer le destinataire il suffit de supprimer la ligne suivante
    //         // $to = 'boitedetestsam@gmail.com';
    //         $subject = 'ARBA ';
    //         $html = '<p>Bonjour,<br><br>
    //                     Veuillez trouver ci-joint votre AR de commande <br><br><p>';

    //         $attachmentPath = "/var/www/ArbaConnect/public/pdf/ar/$nobon.pdf";
    //         $this->mailerService->sendMailWithAttachment($from, $to, $subject, $html, $attachmentPath);

    //         if (file_exists($pdfPath)) {
    //             unlink($pdfPath);
    //         }
    //     } catch (\Exception $e) {
    //         $this->logger->error("Error in sendAR process: " . $e->getMessage());
    //     }
    // }
}
