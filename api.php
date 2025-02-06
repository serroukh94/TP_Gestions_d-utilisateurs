<?php
require_once 'UserManager.php';
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$userManager = new UserManager();

try {
    if ($method === 'POST' && isset($_POST['name'], $_POST['email'])) {
        // Récupération du rôle avec une valeur par défaut "user" si non fourni
        $role = isset($_POST['role']) ? $_POST['role'] : 'user';
        $userManager->addUser($_POST['name'], $_POST['email'], $role);
        echo json_encode(["message" => "Utilisateur ajouté avec succès"], JSON_THROW_ON_ERROR);
    } elseif ($method === 'GET') {
        echo json_encode($userManager->getUsers(), JSON_THROW_ON_ERROR);
    } elseif ($method === 'DELETE' && isset($_GET['id'])) {
        $userManager->removeUser($_GET['id']);
        echo json_encode(["message" => "Utilisateur supprimé"], JSON_THROW_ON_ERROR);
    } elseif ($method === 'PUT') {
        parse_str(file_get_contents("php://input"), $_PUT);
        if (isset($_PUT['id'], $_PUT['name'], $_PUT['email'])) {
            // Récupération du rôle pour la mise à jour ; si non fourni, on le laisse inchangé en passant null
            $role = isset($_PUT['role']) ? $_PUT['role'] : null;
            $userManager->updateUser($_PUT['id'], $_PUT['name'], $_PUT['email'], $role);
            echo json_encode(["message" => "Utilisateur mis à jour"], JSON_THROW_ON_ERROR);
        } else {
            throw new Exception("Paramètres manquants pour la mise à jour.");
        }
    } else {
        throw new Exception("Requête invalide.");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
