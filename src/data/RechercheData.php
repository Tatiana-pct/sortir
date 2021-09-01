<?php

namespace App\data;

use DateTime;

class RechercheData
{


    private $q='';

    private $campus;

    /**
     * @var DateTime|null
     */
    private $dateHeureDebut;

    /**
     * @var DateTime|null
     */
    private $dateCloture;

    /**
     * @var bool
     */
    private $organisateur = false;

    /**
     * @var bool
     */
    private $inscrit = false;

    /**
     * @var bool
     */
    private $notInscrit = false;

    /**
     * @var bool
     */
    private $passee = false;




    public function getQ()
    {
        return $this->q;
    }


    public function setQ($q): void
    {
        $this->q = $q;
    }


    public function getCampus()
    {
        return $this->campus;
    }


    public function setCampus($campus): void
    {
        $this->campus = $campus;
    }

    /**
     * @return DateTime|null
     */
    public function getDateHeureDebut(): ?DateTime
    {
        return $this->dateHeureDebut;
    }

    /**
     * @param DateTime|null $dateHeureDebut
     */
    public function setDateHeureDebut(?DateTime $dateHeureDebut): void
    {
        $this->dateHeureDebut = $dateHeureDebut;
    }

    /**
     * @return DateTime|null
     */
    public function getDateCloture(): ?DateTime
    {
        return $this->dateCloture;
    }

    /**
     * @param DateTime|null $dateCloture
     */
    public function setDateCloture(?DateTime $dateCloture): void
    {
        $this->dateCloture = $dateCloture;
    }




    /**
     * @return bool|null
     */
    public function isOrganisateur(): bool
    {
        return $this->organisateur;
    }

    /**
     * @param bool|null $organisateur
     */
    public function setOrganisateur(bool $organisateur): void
    {
        $this->organisateur = $organisateur;
    }

    /**
     * @return bool|null
     */
    public function isInscrit(): bool
    {
        return $this->inscrit;
    }


    /**
     * @param bool|null $inscrit
     */
    public function setInscrit(bool $inscrit): void
    {
        $this->inscrit = $inscrit;
    }

    /**
     * @return bool|null
     */
    public function isNotInscrit(): bool
    {
        return $this->notInscrit;
    }

    /**
     * @param bool|null $notInscrit
     */
    public function setNotInscrit(bool $notInscrit): void
    {
        $this->notInscrit = $notInscrit;
    }

    /**
     * @return bool|null
     */
    public function isPassee(): bool
    {
        return $this->passee;
    }

    /**
     * @param bool $passee
     */
    public function setPassee(bool $passee): void
    {
        $this->passee = $passee;
    }

    public function getVille()
    {
    }

}