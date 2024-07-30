<?php

namespace App\Service;

class RequestOdbcService
{



    //Retourne la liste des comptes nécessitant la création d'un accès à l'application
    // le CLIW00 est setté manuellement dans rubis pour les rôles supérieur à user
    public function getMembers(): String
    {
        $sql = "
        select 
            '016'||trim(NOCLI) id
            ,trim(NOCLI) corporationId
            ,case 
                when CLIW00 = '0' then 'root'
                when CLIW00 = '1' then 'admin'
                when CLIW00 = '2' then 'superUser'
                else 'user' end as profil
            ,case
                when PWDCL = '' then 'ARBA'
                else PWDCL end password
            ,trim(COMC1) mail
            ,COFIN firstName
            ,NOMCL lastName
        from
        AQAGESTCOM.ACLIENP1 CLI
        where
           CLDI1 in ('AD','SAL')
            --and NOCLI != 'FICTIF'
            and ETCLE != 'S'
        order by
            NOCLI
        ";
        return $sql;
    }






    // Retourne les commandes éligibles à la modification ADH
    public function getOrders(): String
    {
        $sql = "
       select distinct
            ENT_CMD.NOBON as ID 
            ,trim(CLI.NOCLI) as CORPORATIONID
            ,case
                when trim(ENT_CMD.ETSEE) = 'ANN' THEN 'canceled'
                when trim(ENT_CMD.ENT30) = 'P' THEN 'prepared' 
                when trim(ENT_CMD.ENT30) in ('R/P','R/F') THEN 'not editable' -- CMD partiellement préparée/facturée
                when trim(ENT_CMD.ENT30)= 'F' THEN 'delivred' 
                when ENT_CMD.ENPRM = 'AC' THEN 'edited' 
                else 'editable'end as ORDERSTATUS
            ,trim(ENT_CMD.RFCSB) as REFERENCE
            ,CONCAT(CONCAT(CONCAT(CONCAT(CONCAT(ENT_CMD.DSECS, ENT_CMD.DSECA), '-'),ENT_CMD.DSECM),'-'),ENT_CMD.DSECJ) as ORDERDATE
            ,CONCAT(CONCAT(CONCAT(CONCAT(CONCAT(ENT_CMD.DLSSB, ENT_CMD.DLasB), '-'),ENT_CMD.DLMSB),'-'),ENT_CMD.DLJSB) as DELIVERYDATE
            ,case 
                when ENT_CMD.ENTB40 in ('ORA','ORC') THEN 'Sur ordre'
				when TYVTE = 'LIV' THEN 'Livraison'
				when TYVTE = 'EMP' THEN 'À emporter'  
                else ENT_CMD.TYVTE END as TYPE
            ,case
            when ENT_CMD.LIVSB = 'EOL' then 'EOLAS'
            when ENT_CMD.LIVSB = 'DFE' then 'DFIWEB'
            else 'ARBA' end as SELLER
            ,trim(ENT_CMD.COMED) as COMMENT -- TODO: À supprimer/modifier ce champ sur la V2
        from
            AQAGESTCOM.AENTBOP1 ENT_CMD
        inner join
            AQAGESTCOM.ACLIENP1 CLI ON CLI.NOCLI = ENT_CMD.NOCLI
        inner join
            AQAGESTCOM.ADETBOP1 DET_CMD ON DET_CMD.NOBON = ENT_CMD.NOBON

        where /* Récupère les commandes non éditées */

            (
			CLDI1 = 'AD' -- ADH uniquement
            and ETCLE != 'S' -- ADH ACTIF
            and ETSBE != 'ANN' -- LIGNE ACTIVE
            and CODAR != '' -- Hors ligne commentaire
            and DTZAB != 'O' -- Hors ligne reprise/avoir
			and ETSEE != 'ANN' -- BON ACTIF
			and ENT30 like '%R%' -- Bon à préparer
			and DET27 ='R' -- Ligne à préparer
			and PREDI ='N' -- Bon préparation non édité
            )

        OR  /* Récupère les commandes en cours de moins de 3 mois non livrées pour statuts 'non modifiable', 'annulée', 'préparé' */

			(
			CLDI1 = 'AD' -- ADH uniquement
            and trim(ENT30) <> 'F'
            and ETCLE != 'S' -- ADH ACTIF
            and CODAR != '' -- Hors ligne commentaire
            and DTZAB != 'O' -- Hors ligne reprise/avoir
            and TIMESTAMP_FORMAT(CONCAT(CONCAT(CONCAT(DSECS, DSECA), DSECM), DSECJ), 'YYYYMMDD') >= CURRENT_DATE - 3 MONTHS
            and ENT_CMD.FACSB != 'OUI'
			)

	OR  /* Récupère les commandes livrées depuis 1 semaine ou moins /!\ NOBON='934807', certaines commandes sont manuellement passées en livrée sans BL associé */

			(
			CLDI1 = 'AD' -- ADH uniquement
            and  trim(ENT30) = 'F'
            and ETCLE != 'S' -- ADH ACTIF
            and CODAR != '' -- Hors ligne commentaire
            and DTZAB != 'O' -- Hors ligne reprise/avoir
            and BLVSB ='OUI'
            and TIMESTAMP_FORMAT(CONCAT(CONCAT(CONCAT(DLSSB, DLASB), DLMSB), DLJSB), 'YYYYMMDD') >= CURRENT_DATE -7 DAYS
			)

            ORDER by
            ID
        ";
        return $sql;
    }






