<?php 

declare(strict_types=1);

class Imprimante
{  
    // Attributs privés
    private string $nom;
    private int $nbFeuilles;
    private bool $allumee;
    // Constructeur
    public function __construct(string $nom, int $nbFeuilles)
    {
        if ($nbFeuilles < 0 || $nbFeuilles > 250) {
            throw new Exception("Le stock doit être compris entre 0 et 250 feuilles.");
        }
        $this->nom = $nom;
        $this->nbFeuilles = $nbFeuilles;
        $this->allumee = false; // L'imprimante est éteinte par défaut
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getNbFeuilles(): int
    {
        return $this->nbFeuilles;
    }

    public function isAllumee(): bool
    {
        return $this->allumee;
    }

    public function allumer(): void
    {
        $this->allumee = true;
    }

    public function eteindre(): void
    {
        $this->allumee = false;
    }

}
