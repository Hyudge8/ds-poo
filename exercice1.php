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
        $this->allumee = false;
    }

    // Accesseurs
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

    // Allumer l'imprimante
    public function allumer(): void
    {
        $this->allumee = true;
    }

    // Éteindre l'imprimante
    public function eteindre(): void
    {
        $this->allumee = false;
    }

    // Ajouter du papier
    public function ajouterPapier(int $n): void
    {
        if ($n <= 0) {
            throw new Exception("La quantité ajoutée doit être positive.");
        }

        if ($n > 100) {
            throw new Exception("Un ajout ne peut pas dépasser 100 feuilles.");
        }

        if ($this->nbFeuilles + $n > 250) {
            throw new Exception("Le stock final ne peut pas dépasser 250 feuilles.");
        }

        $this->nbFeuilles = $this->nbFeuilles + $n;
    }

    // Imprimer des pages
    public function imprimer(int $n): void
    {
        if ($n <= 0) {
            throw new Exception("Le nombre de pages doit être positif.");
        }

        if (!$this->allumee) {
            throw new Exception("L'imprimante est éteinte.");
        }

        if ($this->nbFeuilles < $n) {
            throw new Exception("Il n'y a pas assez de feuilles.");
        }

        $this->nbFeuilles = $this->nbFeuilles - $n;
    }
}

// Tests de l'imprimante
$imprimante = new Imprimante("Salle 204", 40);

echo "Imprimante : " . $imprimante->getNom() . PHP_EOL;
echo "Stock initial : " . $imprimante->getNbFeuilles() . " feuilles" . PHP_EOL;

// Test 1 : imprimer lorsque l'imprimante est éteinte
try {
    $imprimante->imprimer(10);
} catch (Exception $e) {
    echo "Test 1 : impression refusée : " . $e->getMessage() . PHP_EOL;
}

// Test 2 : ajouter 80 feuilles
try {
    $imprimante->ajouterPapier(80);
    echo "Test 2 : ajout accepté. Stock : " . $imprimante->getNbFeuilles() . " feuilles" . PHP_EOL;
} catch (Exception $e) {
    echo "Test 2 : erreur : " . $e->getMessage() . PHP_EOL;
}

// Allumer l'imprimante
$imprimante->allumer();

// Test 3 : imprimer 30 pages
try {
    $imprimante->imprimer(30);
    echo "Test 3 : impression acceptée. Stock : " . $imprimante->getNbFeuilles() . " feuilles" . PHP_EOL;
} catch (Exception $e) {
    echo "Test 3 : erreur : " . $e->getMessage() . PHP_EOL;
}

// Test 4 : essayer d'imprimer 100 pages
try {
    $imprimante->imprimer(100);
} catch (Exception $e) {
    echo "Test 4 : impression refusée : " . $e->getMessage() . PHP_EOL;
    echo "Stock conservé : " . $imprimante->getNbFeuilles() . " feuilles" . PHP_EOL;
}

// Test 5 : essayer d'ajouter 120 feuilles
try {
    $imprimante->ajouterPapier(120);
} catch (Exception $e) {
    echo "Test 5 : ajout refusé : " . $e->getMessage() . PHP_EOL;
}

// Test 6 : essayer d'ajouter 0 feuille
try {
    $imprimante->ajouterPapier(0);
} catch (Exception $e) {
    echo "Test 6 : ajout refusé : " . $e->getMessage() . PHP_EOL;
}

// Affichage du stock final
echo "Stock final : " . $imprimante->getNbFeuilles() . " feuilles" . PHP_EOL;
