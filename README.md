# Back2Me

Application de gestion d'objets trouves et reclames, avec:
- une interface web (Laravel + Livewire)
- une API REST (Laravel + Sanctum)
- des notifications (base de donnees + Firebase FCM)
- un module de commissions lors de la restitution

Le projet est structure autour du dossier `back2me-api`.

## 1. Fonctionnalites principales

- Authentification: inscription, connexion, deconnexion.
- Gestion des objets trouves: creation, liste, filtres, detail.
- Reclamation d'objet par un utilisateur (preuve par lien ou fichier).
- Validation/rejet des reclamations par un admin.
- Validation finale de restitution de l'objet.
- Calcul et suivi des commissions (trouveur, surveillant, application).
- Notifications internes + push Firebase.
- Dashboard avec statistiques et evolution.

## 2. Roles

- `user`: peut declarer un objet trouve, consulter la liste, faire une reclamation.
- `admin`: peut gerer les utilisateurs, approuver/rejeter les reclamations, marquer un objet rendu, consulter les commissions et statistiques.

## 3. Stack technique

- PHP 8.2+
- Laravel 12
- Livewire 3
- MySQL 8.4
- Nginx
- Docker Compose
- Firebase Admin SDK (`kreait/firebase-php`)

## 4. Architecture du repo

- `readme.md`: documentation principale affichee sur GitHub.
- `docker-compose.yml`: orchestration Docker (app, nginx, db).
- `back2me-api/`: application Laravel.
  - `routes/web.php`: routes interface web.
  - `routes/api.php`: routes API REST.
  - `app/Livewire`: pages et actions web.
  - `app/Http/Controllers/Api`: endpoints API.
  - `app/Services`: logique notifications et commissions.

## 5. Prerequis

- Docker + Docker Compose
- (Optionnel hors Docker) PHP 8.2+, Composer, Node.js 20+, MySQL 8+

## 6. Installation rapide avec Docker (recommande)

1. Se placer a la racine du projet:
```bash
cd ~/Back2Me
```

2. Copier l'environnement Docker:
```bash
cp back2me-api/.env.docker back2me-api/.env
```

3. Demarrer les conteneurs:
```bash
docker compose up -d --build
```

4. Generer la cle Laravel:
```bash
docker compose exec app php artisan key:generate
```

5. Lancer les migrations:
```bash
docker compose exec app php artisan migrate
```

6. Inserer les donnees de base indispensables (filieres, niveaux, categories, admin):
```bash
docker compose exec app php artisan db:seed --class=FiliereSeeder
docker compose exec app php artisan db:seed --class=NiveauSeeder
docker compose exec app php artisan db:seed --class=CategorySeeder
docker compose exec app php artisan db:seed --class=AdminSeeder
```

7. Creer le lien de stockage public:
```bash
docker compose exec app php artisan storage:link
```

8. Ouvrir l'application:
- Web: `http://localhost:8000`
- API: `http://localhost:8000/api`

## 7. Initialiser les donnees en un seul reset

Si vous voulez tout reinitialiser:
```bash
docker compose exec app php artisan migrate:refresh
docker compose exec app php artisan db:seed --class=FiliereSeeder
docker compose exec app php artisan db:seed --class=NiveauSeeder
docker compose exec app php artisan db:seed --class=CategorySeeder
docker compose exec app php artisan db:seed --class=AdminSeeder
```

## 8. Comptes et acces

Apres `AdminSeeder`:
- email admin: `surveillant@gmail.com`
- mot de passe admin: `surveillant123`

Les utilisateurs standards se creent via la page `/register` ou l'API `/api/register`.

## 9. Flux du site web (pas a pas)

### Flux A: inscription et connexion utilisateur

1. Aller sur `/register`.
2. Renseigner nom, email, telephone, filiere, niveau, mot de passe.
3. Validation -> connexion automatique -> redirection `/dashboard`.
4. Connexion ulterieure via `/login`.

### Flux B: declaration d'un objet trouve

1. Se connecter.
2. Aller sur `/objets`.
3. Ouvrir le formulaire de creation (modal).
4. Saisir categorie, nom, lieu, date, description, photo (optionnelle).
5. Enregistrer.
6. L'objet est cree avec statut `found` et des notifications sont envoyees.

### Flux C: reclamation d'un objet

1. Ouvrir la fiche objet `/objets/{id}`.
2. Cliquer sur reclamer.
3. Fournir au moins une preuve:
- lien (`proof_link`) ou
- fichier (`proof_file`).
4. Renseigner le prix estime de l'objet (`object_price`).
5. Soumettre -> statut reclamation `pending`.

