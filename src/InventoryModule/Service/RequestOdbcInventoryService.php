<?php

namespace App\InventoryModule\Service;

class RequestOdbcInventoryService
{



    //Retourne les informations de l'inventaire présent dans Rubis
    public function getInventory(String $inventoryNumber): String
    {
        $sql = "
        SELECT distinct
            
            INV.INVDP AS WAREHOUSE -- Dépôt 
            ,left(INV.LOCAL,5) AS LOCATION -- 5 1er caractères localisation
            ,CAST(NULL AS VARCHAR(50)) AS REFERENT
            ,0 AS STATUS
            ,INV.INVNO AS INVENTORY_NUMBER -- N° d'inventaire

        FROM 
            AQAGESTCOM.AINVENP1 INV
        
        WHERE
            ETARE <> 'S' and 
            INVST='OUI' and
            INV.LOCAL <> ' ' and  
            INV.INVNO= '$inventoryNumber'
        ";
        return $sql;
    }

    //Retourne les articles liés à une localisation
    public function getArticlesByInventoryNumber(): String
    {
        $sql = "
            
        ";
        return $sql;
    }


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

        limit 1
        ";
        return $sql;
    }
}
