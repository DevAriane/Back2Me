
Nous allons générer un fichier README.md complet pour le backend Laravel de l'application Back2Me. Il doit inclure la description du projet, les prérequis, l'installation, la configuration, les commandes utiles, l'architecture, les endpoints API, et les instructions pour les notifications Firebase. Je vais structurer cela de manière claire.

Je vais rédiger en français, comme demandé.

Back2Me - Backend API (Laravel 12)
Application de gestion des objets trouvés pour l'IUGET.
Backend développé avec Laravel 12, fournissant une API RESTful pour l'application mobile Flutter Back2Me.

📋 Table des matières
Fonctionnalités

Prérequis

Installation

Configuration

Structure de la base de données

Authentification

API Endpoints

Notifications Firebase

Commandes utiles

Déploiement

Auteurs

🚀 Fonctionnalités
Authentification multi‑rôles (admin, étudiant, enseignant, personnel)

Gestion des objets trouvés (CRUD, photos, statuts)

Signalement d’appartenance d’un objet (claims)

Notifications push Firebase Cloud Messaging à tous les utilisateurs connectés lors de l’ajout d’un nouvel objet

Tableau de bord administrateur avec statistiques

Recherche et filtres sur les objets

API REST documentée et sécurisée (Laravel Sanctum)

🧰 Prérequis
PHP ≥ 8.2

Composer

MySQL / MariaDB

Extension PHP : pdo_mysql, mbstring, xml, bcmath, curl, gd (pour les images)

Compte Firebase (pour les notifications)

⚙️ Installation
1. Cloner le projet
bash
git clone https://github.com/votre-repo/back2me-api.git
cd back2me-api
2. Installer les dépendances PHP
bash
composer install
3. Copier le fichier d’environnement
bash
cp .env.example .env
4. Générer la clé de l’application
bash
php artisan key:generate
5. Créer la base de données
Créez une base de données MySQL nommée (exemple : back2me).
Puis modifiez le fichier .env avec vos informations de connexion :

env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=back2me
DB_USERNAME=root
DB_PASSWORD=
6. Exécuter les migrations
bash
php artisan migrate
7. Lier le stockage public
bash
php artisan storage:link
8. Lancer le serveur de développement
bash
php artisan serve
L’API sera accessible à l’adresse : http://localhost:8000/api

🔧 Configuration
Authentification (Sanctum)
Sanctum est déjà installé. Les routes protégées utilisent le middleware auth:sanctum.
Les tokens sont émis lors du login/register et doivent être inclus dans les requêtes avec l’en‑tête :

text
Authorization: Bearer {token}
CORS
Pour autoriser votre application Flutter à accéder à l’API, configurez les origines autorisées dans config/cors.php :

php
'allowed_origins' => [
    'http://localhost:8080',        // Flutter Web
    'http://10.0.2.2:8000',         // Émulateur Android
    'https://votre-domaine.com',    // Production
],
Firebase Cloud Messaging
Téléchargez le fichier JSON de votre compte de service Firebase depuis la console Firebase (Paramètres du projet > Comptes de service > Générer une nouvelle clé privée).

Placez ce fichier dans storage/app/firebase/service-account.json.

Dans .env, ajoutez :

env
FIREBASE_CREDENTIALS=storage/app/firebase/service-account.json
Le service FirebaseNotificationService enverra les notifications sur le topic all_users.
Les utilisateurs doivent s’abonner à ce topic côté Flutter après connexion.

🗄️ Structure de la base de données
Tables principales
Table	Description
users	Utilisateurs (rôle : admin ou user)
categories	Catégories d’objets (ex : Électronique, Clés…)
objets	Objets trouvés (avec statut found, returned, unclaimed)
claims	Signalements de propriété (statut pending, approved, rejected)
Relations
Un objet appartient à un utilisateur (déposant) et à une catégorie.

Un signalement lie un objet et un utilisateur (réclamant).

🔐 Authentification
Endpoints publics
Méthode	URL	Description
POST	/api/register	Inscription
POST	/api/login	Connexion
Endpoints protégés (token requis)
Méthode	URL	Description
POST	/api/logout	Déconnexion
GET	/api/user	Informations de l’utilisateur connecté
POST	/api/user/fcm-token	Enregistrement du token FCM
📦 API Endpoints
Objets
Méthode	URL	Description
GET	/api/objets	Liste paginée avec filtres
GET	/api/objets/{id}	Détail d’un objet
POST	/api/objets	Créer un objet (nécessite auth)
PUT	/api/objets/{id}	Modifier un objet (admin)
DELETE	/api/objets/{id}	Supprimer un objet (admin)
POST	/api/objets/{id}/mark-returned	Marquer comme rendu (admin)
Filtres disponibles (GET /objets) :

category_id

status (found, returned, unclaimed)

search (nom ou description)

date_from, date_to

order_by, order_dir

Catégories
Méthode	URL	Description
GET	/api/categories	Liste des catégories
GET	/api/categories/{id}	Détail d’une catégorie
Signalements (Claims)
Méthode	URL	Description
POST	/api/objets/{id}/claim	Signaler l’objet comme sien
GET	/api/claims/pending	Signalements en attente (admin)
POST	/api/claims/{id}/approve	Approuver un signalement (admin)
POST	/api/claims/{id}/reject	Rejeter un signalement (admin)
Statistiques (admin)
Méthode	URL	Description
GET	/api/stats	Statistiques complètes (dashboard)
Gestion des utilisateurs (admin)
Méthode	URL	Description
GET	/api/users	Liste des utilisateurs
POST	/api/users	Créer un utilisateur
GET	/api/users/{id}	Afficher un utilisateur
PUT	/api/users/{id}	Mettre à jour
DELETE	/api/users/{id}	Supprimer
🔔 Notifications Firebase
Principe
L’utilisateur s’abonne au topic all_users côté Flutter après connexion.

Lors de la création d’un objet (POST /objets), le backend envoie une notification sur ce topic.

Exemple de payload envoyé
json
{
  "title": "Nouvel objet trouvé",
  "body": "Un(e) Smartphone a été trouvé(e) à Amphi B.",
  "data": {
    "objet_id": "42",
    "type": "new_object"
  }
}
Gestion des tokens invalides
Le service FirebaseNotificationService nettoie automatiquement les tokens FCM devenus invalides (NotRegistered) en les supprimant de la base.

🛠️ Commandes utiles
Commande	Description
php artisan serve	Lancer le serveur de développement
php artisan migrate	Exécuter les migrations
php artisan migrate:fresh --seed	Réinitialiser la BDD et insérer des données de test
php artisan make:model Modele -m	Créer un modèle + migration
php artisan make:controller Api/ExempleController --api	Créer un contrôleur API
php artisan storage:link	Lier le stockage public
php artisan tinker	Console interactive
composer require kreait/firebase-php	Installer Firebase (déjà fait)
🌐 Déploiement
Production
Configurez votre serveur web (Nginx / Apache) pour pointer vers le dossier public.

Définissez les variables d’environnement dans .env :

APP_ENV=production

APP_DEBUG=false

APP_URL=https://votre-domaine.com

Configuration de la base de données

Informations Firebase

Optimisez Laravel :

bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
Assurez-vous que le dossier storage et bootstrap/cache ont les bons droits.

👥 Auteurs
Votre nom – @votre-github

Étudiant(s) IUGET (précisez)

📄 Licence
Ce projet est sous licence MIT – voir le fichier LICENSE pour plus de détails.

Back2Me – Retrouvez vos objets perdus en un clin d’œil.