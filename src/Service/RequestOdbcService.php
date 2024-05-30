<?php

namespace App\Service;

class RequestOdbcService
{


    public function getOrders(): String
    {
        $sql = "
        SELECT
            ENT_CMD.NOBON AS ID,
            CLI.NOCLI AS CORPORATIONID,
            CASE
            WHEN ENT_CMD.ENPRM = 'AC' THEN 'edited'
            ELSE 'editable'end AS ORDERSTATUS,
            ENT_CMD.RFCSB AS REFERENCE,    
            CONCAT(CONCAT(CONCAT(CONCAT(CONCAT(ENT_CMD.DSECS, ENT_CMD.DSECA), '-'),ENT_CMD.DSECM),'-'),ENT_CMD.DSECJ) AS ORDERDATE,
            CONCAT(CONCAT(CONCAT(CONCAT(CONCAT(ENT_CMD.DLSSB, ENT_CMD.DLASB), '-'),ENT_CMD.DLMSB),'-'),ENT_CMD.DLJSB) AS DELIVERYDATE,
            CASE 
                WHEN ENT_CMD.TYCDE = 'ORA' THEN ENT_CMD.TYCDE
                ELSE ENT_CMD.TYVTE 
            END AS TYPE,
            ENT_CMD.LIVSB AS SELLER,
            ENT_CMD.COMED AS COMMENT
        FROM
            AQAGESTCOM.AENTBOP1 ENT_CMD
        INNER JOIN
            AQAGESTCOM.ACLIENP1 CLI ON CLI.NOCLI = ENT_CMD.NOCLI
        INNER JOIN
            AQAGESTCOM.ADETBOP1 DET_CMD ON DET_CMD.NOBON = ENT_CMD.NOBON
        WHERE
        CONCAT(CONCAT(CONCAT(ENT_CMD.DSECJ, ENT_CMD.DSECM), ENT_CMD.DSECS), ENT_CMD.DSECA) = '16052024'
            AND CLDI1 = 'AD'
            AND CLI.NOCLI != 'FICTIF'
            AND ETCLE != 'S'
            AND CODAR != ''
            LIMIT 10";
        return $sql;
    }

    public function getCoporations(): String
    {
        $sql = "
        select 
            trim(NOCLI) id
            ,trim(NOMCL) name
            ,case
        when SURCL = '000' then 1
            else 0 end status
   from
       AQAGESTCOM.ACLIENP1 CLI
   where
       CLDI1='AD'
       and NOCLI != 'FICTIF'
       and ETCLE != 'S'";

        return $sql;
    }
}
