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
            and NOCLI != 'FICTIF'
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
                when ENT_CMD.ENPRM = 'AC' THEN 'edited' 
                else 'editable'end as ORDERSTATUS
            ,trim(ENT_CMD.RFCSB) as REFERENCE
            ,CONCAT(CONCAT(CONCAT(CONCAT(CONCAT(ENT_CMD.DSECS, ENT_CMD.DSECA), '-'),ENT_CMD.DSECM),'-'),ENT_CMD.DSECJ) as ORDERDATE
            ,CONCAT(CONCAT(CONCAT(CONCAT(CONCAT(ENT_CMD.DLSSB, ENT_CMD.DLasB), '-'),ENT_CMD.DLMSB),'-'),ENT_CMD.DLJSB) as DELIVERYDATE
            ,case 
                when ENT_CMD.ENTB40 = 'ORA' THEN ENT_CMD.ENTB40 
                else ENT_CMD.TYVTE END as TYPE
            ,ENT_CMD.LIVSB as SELLER
            ,trim(ENT_CMD.COMED) as COMMENT 

        from
            AQAGESTCOM.AENTBOP1 ENT_CMD
        inner join
            AQAGESTCOM.ACLIENP1 CLI ON CLI.NOCLI = ENT_CMD.NOCLI
        inner join
            AQAGESTCOM.ADETBOP1 DET_CMD ON DET_CMD.NOBON = ENT_CMD.NOBON

        where
			CLDI1 = 'AD' -- ADH uniquement
            and CLI.NOCLI != 'FICTIF'
			and SURCL ='000' -- Client normal
            and ETCLE != 'S' -- ADH ACTIF
			and ETSEE != 'ANN' -- BON ACTIF
			and ENT30 like '%R%' -- Bon à préparer
			and ETSBE != 'ANN' -- LIGNE ACTIVE
            and CODAR != '' -- Hors ligne commentaire
			and DET27 ='R' -- Ligne à préparer
			and PREDI = 'N' -- Bon préparation non édité
			and DTZAB != 'O' -- Hors ligne reprise/avoir
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
	    and NOCLI != 'FICTIF'
	    and ETCLE != 'S'
    order by
        NOCLI
    ";
        return $sql;
    }
}
