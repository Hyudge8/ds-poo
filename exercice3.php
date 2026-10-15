
<?php

declare(strict_types=1);

// Classe qui représente un modèle de matériel
class ModeleMateriel
{
    // Attributs privés du modèle
    private ?int $id = null;
    private string $reference = '';
    private string $designation = '';
    private string $categorie = '';

    // Le constructeur initialise l'objet avec les données reçues
    public function __construct(array $donnees = [])
    {
        if (!empty($donnees)) {
            $this->hydrate($donnees);
        }
    }

    // Getters et setters : lire et modifier les attributs

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    public function getDesignation(): string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): void
    {
        $this->designation = $designation;
    }

    public function getCategorie(): string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): void
    {
        $this->categorie = $categorie;
    }

    // Remplit l'objet à partir d'un tableau de données
    public function hydrate(array $donnees): void
    {
        if (isset($donnees['id'])) {
            $this->setId((int) $donnees['id']);
        }

        if (isset($donnees['reference'])) {
            $this->setReference((string) $donnees['reference']);
        }

        if (isset($donnees['designation'])) {
            $this->setDesignation((string) $donnees['designation']);
        }

        if (isset($donnees['categorie'])) {
            $this->setCategorie((string) $donnees['categorie']);
        }
    }
}

// Classe qui gère les échanges avec la base de données
class ModeleMaterielManager
{
    private PDO $pdo;

    // Reçoit la connexion PDO et active les exceptions en cas d'erreur
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
    }

    // Ajoute un modèle dans la base de données
    public function ajouter(ModeleMateriel $modele): void
    {
        // Requête préparée pour insérer un nouveau modèle
        $sql = "INSERT INTO modele_materiel
                (reference, designation, categorie)
                VALUES (:reference, :designation, :categorie)";

        $requete = $this->pdo->prepare($sql);

        // Envoie les valeurs de l'objet à la requête
        $requete->execute([
            'reference' => $modele->getReference(),
            'designation' => $modele->getDesignation(),
            'categorie' => $modele->getCategorie()
        ]);

        // Récupère l'identifiant créé automatiquement par MySQL
        $modele->setId((int) $this->pdo->lastInsertId());
    }

    // Recherche un modèle grâce à sa référence
    // Retourne un objet ModeleMateriel ou null si aucun résultat
    public function trouverParReference(
        string $reference
    ): ?ModeleMateriel {
        $sql = "SELECT id, reference, designation, categorie
                FROM modele_materiel
                WHERE reference = :reference";

        $requete = $this->pdo->prepare($sql);

        // Exécute la recherche avec la référence demandée
        $requete->execute([
            'reference' => $reference
        ]);

        // Récupère le résultat sous forme de tableau associatif
        $donnees = $requete->fetch(PDO::FETCH_ASSOC);

        // Si aucun modèle n'est trouvé, retourne null
        if ($donnees === false) {
            return null;
        }

        // Transforme les données récupérées en objet
        return new ModeleMateriel($donnees);
    }
}

try {
    // Connexion à la base de données MySQL
    $pdo = new PDO(
        'mysql:host=localhost;dbname=ds_poo;charset=utf8mb4',
        'root',
        ''
    );

    // Création du gestionnaire de modèles
    $manager = new ModeleMaterielManager($pdo);

    // Création d'un premier modèle de matériel
    $pc = new ModeleMateriel([
        'reference' => 'PC-LAB-01',
        'designation' => 'Ordinateur laboratoire',
        'categorie' => 'Informatique'
    ]);

    // Création d'un deuxième modèle de matériel
    $videoprojecteur = new ModeleMateriel([
        'reference' => 'VP-AMPHI-01',
        'designation' => 'Videoprojecteur',
        'categorie' => 'Audiovisuel'
    ]);

    // Ajout des deux modèles dans la base de données
    $manager->ajouter($pc);
    echo "Identifiant du PC : " . $pc->getId() . PHP_EOL;

    $manager->ajouter($videoprojecteur);
    echo "Identifiant du videoprojecteur : "
        . $videoprojecteur->getId() . PHP_EOL;

    // Recherche d'un modèle qui existe dans la base
    $modele = $manager->trouverParReference('PC-LAB-01');

    if ($modele !== null) {
        // Affiche les informations du modèle trouvé
        echo PHP_EOL . "Modele trouve :" . PHP_EOL;
        echo "ID : " . $modele->getId() . PHP_EOL;
        echo "Reference : " . $modele->getReference() . PHP_EOL;
        echo "Designation : " . $modele->getDesignation() . PHP_EOL;
        echo "Categorie : " . $modele->getCategorie() . PHP_EOL;
    }

    // Recherche d'une référence qui n'existe pas
    $inexistant = $manager->trouverParReference(
        'REFERENCE-INCONNUE'
    );

    // Vérifie que la méthode retourne bien null
    if ($inexistant === null) {
        echo PHP_EOL;
        echo "Aucun modele trouve pour cette reference."
            . PHP_EOL;
    }
} catch (PDOException $e) {
    // Affiche le message si une erreur de base de données survient
    echo "Erreur de base de donnees : "
        . $e->getMessage() . PHP_EOL;
}
