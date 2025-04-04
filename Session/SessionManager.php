<?php
class SessionManager {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    public function get($key, $default = null) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }
    
    public function has($key) {
        return isset($_SESSION[$key]);
    }
    
    public function remove($key) {
        if ($this->has($key)) {
            unset($_SESSION[$key]);
            return true;
        }
        return false;
    }
    
    public function reset() {
        session_unset();
        session_destroy();
    }
    
    public function all() {
        return $_SESSION;
    }
    
    public function incrementVisitCount() {
        $count = $this->get('visit_count', 0);
        $this->set('visit_count', $count + 1);
        return $count + 1;
    }
    
    public function isFirstVisit() {
        return $this->get('visit_count', 0) <= 1;
    }
}
?>