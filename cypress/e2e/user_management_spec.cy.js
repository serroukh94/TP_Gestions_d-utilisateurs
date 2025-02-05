describe('Gestion des utilisateurs', () => {
    it('Ajoute, modifie et supprime un utilisateur', () => {
        // Accéder à la page
        cy.visit('/');

        // Ajouter un utilisateur
        cy.get('#name').type('Cypress User');
        cy.get('#email').type('cypress@example.com');
        cy.get('#userForm').submit();

        // Vérifier l'ajout dans la liste
        cy.contains('Cypress User').should('exist');

        // Modifier l’utilisateur
        cy.contains('Cypress User').parent().find('button:contains("✏️")').click();
        cy.get('#name').clear().type('Cypress User Modifié');
        cy.get('#email').clear().type('cypressmod@example.com');
        cy.get('#userForm').submit();

        // Vérifier la modification
        cy.contains('Cypress User Modifié').should('exist');

        // Supprimer l’utilisateur
        cy.contains('Cypress User Modifié').parent().find('button:contains("❌")').click();

        // Vérifier la suppression
        cy.contains('Cypress User Modifié').should('not.exist');
    });
});
