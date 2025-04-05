<?php
class AttackPokemon {
    private $attackMinimal;
    private $attackMaximal;
    private $specialAttack;
    private $probabilitySpecialAttack;
    
    public function __construct($attackMinimal, $attackMaximal, $specialAttack, $probabilitySpecialAttack) {
        $this->attackMinimal = $attackMinimal;
        $this->attackMaximal = $attackMaximal;
        $this->specialAttack = $specialAttack;
        $this->probabilitySpecialAttack = $probabilitySpecialAttack;
    }
    
    public function getAttackPoints() {
        return rand($this->attackMinimal, $this->attackMaximal);
    }
    
    public function isSpecialAttack() {
        return rand(1, 100) <= $this->probabilitySpecialAttack;
    }
    
    public function getSpecialAttackMultiplier() {
        return $this->specialAttack;
    }
    
    public function getAttackMinimal() {
        return $this->attackMinimal;
    }
    
    public function getAttackMaximal() {
        return $this->attackMaximal;
    }
    
    public function getSpecialAttack() {
        return $this->specialAttack;
    }
    
    public function getProbabilitySpecialAttack() {
        return $this->probabilitySpecialAttack;
    }
}