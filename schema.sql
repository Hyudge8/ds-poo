
CREATE TABLE modele_materiel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference VARCHAR(20) NOT NULL UNIQUE,
    designation VARCHAR(100) NOT NULL,
    categorie VARCHAR(30) NOT NULL
);
