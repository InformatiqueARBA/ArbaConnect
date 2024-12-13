<?php

namespace App\InventoryModule\Service;

class RequestOdbcInventoryService
{
    // Retourne les allées de l'inventaire Rubis
    public function getUniqueInventoryLocations(String $inventoryNumber): String
    {
        $sql = "
        SELECT DISTINCT
             INV.INVDP AS WAREHOUSE -- Dépôt 
            ,CASE
                WHEN LEFT(INV.LOCAL, 5) = '' THEN CAST(NULL AS VARCHAR(12))
                ELSE LEFT(INV.LOCAL, 5) END AS LOCATION -- 1ère Loc sur 5 caractères
            ,CAST(NULL AS VARCHAR(50)) AS REFERENT
            ,0 AS STATUS
            ,INV.INVNO AS INVENTORY_NUMBER -- N° d'inventaire
        FROM 
            AQAGESTCOM.AINVENP1 INV
        WHERE
            ETARE <> 'S' AND 
            INVST = 'OUI' AND 
            INV.LOCAL <> ' ' AND  
            INV.INVNO = '$inventoryNumber'
    
        UNION
    
        SELECT DISTINCT
             INV.INVDP AS WAREHOUSE -- Dépôt 
            ,CASE
                WHEN LEFT(INV.LOCA2, 5) = '' THEN CAST(NULL AS VARCHAR(12))
                ELSE LEFT(INV.LOCA2, 5) END AS LOCATION -- 2ème Loc sur 5 caractères 
            ,CAST(NULL AS VARCHAR(50)) AS REFERENT
            ,0 AS STATUS
            ,INV.INVNO AS INVENTORY_NUMBER -- N° d'inventaire
        FROM 
            AQAGESTCOM.AINVENP1 INV
        WHERE
            ETARE <> 'S' AND 
            INVST = 'OUI' AND 
            INV.LOCAL <> ' ' AND  
            INV.INVNO = '$inventoryNumber'
    
        UNION
    
        SELECT DISTINCT
             INV.INVDP AS WAREHOUSE -- Dépôt  
            ,CASE
                WHEN LEFT(INV.LOCA3, 5) = '' THEN CAST(NULL AS VARCHAR(12))
                ELSE LEFT(INV.LOCA3, 5) END AS LOCATION -- 3ème Loc sur 5 caractères
            ,CAST(NULL AS VARCHAR(50)) AS REFERENT
            ,0 AS STATUS
            ,INV.INVNO AS INVENTORY_NUMBER -- N° d'inventaire
        FROM 
            AQAGESTCOM.AINVENP1 INV
        WHERE
            ETARE <> 'S' AND 
            INVST = 'OUI' AND 
            INV.LOCAL <> ' ' AND  
            INV.INVNO = '$inventoryNumber'
    
        ORDER BY LOCATION
        ";

        return $sql;
    }



    //     //Retourne les articles d'un inventaire donné
    //     public function getArticlesWithLocation(String $inventoryNumber): String
    //     {
    //         $sql = "
    //             select distinct
    //     INV.INVNO as INVENTORY_NUMBER,
    //     INV.INVDP as WAREHOUSE,
    //     case
    //         when trim(INV.LOCAL) = '' then cast(null as varchar(12))
    //         else trim(INV.LOCAL)
    //     end as location,
    //     case
    //         when trim(INV.LOCA2) = '' then cast(null as varchar(12))
    //         else trim(INV.LOCA2)
    //     end as LOCATION2,
    //     case
    //         when trim(INV.LOCA3) = '' then cast(null as varchar(12))
    //         else trim(INV.LOCA3)
    //     end as LOCATION3,
    //     trim(INV.INVAR) as CODE_ARTICLE,
    //     trim(INV.DESI1) as DESIGNATION1,
    //     trim(INV.DESI2) as DESIGNATION2,
    //     trim(INVLO.INLLO) as CODE_LOT,
    //     trim(ART.TYDIM) as TYPE_DIMENSION,
    //     trim(ART.CONDI) as CONDITIONNEMENT,
    //     case
    //         when (UNI.COD15 <> '')
    //         and ART.CDCON = 'NON' then trim(ART.ARTD4)
    //         else trim(ART.ULPRE)
    //     end as LIBELLE_CONDI,
    //     cast(null as decimal(8)) as QUANTITE_LOC1,
    //     cast(null as decimal(8)) as QUANTITE_LOC2,
    //     cast(null as decimal(8)) as QUANTITE_LOC3,
    //     trim(ART.ULPRE) as UNITE_PREPARATION,
    //     cast(null as decimal(8)) as QUANTITE2_LOC1,
    //     cast(null as decimal(8)) as QUANTITE2_LOC2,
    //     cast(null as decimal(8)) as QUANTITE2_LOC3,
    //     case
    //         when trim(ART.CDCON) = 'NON' then 0
    //         when trim(ART.CDCON) = '' then 0
    //         when trim(ART.CDCON) = 'OUI' then 1
    //         else 1
    //     end as DIVISIBLE,
    //     trim(ART.ART32) as TYPE_ARTICLE,
    //     ART.SERST as SERVED_FROM_STOCK
    // from
    //     AQAGESTCOM.AARTICP1 ART
    //     inner join AQAGESTCOM.AINVENP1 INV on ART.NOART = INV.INVAR
    //     left outer join AQAGESTCOM.ASTOLOP1 LOT on LOT.LOART = ART.NOART
    //     left outer join AQAGESTCOM.AINVLOP1 INVLO on ART.NOART = INVLO.INLAR
    //     left outer join AQAGESTCOM.ATAB15P1 UNI on ART.ARTD4 = UNI.LIRPR
    //     and UNI.TYPPR = 'UNI'
    // where
    // (   
    //     ART.ART32 = 'ART'
    //     and
    //     ART.ARDIV <> 'OUI'
    //     and -- Hors articles divers
    //     ART.ETARE <> 'S'
    //     and -- Non suspendu
    //     INV.LOCAL <> ''
    //     and 
    //     INV.INVNO = '$inventoryNumber'
    // )

