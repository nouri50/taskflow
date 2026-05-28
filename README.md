# TaskFlow — Application Kanban

> Application de gestion de projets type Trello, développée pour enrichir mon portfolio et rester actif techniquement.



##  Stack Technique

**Backend**
- Symfony 7.4 — API REST
- JWT Authentication (LexikJWTAuthenticationBundle)
- Doctrine ORM — MySQL
- Mercure (temps réel)
- NelmioCorsBundle

**Frontend**
- Angular 20 (Standalone Components)
- Angular CDK — Drag & Drop
- RxJS — Gestion des flux
- SCSS

**Outils**
- Docker
- Git / GitHub
- Figma (maquettes)
- Postman (tests API)

##  Fonctionnalités

-  Authentification JWT (inscription / connexion)
-  Dashboard avec gestion des boards
-  Vue Kanban avec colonnes (À faire / En cours / Terminé)
-  Cartes complètes :
  - Titre, description, priorité, date limite
  - Checklist avec progress bar
  - Commentaires
  - Labels colorés
  - Assignation de membres
-  Drag & Drop entre colonnes
-  Invitation de membres par email
-  Modification / Suppression boards et cartes



##  Installation

### Prérequis
- PHP 8.4
- Node.js 22
- MySQL
- Symfony CLI

### Backend

```bash
cd taskflow-backend

# Installer les dépendances
composer install

# Configurer la base de données
cp .env .env.local
# Modifier DATABASE_URL dans .env.local

# Créer la base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Générer les clés JWT
php bin/console lexik:jwt:generate-keypair

# Lancer le serveur
symfony server:start --no-tls
```

### Frontend

```bash
cd taskflow-frontend

# Installer les dépendances
npm install

# Lancer l'application
ng serve
```

L'application est accessible sur `http://localhost:4200`





##  Objectif

Projet personnel développé pour :
- Pratiquer Symfony + Angular en dehors des projets professionnels
- Élargir mon profil PHP/Symfony + React vers Angular
- Avoir un outil Kanban personnel indépendant de Trello/Jira
