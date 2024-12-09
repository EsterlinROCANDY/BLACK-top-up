
<?php
// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=black-haïti', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Gestion des sessions
session_start();

// Gestion des actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    // Inscription
    if ($action == 'inscription' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $nom_complet = $_POST['nom_complet'];
        $email = $_POST['email'];
        $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_BCRYPT);

        $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            echo "L'email existe déjà.";
        } else {
            $stmt = $pdo->prepare('INSERT INTO utilisateurs (nom_complet, email, mot_de_passe) VALUES (?, ?, ?)');
            $stmt->execute([$nom_complet, $email, $mot_de_passe]);
            echo "Inscription réussie.";
        }
    }
    // Connexion
    elseif ($action == 'connexion' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $mot_de_passe = $_POST['mot_de_passe'];

        $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        $utilisateur = $stmt->fetch();

        if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            $_SESSION['utilisateur_id'] = $utilisateur['id'];
            echo "Connexion réussie.";
        } else {
            echo "Email ou mot de passe incorrect.";
        }
    }
    // Ajouter un produit
    elseif ($action == 'ajouter_produit' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $nom = $_POST['nom'];
        $categorie = $_POST['categorie'];
        $prix = $_POST['prix'];
        $description = $_POST['description'];

        $stmt = $pdo->prepare('INSERT INTO produits (nom, categorie, prix, description) VALUES (?, ?, ?, ?)');
        $stmt->execute([$nom, $categorie, $prix, $description]);
        echo "Produit ajouté avec succès.";
    }
    // Afficher les produits
    elseif ($action == 'afficher_produits') {
        $stmt = $pdo->prepare('SELECT * FROM produits');
        $stmt->execute();
        $produits = $stmt->fetchAll();
        echo "<h2>Liste des Produits</h2>";
        foreach ($produits as $produit) {
            echo "<div style='border:1px solid #000; padding:10px; margin:10px;'>";
            echo "<h3>" . htmlspecialchars($produit['nom']) . " - " . htmlspecialchars($produit['prix']) . " HTG</h3>";
            echo "<p>" . htmlspecialchars($produit['description']) . "</p>";
            echo "<p><strong>Catégorie :</strong> " . htmlspecialchars($produit['categorie']) . "</p>";
            echo "</div>";
        }
    }
}

// Produits prédéfinis
$produits = [
    ['nom' => '110 Diamants', 'categorie' => 'Top-Up', 'prix' => 175, 'description' => 'Recharge de 110 diamants pour Free Fire.'],
    ['nom' => '220 Diamants', 'categorie' => 'Top-Up', 'prix' => 350, 'description' => 'Recharge de 220 diamants pour Free Fire.'],
    ['nom' => '341 Diamants', 'categorie' => 'Top-Up', 'prix' => 525, 'description' => 'Recharge de 341 diamants pour Free Fire.'],
    ['nom' => 'Netflix (1 mois)', 'categorie' => 'Carte cadeau', 'prix' => 500, 'description' => 'Carte cadeau Netflix pour 1 mois.'],
    ['nom' => 'Disney+ (1 mois)', 'categorie' => 'Carte cadeau', 'prix' => 500, 'description' => 'Carte cadeau Disney+ pour 1 mois.'],
    ['nom' => 'Prime Video (1 mois)', 'categorie' => 'Carte cadeau', 'prix' => 500, 'description' => 'Carte cadeau Prime Video pour 1 mois.']
];

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Black Haïti</title>
</head>
<body>
    <h1>Bienvenue sur Black Haïti</h1>
    <h2>Produits disponibles</h2>
    <ul>
        <?php foreach ($produits as $produit): ?>
            <li>
                <strong><?php echo htmlspecialchars($produit['nom']); ?></strong> - 
                <?php echo htmlspecialchars($produit['prix']); ?> HTG <br>
                <?php echo htmlspecialchars($produit['description']); ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
