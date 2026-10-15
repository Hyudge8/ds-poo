<?php

declare(strict_types=1);

/**
 * Classe mère : un équipement du lycée, identifié par son numéro d'inventaire.
 */
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

    public function getMarque(): string
    {
        return $this->marque;
    }

    public function description(): string
    {
        return "Équipement n° {$this->numeroInventaire} (marque : {$this->marque})";
    }
}

/**
 * Contrat : tout matériel qui peut être prêté doit offrir ces trois méthodes.
 * L'interface ne contient aucune règle métier, seulement les signatures.
 */
interface Empruntable
{
    public function emprunter(string $nom): void;

    public function restituer(): void;

    public function estDisponible(): bool;
}

/**
 * Classe fille : un portable est un équipement ET un matériel empruntable.
 */
class OrdinateurPortable extends Equipement implements Empruntable
{
    private int $ramGo;
    private ?string $emprunteur = null; // null = disponible

    public function __construct(string $numeroInventaire, string $marque, int $ramGo)
    {
        if ($ramGo <= 0) {
            throw new InvalidArgumentException(
                "RAM invalide ($ramGo Go) : elle doit être strictement positive."
            );
        }

        parent::__construct($numeroInventaire, $marque); // initialise la partie "Equipement"
        $this->ramGo = $ramGo;
    }

    public function getRamGo(): int
    {
        return $this->ramGo;
    }

    public function getEmprunteur(): ?string
    {
        return $this->emprunteur;
    }

    public function emprunter(string $nom): void
    {
        if (trim($nom) === '') {
            throw new InvalidArgumentException(
                "Emprunt refusé : le nom de l'emprunteur ne peut pas être vide."
            );
        }
        if ($this->emprunteur !== null) {
            throw new LogicException(
                "Emprunt refusé : le portable est déjà emprunté par {$this->emprunteur}."
            );
        }

        $this->emprunteur = trim($nom); // état modifié seulement après validation
    }

    public function restituer(): void
    {
        if ($this->emprunteur === null) {
            throw new LogicException(
                'Restitution refusée : aucun emprunt en cours pour ce portable.'
            );
        }

        $this->emprunteur = null;
    }

    public function estDisponible(): bool
    {
        return $this->emprunteur === null;
    }

    /**
     * Redéfinition : on réutilise la description du parent et on ajoute la RAM.
     */
    public function description(): string
    {
        return parent::description() . " - Ordinateur portable, {$this->ramGo} Go de RAM";
    }
}

/**
 * Fonction indépendante des classes concrètes : elle ne connaît que le contrat.
 * Tout objet Empruntable (portable, vidéoprojecteur, etc.) fonctionnerait.
 */
function afficherDisponibilite(Empruntable $materiel): void
{
    echo $materiel->estDisponible() ? 'Disponible' : 'Emprunté';
    echo PHP_EOL;
}

// =====================================================================
// Tests
// =====================================================================

/**
 * Exécute une action et affiche son résultat : succès ou refus expliqué.
 */
function tester(string $titre, callable $action): void
{
    echo "▶ $titre" . PHP_EOL;
    try {
        $action();
        echo '   OK : opération acceptée.' . PHP_EOL;
    } catch (Exception $e) {
        echo '   Refus (' . get_class($e) . ') : ' . $e->getMessage() . PHP_EOL;
    }
}

$portable = new OrdinateurPortable('LENOVO-024', 'Lenovo', 16);

// 1. Description et disponibilité initiale
echo $portable->description() . PHP_EOL;
echo 'Disponibilité initiale : ';
afficherDisponibilite($portable); // attendu : Disponible
echo PHP_EOL;

// 2. Emprunt par Camille
tester('2. Emprunt au nom de « Camille »', fn () => $portable->emprunter('Camille'));
echo 'Disponibilité : ';
afficherDisponibilite($portable); // attendu : Emprunté
echo PHP_EOL;

// 3. Second emprunt refusé
tester('3. Second emprunt au nom de « Alex »', fn () => $portable->emprunter('Alex'));
echo "   Emprunteur toujours enregistré : {$portable->getEmprunteur()}" . PHP_EOL . PHP_EOL;

// 4. Restitution
tester('4. Restitution du portable', fn () => $portable->restituer());
echo 'Disponibilité : ';
afficherDisponibilite($portable); // attendu : Disponible
echo PHP_EOL;

// 5. Nouvelle restitution refusée
tester('5. Seconde restitution', fn () => $portable->restituer());
echo PHP_EOL;

// Bonus : règles complémentaires
tester('Bonus a. Emprunt avec un nom composé d\'espaces', fn () => $portable->emprunter('   '));
tester('Bonus b. Création avec 0 Go de RAM', fn () => new OrdinateurPortable('TEST-001', 'Dell', 0));