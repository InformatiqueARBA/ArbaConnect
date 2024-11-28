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
            inner join AQAGESTCOM.ADETBOP1 ENT_CMD on ENT_CMD.NOBON = DET_CMD.NOBON
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
select
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
    RowNum = 1;