    // Remonte les entreprises des comptes utilisateurs
    public function getCoporations(): String
    {
        $sql = "
    select 
	     trim(NOCLI) ID
	    ,trim(NOMCL) NAME
	    ,case
		    when SURCL = '001' then 1
		    else 0 end STATUS
    from
    AQAGESTCOM.ACLIENP1 CLI
    where
		CLDI1 in ('AD','SAL')
	    --and NOCLI != 'FICTIF'
	    and ETCLE != 'S'
    order by
        NOCLI
    ";
        return $sql;
    }




    //TODO: Prendre en compte les comptes supprimer pour gérer la suppression dans la base Sécurity (voir le souhait pour la gestion des comtpes sous surveillance)
    //Retourne la liste des comptes nécessitant la création d'un accès à l'application
    // le CLIW00 est setté manuellement dans rubis pour les rôles supérieur à user
    public function getUsers(): String
    {
        $sql = "
        select 
            '016'||trim(NOCLI) login
            ,trim(RENDI) mail
            ,trim(COMC1) mail_AR 
            ,trim(NOCLI) enterprise   
            ,'ROLE_USER' role         
            ,'0000' password
            ,trim(ETCLE) status
            ,trim(TOUCL) tour_code
        from
        AQAGESTCOM.ACLIENP1 CLI
        where
           CLDI1 in ('AD')
        order by
            NOCLI
           
        ";
        return $sql;
    }

    public function getTourCodes(): String
    {
        $sql = "
        select 
            trim(CTTOU) TOURCODE
            ,CONCAT(CONCAT(CONCAT(CONCAT(CONCAT(CTDLS,CTDLA), '-'),CTDLM),'-'),CTDLJ) as DELIVERYDATE
			,TIMESTAMP_FORMAT(CTDCS || CTDCA || CTDCM || CTDCJ || LPAD(CTHHC, 2, '0') || LPAD(CTMNC, 2, '0'),'YYYYMMDDHH24MI') as LIMITDATE
        from
            AQAGESTCOM.ACALTOP1
        where
            TIMESTAMP_FORMAT(CONCAT(CONCAT(CONCAT(CTDLS, CTDLA), CTDLM), CTDLJ), 'YYYYMMDD') > CURRENT_DATE
        ";
        return $sql;
    }

    /* Requête pour prendre en compte les heures limites de passation de commande 
        select 
            trim(CTTOU) TOURCODE
            ,CONCAT(CONCAT(CONCAT(CONCAT(CONCAT(CTDLS,CTDLA), '-'),CTDLM),'-'),CTDLJ) as DELIVERYDATE
			,TIMESTAMP_FORMAT(CTDCS || CTDCA || CTDCM || CTDCJ || LPAD(CTHHC, 2, '0') || LPAD(CTMNC, 2, '0'),'YYYYMMDDHH24MI') as LIMITDATE
        from
            AQAGESTCOM.ACALTOP1
        where
            TIMESTAMP_FORMAT(CONCAT(CONCAT(CONCAT(CTDLS, CTDLA), CTDLM), CTDLJ), 'YYYYMMDD') > CURRENT_DATE 
################################################## ANCIENNE REQUETE #################################################################
        select 
            trim(CTTOU) TOURCODE
            ,CONCAT(CONCAT(CONCAT(CONCAT(CONCAT(CTDLS,CTDLA), '-'),CTDLM),'-'),CTDLJ) as DELIVERYDATE
        from
        AQAGESTCOM.ACALTOP1
        where
             TIMESTAMP_FORMAT(CONCAT(CONCAT(CONCAT(CTDLS, CTDLA), CTDLM), CTDLJ), 'YYYYMMDD') > CURRENT_DATE + 2 DAYS
    
    */

    public function getInfosAdh(): String
    { //Récupérer les infos de la fiche client & de l'adresse ATEL : 
        $sql = "
        select 
           -- Infos Contact : (modifiable)
            TELCL telephone_fixe
           ,TLXCL telephone_portable
           ,RENDI mail_general
           ,PROFE mail_ar
           ,COMC1 mail_bl
           ,CLIL05 site_web

           -- Infos siège social : (non modifiable)
           ,AD1CL adresse1
           ,AD2CL adresse2
           ,RUECL adresse3
           ,CPCLF code_postal
           ,BURCL ville

           -- Infos atelier : (modifiable)
           ,AD1LV adresse1
           ,AD2LV adresse2
           ,RUELV adresse3
           ,CPOLV code_postal
           ,BURLV ville

           -- Infos accessibilité : (modifiable)
           ,VILLV

        from
            AQAGESTCOM.ACLIENP1
        inner join 
            AQAGESTCOM.ALIVADP1
        on ALIVADP1.NOCLI = ALIENP1.NOCLI
        where
            NOLIV='ATEL'
            -- ajout de filtre selon utilisation fonction     
        ";
        return $sql;
    }
}
