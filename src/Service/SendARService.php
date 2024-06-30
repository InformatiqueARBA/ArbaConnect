<?php

namespace App\Service;

class SendARService
{
    private $mailerService;

    public function __construct(MailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }



    public function sendAR($nobon, $formattedDate)
    {

        // Encodage des paramètres de l'URL
        $nobonEncoded = urlencode($nobon);
        $formattedDateEncoded = urlencode($formattedDate);

        // Construction de l'URL
        // dd("http://10.211.19.120/ar/edition_pdf_AR_ARBACONNECT.php?NOBON=$nobonEncoded&DATE=$formattedDateEncoded");
        $url = "http://10.211.19.120/ar/edition_pdf_AR_ARBACONNECT.php?NOBON=$nobonEncoded&DATE=$formattedDateEncoded";

        try {
            // Utilisation de file_get_contents pour télécharger le fichier
            $pdfContent = file_get_contents($url);

            if ($pdfContent === false) {
                throw new \Exception("Failed to download PDF from $url.");
            }

            // Chemin où enregistrer le fichier PDF téléchargé
            $pdfPath = "/var/www/ArbaConnect/public/pdf/ar/$nobon.pdf";

            // Enregistrement du fichier PDF
            if (file_put_contents($pdfPath, $pdfContent) === false) {
                throw new \Exception("Failed to save PDF to $pdfPath.");
            }



            $from = 'informatique@arba.coop';
            $to = 'kchartier@arba.coop';
            $subject = 'ARBA | votre AR de commande modifié';
            $html = '<p>Bonjour,<br><br>
                        Veuillez trouver ci-joint votre AR de commande modifié <br><br><p>';

            $attachmentPath = "/var/www/ArbaConnect/public/pdf/ar/$nobon.pdf";
            $this->mailerService->sendMailWithAttachment($from, $to, $subject, $html, $attachmentPath);
            // création d'un message flash pour avertir de la modification

        } catch (\Exception $e) {
            // Gestion des erreurs

        }
    }
}
