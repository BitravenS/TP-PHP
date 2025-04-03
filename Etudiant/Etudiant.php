<?php
class Etudiant {
    private $nom;
    private $notes = [];

    public function __construct($nom, $notes = []) {
        $this->nom = $nom;
        $this->notes = $notes;
    }

    public function ajouterNote($note) {
        $this->notes[] = $note;
    }

    public function afficherNotes() {
        echo "<div class='notes-container'>";
        echo "<h3>Notes de $this->nom:</h3>";
        echo "<div class='notes-grid'>";
        
        foreach ($this->notes as $note) {
            $class = "note";
            if ($note < 10) {
                $class .= " fail";
            } elseif ($note > 10) {
                $class .= " pass";
            } else {
                $class .= " neutral";
            }
            
            echo "<div class='$class'>$note</div>";
        }
        
        echo "</div></div>";
    }

    public function calculerMoyenne() {
        if (count($this->notes) === 0) {
            return 0;
        }
        return array_sum($this->notes) / count($this->notes);
    }

    public function afficherResultat() {
        $moyenne = $this->calculerMoyenne();
        $statut = ($moyenne >= 10) ? "Admis" : "Non Admis";
        $class = ($moyenne >= 10) ? "admis" : "non-admis";
        
        echo "<div class='resultat $class'>";
        echo "<p>Moyenne: " . number_format($moyenne, 2) . "</p>";
        echo "<p>Résultat: $statut</p>";
        echo "</div>";
    }

    public function getNom() {
        return $this->nom;
    }
}
?>

<?php
