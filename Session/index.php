<?php
require_once 'SessionManager.php';

$sessionManager = new SessionManager();

if (isset($_POST['reset'])) {
    $sessionManager->reset();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$visitCount = $sessionManager->incrementVisitCount();
$isFirstVisit = $visitCount === 1;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionnaire de Sessions</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6c5ce7;
            --secondary-color: #a29bfe;
            --accent-color: #fd79a8;
            --dark-color: #2d3436;
            --light-color: #f1f2f6;
            --success-color: #00b894;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--light-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            width: 100%;
            max-width: 600px;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        header {
            background-color: var(--primary-color);
            color: white;
            text-align: center;
            padding: 30px 20px;
        }
        
        header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .content {
            padding: 30px;
        }
        
        .message {
            background-color: rgba(108, 92, 231, 0.1);
            border-left: 4px solid var(--primary-color);
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
            font-size: 1.2rem;
        }
        
        .message.first {
            background-color: rgba(0, 184, 148, 0.1);
            border-left: 4px solid var(--success-color);
        }
        
        .counter {
            text-align: center;
            font-size: 3rem;
            color: var(--accent-color);
            font-weight: bold;
            margin: 20px 0;
        }
        
        .action {
            text-align: center;
            margin-top: 30px;
        }
        
        button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }
        
        button i {
            margin-right: 8px;
        }
        
        button:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        footer {
            text-align: center;
            padding: 20px;
            border-top: 1px solid #eee;
            color: #7f8c8d;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-fingerprint"></i> Gestionnaire de Sessions</h1>
            <p>Votre activité sur notre plateforme</p>
        </header>
        
        <div class="content">
            <?php if ($isFirstVisit): ?>
                <div class="message first">
                    <i class="fas fa-hand-sparkles"></i> Bienvenue à notre plateforme!
                </div>
            <?php else: ?>
                <div class="message">
                    <i class="fas fa-medal"></i> Merci pour votre fidélité, c'est votre <?php echo $visitCount; ?><sup>ème</sup> visite!
                </div>
                <div class="counter">
                    <?php echo $visitCount; ?>
                </div>
            <?php endif; ?>
            
            <div class="action">
                <form method="post">
                    <button type="submit" name="reset">
                        <i class="fas fa-redo-alt"></i> Réinitialiser la session
                    </button>
                </form>
            </div>
        </div>
        
        <footer>
            <p>&copy; <?php echo date("Y"); ?> - Système de Gestion des Sessions</p>
        </footer>
    </div>
</body>
</html>