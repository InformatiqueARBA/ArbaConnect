<?php

namespace App\ArbaConnect\Service;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/*service qui envoie les csv sur le QDLS Rubis*/

class CsvToRubisService
{
    private  $ERPDirDev;
    // private  $ERPDirProdAC;
    private  $params;

    public function __construct($ERPDirDev, ParameterBagInterface $params)
    {
        $this->ERPDirDev = $ERPDirDev;
        // $this->ERPDirProdAC = $ERPDirProdAC;
        $this->params = $params;
    }

    public function sendCsvToRubis($filePath, $fileName, $ERPDirProd)
    {

        // Se connecter au serveur FTP
        $ftp_server = "10.211.200.1";
        $ftp_username = "AQADMIN";
        $ftp_password = "ARBA";

        $ftp_conn = null;

        try {
            $ftp_conn = ftp_connect($ftp_server);
            if (!$ftp_conn) {
                throw new Exception("Impossible de se connecter au serveur FTP");
            }

            if (!ftp_login($ftp_conn, $ftp_username, $ftp_password)) {
                throw new Exception("Connexion FTP échouée");
            }

            ftp_pasv($ftp_conn, true);



            // envoi dans le dossier ERP prod ou test en fonction de l'environnement de l'Appp
            if ($this->params->get('kernel.environment') === 'dev') {
                $remote_directory =  $this->ERPDirDev;
            } else {
                $remote_directory =  $ERPDirProd;
            }


            $remote_file = $remote_directory . $fileName;

            if (ftp_put($ftp_conn, $remote_file, $filePath . $fileName, FTP_ASCII)) {
                echo "successfully uploaded $fileName\n";
            } else {
                echo "There was a problem while uploading $fileName\n";
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        } finally {
            if ($ftp_conn) {
                ftp_close($ftp_conn);
            }
        }
    }
}
