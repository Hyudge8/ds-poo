
<?php

declare(strict_types=1);

class Equipement
{
    private string $numeroInventaire;
    private string $marque;

    public function __construct(string $numeroInventaire, string $marque)
    {
        $this->numeroInventaire = $numeroInventaire;
        $this->marque = $marque;
    }
    
    public function getNumeroInventaire(): string
    {
        return $this->numeroInventaire;
    }
}
