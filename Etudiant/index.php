<?php
require_once 'Etudiant.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Étudiants</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --light-color: #f1f2f6;
            --dark-color: #2c3e50;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--light-color);
            color: var(--dark-color);
            line-height: 1.6;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        h1 {
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .student-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            border-left: 5px solid var(--primary-color);
        }
        
        .student-name {
            font-size: 1.5rem;
            color: var(--dark-color);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .student-name i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .notes-container {
            margin: 15px 0;
        }
        
        .notes-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 10px 0;
        }
        
        .note {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: bold;
            color: white;
        }
        
        .note.fail {
            background-color: var(--danger-color);
        }
        
        .note.pass {
            background-color: var(--secondary-color);
        }
        
        .note.neutral {
            background-color: var(--warning-color);
        }
        
        .resultat {
            margin-top: 15px;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }
        
        .admis {
            background-color: rgba(46, 204, 113, 0.2);
            color: var(--secondary-color);
        }
        
        .non-admis {
            background-color: rgba(231, 76, 60, 0.2);
            color: var(--danger-color);
        }
        
        footer {
            text-align: center;
            margin-top: 30px;
            color: #7f8c8d;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-user-graduate"></i> Gestion des Étudiants</h1>
            <p>Système de gestion des notes et résultats</p>
        </header>
        
        <?php
        $etudiants = [
            new Etudiant("Aymen", [11, 13, 18, 7, 10, 13, 2, 5, 1]),
            new Etudiant("Skander", [15, 9, 8, 16]),
            new Etudiant("Nesrine", [19, 18, 17, 20, 16]),
            new Etudiant("Sami", [5, 7, 4, 9, 8])
        ];
        
        foreach ($etudiants as $etudiant) {
            echo "<div class='student-card'>";
            echo "<h2 class='student-name'><i class='fas fa-user'></i> " . $etudiant->getNom() . "</h2>";
            $etudiant->afficherNotes();
            $etudiant->afficherResultat();
            echo "</div>";
        }
        ?>
        
        <footer>
            <p>&copy; <?php echo date("Y"); ?> - Système de Gestion des Étudiants</p>
        </footer>
    </div>
</body>
</html>