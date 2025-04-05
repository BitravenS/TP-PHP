<?php
class Pokemon {
    protected $name;
    protected $url;
    protected $hp;
    protected $attackPokemon;
    protected $type = "Normal";
    
    public function __construct($name, $url, $hp, AttackPokemon $attackPokemon) {
        $this->name = $name;
        $this->url = $url;
        $this->hp = $hp;
        $this->attackPokemon = $attackPokemon;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getUrl() {
        return $this->url;
    }
    
    public function getHp() {
        return $this->hp;
    }
    
    public function getType() {
        return $this->type;
    }
    
    public function getAttackPokemon() {
        return $this->attackPokemon;
    }
    
    public function setHp($hp) {
        $this->hp = $hp;
    }
    
    public function isDead() {
        return $this->hp <= 0;
    }
    
    protected function getDamageMultiplier($targetPokemon) {
        return 1;
    }
    
    public function attack(Pokemon $target) {
        $attackPoints = $this->attackPokemon->getAttackPoints();
        $isSpecialAttack = $this->attackPokemon->isSpecialAttack();
        $multiplier = $this->getDamageMultiplier($target);
        
        if ($isSpecialAttack) {
            $attackPoints *= $this->attackPokemon->getSpecialAttackMultiplier();
        }
        
        $attackPoints *= $multiplier;
        $attackPoints = round($attackPoints);
        $target->setHp(max(0, $target->getHp() - $attackPoints));
        
        $message = "<div class='attack-message'>";
        $message .= "<span class='attacker'>{$this->name}</span> attaque <span class='defender'>{$target->getName()}</span>";
        
        if ($isSpecialAttack) {
            $message .= " avec une <span class='special'>ATTAQUE SPÉCIALE</span>";
        }
        
        if ($multiplier > 1) {
            $message .= " - <span class='super-effective'>C'est super efficace!</span>";
        } elseif ($multiplier < 1) {
            $message .= " - <span class='not-effective'>Ce n'est pas très efficace...</span>";
        }
        
        $message .= " et inflige <span class='damage'>{$attackPoints} points</span> de dégâts!";
        
        if ($target->isDead()) {
            $message .= " <span class='fainted'>{$target->getName()} est K.O.!</span>";
        } else {
            $message .= " <span class='hp-remaining'>{$target->getName()} a {$target->getHp()} HP restants.</span>";
        }
        
        $message .= "</div>";
        
        return $message;
    }
    
    public function whoAmI() {
        $output = "<div class='pokemon-info'>";
        $output .= "<h3>{$this->name}</h3>";
        $output .= "<img src='{$this->url}' alt='{$this->name}' class='pokemon-image'>";
        $output .= "<p>Type: <span class='type type-{$this->type}'>{$this->type}</span></p>";
        $output .= "<p>HP: <span class='hp'>{$this->hp}</span></p>";
        $output .= "<p>Attaque: {$this->attackPokemon->getAttackMinimal()} - {$this->attackPokemon->getAttackMaximal()}</p>";
        $output .= "<p>Attaque spéciale: x{$this->attackPokemon->getSpecialAttack()} (Probabilité: {$this->attackPokemon->getProbabilitySpecialAttack()}%)</p>";
        $output .= "</div>";
        
        return $output;
    }
}

class PokemonFeu extends Pokemon {
    protected $type = "Feu";
    
    protected function getDamageMultiplier($target) {
        $targetType = $target->getType();
        
        if ($targetType == "Plante") {
            return 2.0;
        } elseif ($targetType == "Eau" || $targetType == "Feu") {
            return 0.5;
        }
        
        return 1.0;
    }
}

class PokemonEau extends Pokemon {
    protected $type = "Eau";
    
    protected function getDamageMultiplier($target) {
        $targetType = $target->getType();
        
        if ($targetType == "Feu") {
            return 2.0;
        } elseif ($targetType == "Eau" || $targetType == "Plante") {
            return 0.5;
        }
        
        return 1.0;
    }
}

class PokemonPlante extends Pokemon {
    protected $type = "Plante";
    
    protected function getDamageMultiplier($target) {
        $targetType = $target->getType();
        
        if ($targetType == "Eau") {
            return 2.0;
        } elseif ($targetType == "Plante" || $targetType == "Feu") {
            return 0.5;
        }
        
        return 1.0;
    }
}
?>