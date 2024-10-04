        SELECT DISTINCT
             INV.INVDP AS WAREHOUSE -- Dépôt 
            ,LEFT(INV.LOCAL, 5) AS LOCATION -- 1ère Loc sur 5 caractères
            ,CAST(NULL AS VARCHAR(50)) AS REFERENT
            ,0 AS STATUS
            ,INV.INVNO AS INVENTORY_NUMBER -- N° d'inventaire
        FROM 
            AQAGESTCOM.AINVENP1 INV
        WHERE
            ETARE <> 'S' AND 
            INVST = 'OUI' AND 
            INV.LOCAL <> ' ' AND  
            INV.INVNO = '002612'
    
        UNION
    
        SELECT DISTINCT
             INV.INVDP AS WAREHOUSE -- Dépôt 
            ,LEFT(INV.LOCA2, 5) AS LOCATION -- 2ème Loc sur 5 caractères 
            ,CAST(NULL AS VARCHAR(50)) AS REFERENT
            ,0 AS STATUS
            ,INV.INVNO AS INVENTORY_NUMBER -- N° d'inventaire
        FROM 
            AQAGESTCOM.AINVENP1 INV
        WHERE
            ETARE <> 'S' AND 
            INVST = 'OUI' AND 
            INV.LOCAL <> ' ' AND  
            INV.INVNO = '002612'
    
        UNION
    
        SELECT DISTINCT
             INV.INVDP AS WAREHOUSE -- Dépôt  
            ,LEFT(INV.LOCA3, 5) AS LOCATION -- 3ème Loc sur 5 caractères
            ,CAST(NULL AS VARCHAR(50)) AS REFERENT
            ,0 AS STATUS
            ,INV.INVNO AS INVENTORY_NUMBER -- N° d'inventaire
        FROM 
            AQAGESTCOM.AINVENP1 INV
        WHERE
            ETARE <> 'S' AND 
            INVST = 'OUI' AND 
            INV.LOCAL <> ' ' AND  
            INV.INVNO = '002612'
    
        ORDER BY LOCATION