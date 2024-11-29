<?php

namespace App\DeliveryDateModule\Service;

class RequestOdbcDeliveryDateService
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
                when ENT_CMD.ENTB40 = 'ORC' THEN 'not editable' -- La cmd ORA a reçu une date de liv. donc plus modifiable
                when ENT_CMD.ENTB37 = 'IMP' THEN 'not editable' -- Qualifiant date de livraison / commande impérative
                when trim(ENT_CMD.ENT30)= 'F' THEN 'delivred' 
                when ENT_CMD.ENPRM = 'AC' THEN 'edited' 
                else 'editable'end as ORDERSTATUS
            ,trim(ENT_CMD.RFCSB) as REFERENCE
            ,CONCAT(CONCAT(CONCAT(CONCAT(CONCAT(ENT_CMD.DSECS, ENT_CMD.DSECA), '-'),ENT_CMD.DSECM),'-'),ENT_CMD.DSECJ) as ORDERDATE
            ,CONCAT(CONCAT(CONCAT(CONCAT(CONCAT(ENT_CMD.DLSSB, ENT_CMD.DLasB), '-'),ENT_CMD.DLMSB),'-'),ENT_CMD.DLJSB) as DELIVERYDATE
            ,case 
                when ENT_CMD.ENTB37 = 'IMP' THEN 'Commande impérative'
                when ENT_CMD.ENTB40 = 'ORA' THEN 'Sur ordre'
                when ENT_CMD.ENTB40 = 'ORC' THEN 'Sur ordre déclenché'
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
            and ETSEE != 'ANN' -- BON INACTIF
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

    OR  /* Récupère les commandes annulées depuis 1 semaine ou moins */

			(
			CLDI1 = 'AD' -- ADH uniquement
            and ETCLE != 'S' -- ADH ACTIF
            and CODAR != '' -- Hors ligne commentaire
            and ETSEE = 'ANN' -- BON INACTIF
            and TIMESTAMP_FORMAT(CONCAT(CONCAT(CONCAT(DSECS, DSECA), DSECM), DSECJ), 'YYYYMMDD') >= CURRENT_DATE -7 DAYS
			)

            ORDER by
            CORPORATIONID
        ";
        return $sql;
    }




    // Retourne les lignes des commandes éligibles à la modification ADH
    public function getDetailOrders(): String
    {
        $sql = "
            with
    RankedData as (
        select
            trim(DET_CMD.NOBON) NUM_BON,
            trim(DET_CMD.NOCLI) NUM_CLI,
            trim(DET_CMD.NOLIG) NUM_LIG,
            trim(DET_CMD.CODAR) NUM_ART,
            trim(DET_CMD.DS1DB) || trim(DET_CMD.DS2DB) DESI,
            trim(DET_CMD.QTESA) QTE,
            trim(DET_CMD.LUSTO) UNI,
            trim(DET_CMD.NBOFO) NUM_BON_FOU,
            trim(DET_FOU.CFCOD) CONF_FOU,
            trim(DET_CMD.DET27) TYP_LIG,
            DET_CMD.DSBCS || DET_CMD.DSBCA || '-' || DET_CMD.DSBCM || '-' || DET_CMD.DSBCJ DATE_COMMANDE,
            DET_FOU.CFDLS || DET_FOU.CFDLA || '-' || DET_FOU.CFDLM || '-' || DET_FOU.CFDLJ DATE_RECEPTION,
            row_number() over (
                partition by
                    DET_CMD.NOBON,
                    DET_CMD.NOCLI,
                    DET_CMD.NOLIG,
                    DET_CMD.CODAR
                order by
                    case
                        when DET_FOU.CFDLS is not null then to_date(
                            DET_FOU.CFDLS || DET_FOU.CFDLA || DET_FOU.CFDLM || DET_FOU.CFDLJ,
                            'YYYYMMDD'
                        )
                        else null
                    end desc nulls LAST
            ) as RowNum
        from
            AQAGESTCOM.ADETBOP1 DET_CMD
            inner join AQAGESTCOM.AENTBOP1 ENT_CMD on ENT_CMD.NOBON = DET_CMD.NOBON
            inner join AQAGESTCOM.ACLIENP1 CLI on CLI.NOCLI = DET_CMD.NOCLI
            left outer join AQAGESTCOM.ACFDETP1 DET_FOU on DET_FOU.CFBON = DET_CMD.NBOFO
        where
            /* Clause identique à celle des entêtes de commande  */
            (
                CLDI1 = 'AD' -- ADH uniquement
                and ETCLE != 'S' -- ADH ACTIF
                and DET_CMD.ETSBE != 'ANN' -- LIGNE ACTIVE
                and DET_CMD.CODAR != '' -- Hors ligne commentaire
                and DET_CMD.DTZAB != 'O' -- Hors ligne reprise/avoir
                and ENT_CMD.ETSEE != 'ANN' -- BON ACTIF
                and ENT30 like '%R%' -- Bon à préparer
                and DET27 = 'R' -- Ligne à préparer
                and PREDI = 'N' -- Bon préparation non édité
            )
            or /* Récupère les commandes en cours de moins de 3 mois non livrées pour statuts 'non modifiable', 'annulée', 'préparé' */ (
                CLDI1 = 'AD' -- ADH uniquement
                and trim(ENT30) <> 'F'
                and ETCLE != 'S' -- ADH ACTIF
                and ENT_CMD.ETSEE != 'ANN' -- BON INACTIF
                and CODAR != '' -- Hors ligne commentaire
                and DTZAB != 'O' -- Hors ligne reprise/avoir
                and timestamp_format(
                    concat(concat(concat(DSECS, DSECA), DSECM), DSECJ),
                    'YYYYMMDD'
                ) >= current_date - 3 months
                and ENT_CMD.FACSB != 'OUI'
            )
            or /* Récupère les commandes livrées depuis 1 semaine ou moins /!\ NOBON='934807', certaines commandes sont manuellement passées en livrée sans BL associé */ (
                CLDI1 = 'AD' -- ADH uniquement
                and trim(ENT30) = 'F'
                and ETCLE != 'S' -- ADH ACTIF
                and CODAR != '' -- Hors ligne commentaire
                and DTZAB != 'O' -- Hors ligne reprise/avoir
                and BLVSB = 'OUI'
                and timestamp_format(
                    concat(concat(concat(DLSSB, DLASB), DLMSB), DLJSB),
                    'YYYYMMDD'
                ) >= current_date -7 days
            )
            or /* Récupère les commandes annulées depuis 1 semaine ou moins */ (
                CLDI1 = 'AD' -- ADH uniquement
                and ETCLE != 'S' -- ADH ACTIF
                and CODAR != '' -- Hors ligne commentaire
                and ENT_CMD.ETSEE = 'ANN' -- BON INACTIF
                and timestamp_format(
                    concat(concat(concat(DSECS, DSECA), DSECM), DSECJ),
                    'YYYYMMDD'
                ) >= current_date -7 days
            )
     )
        select distinct
            NUM_BON,
            NUM_CLI,
            NUM_LIG,
            NUM_ART,
            DESI,
            QTE,
            UNI,
            NUM_BON_FOU,
            CONF_FOU,
            TYP_LIG,
            DATE_COMMANDE,
            DATE_RECEPTION
        from
            RankedData
        where
            RowNum = 1
    ";
        return $sql;
    }
    // public function getDetailOrders(): String
    // {
    //     $sql = "
    // -- Retourne les lignes de commandes client et la notion fournisseur confirmée
    // SELECT DISTINCT
    //      trim(DET_CMD.NOBON) NUM_BON
    //     ,trim(DET_CMD.NOCLI) NUM_CLI
    //     ,trim(DET_CMD.NOLIG) NUM_LIG
    //     ,trim(DET_CMD.CODAR) NUM_ART
    //     ,trim(DET_CMD.DS1DB) || trim(DET_CMD.DS2DB) DESI
    //     ,trim(DET_CMD.QTESA) QTE
    //     ,trim(DET_CMD.LUSTO) UNI
    //     ,trim(DET_CMD.NBOFO) NUM_BON_FOU
    //     ,trim(DET_FOU.CFCOD) CONF_FOU
    //     ,trim(DET_CMD.DET27) TYP_LIG
    //     ,DET_CMD.DSBCS ||DET_CMD.DSBCA ||'-'||DET_CMD.DSBCM ||'-'|| DET_CMD.DSBCJ DATE_COMMANDE 
    //     ,DET_FOU.CFDLS ||DET_FOU.CFDLA  ||'-'||DET_FOU.CFDLM ||'-'|| DET_FOU.CFDLJ DATE_RECEPTION
    // FROM
    //     AQAGESTCOM.ADETBOP1 DET_CMD
    // INNER JOIN
    //     AQAGESTCOM.AENTBOP1 ENT_CMD
    // ON
    //     ENT_CMD.NOBON = DET_CMD.NOBON
    // INNER JOIN
    //     AQAGESTCOM.ACLIENP1 CLI
    // ON
    //     CLI.NOCLI = DET_CMD.NOCLI
    // LEFT OUTER JOIN
    //     AQAGESTCOM.ACFDETP1 DET_FOU
    // ON
    //     DET_FOU.CFBON = DET_CMD.NBOFO
    // AND
    //     DET_FOU.CFCLL = DET_CMD.NOLIG

    //  where /* Clause identique à celle des entêtes de commande  */

    //             (
    //             CLDI1 = 'AD' -- ADH uniquement
    //             and ETCLE != 'S' -- ADH ACTIF
    //             and ETSBE != 'ANN' -- LIGNE ACTIVE
    //             and CODAR != '' -- Hors ligne commentaire
    //             and DTZAB != 'O' -- Hors ligne reprise/avoir
    //             and ETSEE != 'ANN' -- BON ACTIF
    //             and ENT30 like '%R%' -- Bon à préparer
    //             and DET27 ='R' -- Ligne à préparer
    //             and PREDI ='N' -- Bon préparation non édité
    //             )

    //         OR  /* Récupère les commandes en cours de moins de 3 mois non livrées pour statuts 'non modifiable', 'annulée', 'préparé' */

    //             (
    //             CLDI1 = 'AD' -- ADH uniquement
    //             and trim(ENT30) <> 'F'
    //             and ETCLE != 'S' -- ADH ACTIF
    //             and ETSEE != 'ANN' -- BON INACTIF
    //             and CODAR != '' -- Hors ligne commentaire
    //             and DTZAB != 'O' -- Hors ligne reprise/avoir
    //             and TIMESTAMP_FORMAT(CONCAT(CONCAT(CONCAT(DSECS, DSECA), DSECM), DSECJ), 'YYYYMMDD') >= CURRENT_DATE - 3 MONTHS
    //             and ENT_CMD.FACSB != 'OUI'
    //             )

    //         OR  /* Récupère les commandes livrées depuis 1 semaine ou moins /!\ NOBON='934807', certaines commandes sont manuellement passées en livrée sans BL associé */

    //             (
    //             CLDI1 = 'AD' -- ADH uniquement
    //             and  trim(ENT30) = 'F'
    //             and ETCLE != 'S' -- ADH ACTIF
    //             and CODAR != '' -- Hors ligne commentaire
    //             and DTZAB != 'O' -- Hors ligne reprise/avoir
    //             and BLVSB ='OUI'
    //             and TIMESTAMP_FORMAT(CONCAT(CONCAT(CONCAT(DLSSB, DLASB), DLMSB), DLJSB), 'YYYYMMDD') >= CURRENT_DATE -7 DAYS
    //             )

    //     OR  /* Récupère les commandes annulées depuis 1 semaine ou moins */

    //             (
    //             CLDI1 = 'AD' -- ADH uniquement
    //             and ETCLE != 'S' -- ADH ACTIF
    //             and CODAR != '' -- Hors ligne commentaire
    //             and ETSEE = 'ANN' -- BON INACTIF
    //             and TIMESTAMP_FORMAT(CONCAT(CONCAT(CONCAT(DSECS, DSECA), DSECM), DSECJ), 'YYYYMMDD') >= CURRENT_DATE -7 DAYS
    //             )      
    // ";
    //     return $sql;
    // }




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