    //  or 

    // (    
    //     ART.ART32 = 'LOV'
    //     and
    //     ART.ARDIV <> 'OUI'
    //     and -- Hors articles divers
    //     ART.ETARE <> 'S'
    //     and -- Non suspendu
    //     INV.LOCAL <> ''
    //     and 
    //     LOT.LOLOT = INVLO.INLLO
    //     and 
    //     LOINV > 0.000
    //     and 
    //     INV.INVNO = '$inventoryNumber'
    // )
    // order by
    //     location,
    //     LOCATION2,
    //     LOCATION3
    //      ";
    //         return $sql;
    //     }

    //Retourne les articles d'un inventaire donné
    public function getArticlesWithLocation(String $inventoryNumber): String
    {
        $sql = "
            select distinct
    INV.INVNO as INVENTORY_NUMBER,
    INV.INVDP as WAREHOUSE,
    case
        when trim(INV.LOCAL) = '' then cast(null as varchar(12))
        else trim(INV.LOCAL)
    end as location,
    case
        when trim(INV.LOCA2) = '' then cast(null as varchar(12))
        else trim(INV.LOCA2)
    end as LOCATION2,
    case
        when trim(INV.LOCA3) = '' then cast(null as varchar(12))
        else trim(INV.LOCA3)
    end as LOCATION3,
    trim(INV.INVAR) as CODE_ARTICLE,
    trim(INV.DESI1) as DESIGNATION1,
    trim(INV.DESI2) as DESIGNATION2,
    trim(INVLO.INLLO) as CODE_LOT,
    trim(ART.TYDIM) as TYPE_DIMENSION,
    trim(ART.CONDI) as CONDITIONNEMENT,
    case
        when (UNI.COD15 <> '')
        and ART.CDCON = 'NON' then trim(ART.ARTD4)
        else trim(ART.ULPRE)
    end as LIBELLE_CONDI,
    cast(null as decimal(8)) as QUANTITE_LOC1,
    cast(null as decimal(8)) as QUANTITE_LOC2,
    cast(null as decimal(8)) as QUANTITE_LOC3,
    trim(ART.ULPRE) as UNITE_PREPARATION,
    cast(null as decimal(8)) as QUANTITE2_LOC1,
    cast(null as decimal(8)) as QUANTITE2_LOC2,
    cast(null as decimal(8)) as QUANTITE2_LOC3,
    case
        when trim(ART.CDCON) = 'NON' then 0
        when trim(ART.CDCON) = '' then 0
        when trim(ART.CDCON) = 'OUI' then 1
        else 1
    end as DIVISIBLE,
    trim(ART.ART32) as TYPE_ARTICLE,
    ART.SERST as SERVED_FROM_STOCK,
    STO.QTINV as QUANTITE_THEORIQUE
from
    AQAGESTCOM.AARTICP1 ART
    inner join AQAGESTCOM.AINVENP1 INV on ART.NOART = INV.INVAR
    left outer join AQAGESTCOM.ASTOLOP1 LOT on LOT.LOART = ART.NOART
    left outer join AQAGESTCOM.ASTOCKP1 STO on ART.NOART = STO.NOART and ART.ARTL3 = STO.DEPOT
    left outer join AQAGESTCOM.AINVLOP1 INVLO on ART.NOART = INVLO.INLAR
    left outer join AQAGESTCOM.ATAB15P1 UNI on ART.ARTD4 = UNI.LIRPR
    and UNI.TYPPR = 'UNI'
where
(   
    ART.ART32 = 'ART'
    and
    ART.ARDIV <> 'OUI'
    and -- Hors articles divers
    ART.ETARE <> 'S'
    and -- Non suspendu
    INV.LOCAL <> ''
    and 
    INV.INVNO = '$inventoryNumber'
)
    
 or 

(    
    ART.ART32 = 'LOV'
    and
    ART.ARDIV <> 'OUI'
    and -- Hors articles divers
    ART.ETARE <> 'S'
    and -- Non suspendu
    INV.LOCAL <> ''
    and 
    LOT.LOLOT = INVLO.INLLO
    and 
    LOINV > 0.000
    and 
    INV.INVNO = '$inventoryNumber'
)
order by
    location,
    LOCATION2,
    LOCATION3
     ";
        return $sql;
    }
}
