
<?php
require_once 'AttackPokemon.php';
require_once 'Pokemon.php';

function simulateBattle($pokemon1, $pokemon2) {
    $battleLog = [];
    $round = 1;
    
    $battleLog[] = "<div class='battle-start'>";
    $battleLog[] = "<h2>Combat Pokémon</h2>";
    $battleLog[] = "<p class='battle-intro'>Un combat entre <span class='pokemon-name'>{$pokemon1->getName()}</span> et <span class='pokemon-name'>{$pokemon2->getName()}</span> commence!</p>";
    $battleLog[] = "</div>";
    
    $battleLog[] = "<div class='pokemon-details'>";
    $battleLog[] = "<div class='pokemon-column'>" . $pokemon1->whoAmI() . "</div>";
    $battleLog[] = "<div class='pokemon-column'>" . $pokemon2->whoAmI() . "</div>";
    $battleLog[] = "</div>";
    
    $battleLog[] = "<div class='battle-log'>";
    
    while (!$pokemon1->isDead() && !$pokemon2->isDead()) {
        $battleLog[] = "<div class='round'>";
        $battleLog[] = "<h3>Round {$round}</h3>";
        
        $battleLog[] = $pokemon1->attack($pokemon2);
        
        if ($pokemon2->isDead()) {
            break;
        }
        
        $battleLog[] = $pokemon2->attack($pokemon1);
        
        $battleLog[] = "</div>"; 
        $round++;
    }
    
    $battleLog[] = "</div>";
    
    $battleLog[] = "<div class='battle-result'>";
    if ($pokemon1->isDead()) {
        $battleLog[] = "<p class='winner'><span class='pokemon-name'>{$pokemon2->getName()}</span> remporte le combat avec {$pokemon2->getHp()} HP restants!</p>";
        $winner = $pokemon2;
    } else {
        $battleLog[] = "<p class='winner'><span class='pokemon-name'>{$pokemon1->getName()}</span> remporte le combat avec {$pokemon1->getHp()} HP restants!</p>";
        $winner = $pokemon1;
    }
    $battleLog[] = "</div>";
    
    return [
        'log' => implode("\n", $battleLog),
        'winner' => $winner
    ];
}

$pokemons = [
    new Pokemon(
        "Evoli", 
        "https://assets.pokemon.com/assets/cms2/img/pokedex/full/133.png", 
        100, 
        new AttackPokemon(10, 20, 2, 30)
    ),
    new PokemonFeu(
        "Salameche", 
        "https://assets.pokemon.com/assets/cms2/img/pokedex/full/004.png", 
        90, 
        new AttackPokemon(15, 25, 1.8, 25)
    ),
    new PokemonEau(
        "Carapuce", 
        "https://assets.pokemon.com/assets/cms2/img/pokedex/full/007.png", 
        120, 
        new AttackPokemon(8, 18, 2.2, 20)
    ),
    new PokemonPlante(
        "Bulbizarre", 
        "https://assets.pokemon.com/assets/cms2/img/pokedex/full/001.png", 
        110, 
        new AttackPokemon(12, 22, 1.9, 35)
    )
];

$selectedPokemon1 = null;
$selectedPokemon2 = null;
$battleResult = null;

