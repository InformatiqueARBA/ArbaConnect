<?php

namespace App\InventoryModule\Service;

class RequestOdbcInventoryService
{



    //Retourne les informations Rubis de localisation pour un inventaire donné
    public function getInventory(String $inventoryNumber): String
    {
        $sql = "
        SELECT distinct

             INV.INVDP AS WAREHOUSE -- Dépôt 
            ,left(INV.LOCAL,5) AS LOCATION -- 1ème Loc sur 5 caractères
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
        ORDER BY LOCATION
        ";
        return $sql;
    }

    public function getInventory2(String $inventoryNumber): String
    {
        $sql = "
        SELECT distinct

             INV.INVDP AS WAREHOUSE -- Dépôt 
            ,left(INV.LOCA2,5) AS LOCATION -- 2ème Loc sur 5 caractères 
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
        ORDER BY LOCATION
        ";
        return $sql;
    }

    public function getInventory3(String $inventoryNumber): String
    {
        $sql = "
        SELECT distinct

             INV.INVDP AS WAREHOUSE -- Dépôt  
            ,left(INV.LOCA3,5) AS LOCATION -- 3ème Loc sur 5 caractères
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
        ORDER BY LOCATION
        ";
        return $sql;
    }

    //Retourne les articles d'un inventaire donné
    public function getArticlesWithLocation(String $inventoryNumber): String
    {
        $sql = "
            SELECT distinct
                 INV.INVNO AS INVENTORY_NUMBER
                ,INV.INVDP AS WAREHOUSE 
                ,left(INV.LOCAL,5) AS LOCATION
                ,left(INV.LOCA2,5) AS LOCATION2 
                ,left(INV.LOCA3,5) AS LOCATION3
                ,TRIM(INV.INVAR) AS CODE_ARTICLE
                ,TRIM(INV.DESI1) AS DESIGNATION1
                ,TRIM(INV.DESI2) AS DESIGNATION2
                ,INV.HILOT AS CODE_LOT
                ,ART.TYDIM AS TYPE_DIMENSION
                ,ART.CONDI AS CONDITIONNEMENT
                ,TRIM(ART.ARTD4) AS LIBELLE_CONDI
                ,CAST(NULL AS DECIMAL(8)) AS QUANTITE_LOC1
                ,CAST(NULL AS DECIMAL(8)) AS QUANTITE_LOC2
                ,CAST(NULL AS DECIMAL(8)) AS QUANTITE_LOC3
                ,TRIM(ART.ULPRE) AS UNITE_PREPARATION
                ,CAST(NULL AS DECIMAL(8)) AS QUANTITE2_LOC1
                ,CAST(NULL AS DECIMAL(8)) AS QUANTITE2_LOC2
                ,CAST(NULL AS DECIMAL(8)) AS QUANTITE2_LOC3
                --,'' AS DEP AS DEPOT
                --,'' AS P_DEP
            FROM 
                AQAGESTCOM.AINVENP1 INV
            inner join
                AQAGESTCOM.AARTICP1 ART 
            on 
                INV.INVAR = ART.NOART
            WHERE
                ART.ARDIV <> 'OUI' and -- Hors articles divers
                INV.ETARE <> 'S' and -- Non suspendu
                INV.LOCAL <> '' and -- Hors localisation 1 vide
                INV.SERST = 'OUI' and -- Articles stockés uniquement (fiche article)
                INV.INVST = 'OUI' and -- Articles stockés uniquement (fiche stock)
                INV.INVNO= '$inventoryNumber'
                ";
        return $sql;
    }
}
