<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../UserManager.php';

class UserManagerTest extends TestCase
{
    private UserManager $userManager;
    protected function setUp(): void
    {
        // Initialisation de l’objet UserManager.
        $this->userManager = new UserManager();
        $this->userManager->resetDatabase();

    }
    public function testAddUser(): void
    {
        $name = "Test User";
        $email = "test@example.com";
        $this->userManager->addUser($name, $email);

        $users = $this->userManager->getUsers();
        $this->assertNotEmpty($users, "La liste des utilisateurs ne devrait pas être vide après l'ajout.");
    }

    public function testAddUserWithRole(): void
    {
        $name = "Role Test";
        $email = "role@example.com";
        $role = "admin";
        $this->userManager->addUser($name, $email, $role);

        $array = $this->userManager->getUsers();
        $user = $this->userManager->getUser(end($array)['id']);
        $this->assertEquals($role, $user['role']);
    }


    public function testAddUserEmailException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->userManager->addUser("Test User", "email-non-valide");
    }

    
    public function testUpdateUser(): void
    {
        // Ajout d’un utilisateur puis modification de ses informations
        $name = "User Initial";
        $email = "initial@example.com";
        $this->userManager->addUser($name, $email);

        // Récupérer l'ID de l'utilisateur ajouté
        $users = $this->userManager->getUsers();
        $user = end($users);
        $userId = $user['id'];

        // Mise à jour
        $newName = "User Modifié";
        $newEmail = "modifie@example.com";
        $this->userManager->updateUser($userId, $newName, $newEmail);

        $updatedUser = $this->userManager->getUser($userId);
        $this->assertEquals($newName, $updatedUser['name']);
        $this->assertEquals($newEmail, $updatedUser['email']);
    }

    public function testUpdateUserWithRole(): void
    {
        // Ajout d'un utilisateur sans préciser le rôle (donc rôle par défaut "user")
        $name = "User Role";
        $email = "userrole@example.com";
        $this->userManager->addUser($name, $email);

        $array = $this->userManager->getUsers();
        $user = end($array);
        $userId = $user['id'];

        // Mise à jour en changeant le rôle
        $newName = "User Role Updated";
        $newEmail = "userroleupdated@example.com";
        $newRole = "manager";
        $this->userManager->updateUser($userId, $newName, $newEmail, $newRole);

        $updatedUser = $this->userManager->getUser($userId);
        $this->assertEquals($newRole, $updatedUser['role']);
    }
    

    public function testRemoveUser(): void
    {
        // Ajout puis suppression d’un utilisateur
        $name = "User à Supprimer";
        $email = "delete@example.com";
        $this->userManager->addUser($name, $email);
        $users = $this->userManager->getUsers();
        $user = end($users);
        $userId = $user['id'];

        $this->userManager->removeUser($userId);
        $usersAfterDelete = $this->userManager->getUsers();

        $this->assertNotContains($userId, array_column($usersAfterDelete, 'id'));
    }

    public function testGetUsers(): void
    {
        $users = $this->userManager->getUsers();
        $this->assertIsArray($users);
    }


    public function testInvalidUpdateThrowsException(): void
    {
        $this->expectException(Exception::class);
        // Tenter de mettre à jour un utilisateur inexistant (par exemple, avec un ID très élevé)
        $this->userManager->updateUser(9999, "NonExistant", "noexist@example.com");
    }

    public function testInvalidDeleteThrowsException(): void
    {
        $this->expectException(Exception::class);
        // Tenter de supprimer un utilisateur inexistant
        $this->userManager->removeUser(9999);
    }

}