if (isset($_POST['start_battle']) && isset($_POST['pokemon1']) && isset($_POST['pokemon2'])) {
    $selectedPokemon1 = $pokemons[$_POST['pokemon1']];
    $selectedPokemon2 = $pokemons[$_POST['pokemon2']];
    
    $pokemon1 = clone $selectedPokemon1;
    $pokemon2 = clone $selectedPokemon2;
    
    $battleResult = simulateBattle($pokemon1, $pokemon2);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Combat Pokémon</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --fire-color: #F08030;
            --water-color: #6890F0;
            --grass-color: #78C850;
            --normal-color: #A8A878;
            --dark-color: #2c3e50;
            --light-color: #f5f6fa;
            --container-color: white;
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
            background-color: var(--container-color);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        header {
            background: linear-gradient(135deg, #ff9966, #ff5e62);
            color: white;
            text-align: center;
            padding: 30px 20px;
        }
        
        header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .content {
            padding: 30px;
        }
        
        .pokemon-selection {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .pokemon-card {
            width: 220px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .pokemon-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .pokemon-card.selected {
            border: 3px solid #3498db;
            transform: translateY(-5px);
        }
        
        .pokemon-card img {
            width: 100%;
            height: 150px;
            object-fit: contain;
            background-color: #f7f7f7;
            padding: 10px;
        }
        
        .pokemon-card-info {
            padding: 15px;
        }
        
        .pokemon-card-info h3 {
            font-size: 1.2rem;
            margin-bottom: 8px;
            color: var(--dark-color);
        }
        
        .type {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
            color: white;
            margin-bottom: 8px;
        }
        
        .type-Feu {
            background-color: var(--fire-color);
        }
        
        .type-Eau {
            background-color: var(--water-color);
        }
        
        .type-Plante {
            background-color: var(--grass-color);
        }
        
        .type-Normal {
            background-color: var(--normal-color);
        }
        
        .battle-controls {
            text-align: center;
            margin: 20px 0;
        }
        
        .battle-btn {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .battle-btn:hover {
            background: linear-gradient(135deg, #2980b9, #2471a3);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .battle-btn:disabled {
            background: #95a5a6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .battle-area {
            background-color: #f5f6fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .pokemon-details {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
            margin: 20px 0;
        }
        
        .pokemon-column {
            flex: 1;
            min-width: 280px;
            max-width: 400px;
        }
        
        .pokemon-info {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            text-align: center;
        }
        
        .pokemon-info h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: var(--dark-color);
        }
        
        .pokemon-image {
            width: 150px;
            height: 150px;
            object-fit: contain;
            margin: 10px 0;
        }
        
        .hp {
            font-weight: bold;
            color: #e74c3c;
        }
        
        .battle-log {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin: 20px 0;
            max-height: 500px;
            overflow-y: auto;
        }
        
        .round {
            margin: 20px 0;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .round h3 {
            color: #7f8c8d;
            margin-bottom: 10px;
        }
        
        .attack-message {
            margin: 10px 0;
            line-height: 1.8;
        }
        
        .attacker {
            font-weight: bold;
            color: #3498db;
        }
        
        .defender {
            font-weight: bold;
            color: #e74c3c;
        }
        
        .special {
            font-weight: bold;
            color: #9b59b6;
        }
        
        .damage {
            font-weight: bold;
            color: #e74c3c;
        }
        
        .super-effective {
            font-weight: bold;
            color: #27ae60;
        }
        
        .not-effective {
            font-weight: bold;
            color: #95a5a6;
        }
        
        .hp-remaining {
            font-style: italic;
            color: #7f8c8d;
        }
        
        .fainted {
            font-weight: bold;
            color: #e74c3c;
        }
        
        .battle-result {
            background-color: #f39c12;
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            font-size: 1.2rem;
            margin: 20px 0;
            animation: pulse 2s infinite;
        }
        
        .winner {
            font-weight: bold;
        }
        
        .pokemon-name {
            font-weight: bold;
        }
        
        .battle-intro {
            text-align: center;
            font-size: 1.2rem;
            margin: 20px 0;
        }
        
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(243, 156, 18, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(243, 156, 18, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(243, 156, 18, 0);
            }
        }
        
        footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            color: #7f8c8d;
            font-size: 0.9rem;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-gamepad"></i> Système de Combat Pokémon</h1>
            <p>Sélectionnez deux Pokémon pour lancer un combat!</p>
        </header>
        
        <div class="content">
            <form method="post" id="battleForm">
                <div class="pokemon-selection">
                    <?php foreach ($pokemons as $index => $pokemon): ?>
                        <div class="pokemon-card <?php echo (isset($_POST['pokemon1']) && $_POST['pokemon1'] == $index) ? 'selected' : ''; ?>" 
                             onclick="selectPokemon(1, <?php echo $index; ?>)">
                            <img src="<?php echo $pokemon->getUrl(); ?>" alt="<?php echo $pokemon->getName(); ?>">
                            <div class="pokemon-card-info">
                                <h3><?php echo $pokemon->getName(); ?></h3>
                                <span class="type type-<?php echo $pokemon->getType(); ?>"><?php echo $pokemon->getType(); ?></span>
                                <p>HP: <?php echo $pokemon->getHp(); ?></p>
                            </div>
                            <input type="radio" name="pokemon1" value="<?php echo $index; ?>" 
                                   <?php echo (isset($_POST['pokemon1']) && $_POST['pokemon1'] == $index) ? 'checked' : ''; ?> 
                                   style="display: none;">
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="battle-controls">
                    <p>VS</p>
                </div>
                
                <div class="pokemon-selection">
                    <?php foreach ($pokemons as $index => $pokemon): ?>
                        <div class="pokemon-card <?php echo (isset($_POST['pokemon2']) && $_POST['pokemon2'] == $index) ? 'selected' : ''; ?>"
                             onclick="selectPokemon(2, <?php echo $index; ?>)">
                            <img src="<?php echo $pokemon->getUrl(); ?>" alt="<?php echo $pokemon->getName(); ?>">
                            <div class="pokemon-card-info">
                                <h3><?php echo $pokemon->getName(); ?></h3>
                                <span class="type type-<?php echo $pokemon->getType(); ?>"><?php echo $pokemon->getType(); ?></span>
                                <p>HP: <?php echo $pokemon->getHp(); ?></p>
                            </div>
                            <input type="radio" name="pokemon2" value="<?php echo $index; ?>" 
                                   <?php echo (isset($_POST['pokemon2']) && $_POST['pokemon2'] == $index) ? 'checked' : ''; ?> 
                                   style="display: none;">
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="battle-controls">
                    <button type="submit" name="start_battle" class="battle-btn" id="battleButton" disabled>
                        <i class="fas fa-bolt"></i> Lancer le Combat
                    </button>
                </div>
            </form>
            
            <?php if ($battleResult): ?>
                <div class="battle-area">
                    <?php echo $battleResult['log']; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <footer>
            <p>&copy; <?php echo date("Y"); ?> - Système de Combat Pokémon</p>
        </footer>
    </div>
    
    <script>
        function selectPokemon(player, index) {
            const playerCards = document.querySelectorAll(`.pokemon-card`);
            
            playerCards.forEach(card => {
                const radio = card.querySelector(`input[name="pokemon${player}"]`);
                if (radio && radio.value == index) {
                    card.classList.add('selected');
                    radio.checked = true;
                } else if (radio && radio.name === `pokemon${player}`) {
                    card.classList.remove('selected');
                    radio.checked = false;
                }
            });
            
            checkBattleButton();
        }
        
        function checkBattleButton() {
            const pokemon1Selected = document.querySelector('input[name="pokemon1"]:checked');
            const pokemon2Selected = document.querySelector('input[name="pokemon2"]:checked');
            
            const battleButton = document.getElementById('battleButton');
            battleButton.disabled = !(pokemon1Selected && pokemon2Selected);
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            checkBattleButton();
        });
    </script>
</body>
</html>