### Flux D: traitement admin des reclamations

1. Admin ouvre `/claims/pending`.
2. Consulte les dossiers et preuves.
3. Approuve ou rejette.
4. En cas d'approbation:
- la commission est calculee et enregistree,
- l'utilisateur reclamant peut etre notifie.

### Flux E: validation de restitution

1. Admin ouvre le detail de reclamation `/claims/{id}`.
2. Si la reclamation est `approved`, il valide "objet rendu".
3. Le statut de l'objet passe a `returned`.
4. Notification de restitution envoyee aux utilisateurs.

### Flux F: commissions (admin)

1. Aller sur `/commissions`.
2. Voir les montants cumules par trouveur (`accrued`).
3. Valider un paiement -> statut `paid`, date `paid_out_at`.

### Flux G: notifications utilisateur

1. Aller sur `/notifications`.
2. Consulter les notifications lues/non lues.
3. Marquer toutes comme lues.

## 10. Regles de commission

Dans `CommissionService`:
- Commission totale = 25% du prix de l'objet.
- Si prix >= 20 000 FCFA, commission plafonnee a 5 000 FCFA.
- Repartition:
  - 50% au trouveur
  - 25% au surveillant
  - 25% a l'application

## 11. API REST (principales routes)

Base URL: `http://localhost:8000/api`

### Auth

- `POST /register`
- `POST /login`
- `POST /logout` (token requis)
- `GET /user` (token requis)
- `POST /user/fcm-token` (token requis)

### Objets

- `GET /objets`
- `POST /objets`
- `GET /objets/{objet}`
- `PUT /objets/{objet}` (admin)
- `DELETE /objets/{objet}` (admin)
- `POST /objets/{objet}/mark-returned` (admin)

Filtres utiles sur `GET /objets`:
- `category_id`
- `status` (`found|returned|unclaimed`)
- `search`
- `date_from`, `date_to`
- `order_by`, `order_dir`, `per_page`

### Reclamations

- `POST /objets/{objet}/claim`
- `GET /claims/pending` (admin)
- `POST /claims/{claim}/approve` (admin)
- `POST /claims/{claim}/reject` (admin)

### Categories

- `GET /categories`
- `GET /categories/{category}`

### Admin

- `GET /stats`
- `GET/POST/PUT/DELETE /users...`

## 12. Exemple d'appel API (login + token)

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"surveillant@gmail.com","password":"surveillant123"}'
```

Puis utiliser le token:

```bash
curl http://localhost:8000/api/objets \
  -H "Accept: application/json" \
  -H "Authorization: Bearer VOTRE_TOKEN"
```

## 13. Variables d'environnement importantes

Dans `back2me-api/.env`:
- `APP_URL`
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `FILESYSTEM_DISK=public`
- `FIREBASE_CREDENTIALS=storage/app/firebase/service-account.json`

Important:
- en Docker (dans le conteneur app), utiliser `DB_HOST=db` et `DB_PORT=3306`.
- depuis la machine host, MySQL est expose en `127.0.0.1:3307`.

## 14. Commandes utiles

```bash
# Etat des conteneurs
docker compose ps

# Logs
docker compose logs -f app
docker compose logs -f db

# Tests
docker compose exec app php artisan test

# Arret
docker compose down
```

## 15. Depannage rapide

### Erreur SQLSTATE[HY000] [2002] Connection refused

Verifier que MySQL est `healthy`:
```bash
docker compose ps
```

Si besoin, recreer le conteneur app pour reprendre la bonne config env:
```bash
docker compose up -d --force-recreate app
```

### Permission denied sur `storage/logs/laravel.log`

Executer:
```bash
docker compose exec app sh -lc 'chown -R www-data:www-data storage bootstrap/cache && chmod -R ug+rw storage bootstrap/cache'
```

## 16. Lancement hors Docker (optionnel)

Dans `back2me-api`:
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed --class=FiliereSeeder
php artisan db:seed --class=NiveauSeeder
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=AdminSeeder
php artisan storage:link
npm install
npm run dev
php artisan serve
```

## 17. A faire / ameliorations suggerees

- Activer les seeders de base directement dans `DatabaseSeeder` pour simplifier l'onboarding.
- Ajouter une documentation Swagger/OpenAPI.
- Ajouter des tests fonctionnels API et Livewire sur les flux critiques.

---

Si vous voulez, je peux aussi vous preparer une version courte du README (marketing) + une version technique detaillee separee dans `docs/`.
