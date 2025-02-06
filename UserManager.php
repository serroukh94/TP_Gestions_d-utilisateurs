<?php
class UserManager {
    private PDO $db;

    public function __construct() {
        $dsn = "mysql:host=localhost;dbname=user_management;charset=utf8";
        $username = "root";
        $password = "root";
        $this->db = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    // Ajout du paramètre $role avec une valeur par défaut "user"
    public function addUser(string $name, string $email, string $role = "user"): void {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Email invalide.");
        }

        $stmt = $this->db->prepare("INSERT INTO users (name, email, role) VALUES (:name, :email, :role)");
        $stmt->execute(['name' => $name, 'email' => $email, 'role' => $role]);
    }

    public function removeUser(int $id): void {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        if ($stmt->rowCount() === 0) {
            throw new Exception("Utilisateur introuvable.");
        }
    }

    public function getUsers(): array {
        $stmt = $this->db->query("SELECT * FROM users");
        return $stmt->fetchAll();
    }

    public function getUser(int $id): array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        if (!$user) throw new Exception("Utilisateur introuvable.");
        return $user;
    }

    // Modification de updateUser pour éventuellement mettre à jour le rôle
    // Si le paramètre $role est fourni (non null), alors le rôle est mis à jour
    public function updateUser(int $id, string $name, string $email, ?string $role = null): void {
        if ($role === null) {
            $stmt = $this->db->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
            $stmt->execute(['id' => $id, 'name' => $name, 'email' => $email]);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET name = :name, email = :email, role = :role WHERE id = :id");
            $stmt->execute(['id' => $id, 'name' => $name, 'email' => $email, 'role' => $role]);
        }
        if ($stmt->rowCount() === 0) {
            throw new Exception("Utilisateur introuvable.");
        }
    }

    public function resetDatabase(): void {
        $this->db->exec("TRUNCATE TABLE users");
    }
}
?>
