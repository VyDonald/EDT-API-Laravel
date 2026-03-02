# 📅 API Gestion d'Emploi du Temps

Une API RESTful complète construite avec Laravel pour la gestion intelligente des emplois du temps, séances, cours, salles et notifications pour établissements scolaires.

[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![React](https://img.shields.io/badge/React-Frontend-61DAFB?style=flat&logo=react&logoColor=black)](https://github.com/VyDonald/EDT_Platforme_Web)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## 🔗 Liens du projet

- 🎨 **Frontend React**: [EDT_Platforme_Web](https://github.com/VyDonald/EDT_Platforme_Web)
- 🔧 **Backend API**: Ce repository

## 📑 Table des matières

- [Fonctionnalités](#-fonctionnalités)
- [Architecture](#-architecture)
- [Prérequis](#-prérequis)
- [Installation](#-installation)
- [Documentation API](#-documentation-api)
- [Authentification](#-authentification)
- [Gestion des rôles](#-gestion-des-rôles)
- [Exemples d'utilisation](#-exemples-dutilisation)

## ✨ Fonctionnalités

- 🔐 **Authentification JWT** sécurisée
- 👥 **Gestion multi-rôles** (Admin, Enseignant, Délégué, Étudiant)
- 📚 **Gestion des filières** et programmes
- 📖 **Gestion des cours** par l'administration
- 🏫 **Gestion des salles** et disponibilités
- ⏰ **Planification des séances** avec créneaux horaires
- 📋 **Emplois du temps** dynamiques par filière
- 🚫 **Gestion des indisponibilités** des enseignants
- 🔔 **Système de notifications** en temps réel
- ✅ **Suivi des séances** (fait, annulé, reporté)
- 👨‍🏫 **Remplacement d'enseignants** en cas d'absence
- 📊 **Visualisation** optimisée des emplois du temps

## 🏗️ Architecture

### Stack Technique

**Backend (API)**
- Laravel 10.x
- PHP 8.1+
- MySQL/PostgreSQL
- JWT Authentication (tymon/jwt-auth)
- RESTful API Design

**Frontend**
- React.js
- Repository: [EDT_Platforme_Web](https://github.com/VyDonald/EDT_Platforme_Web)

### Structure du projet

```
app/
├── Http/
│   └── Controllers/
│       ├── RoleController.php
│       ├── UtilisateurController.php
│       ├── FiliereController.php
│       ├── EmploiDuTempsController.php
│       ├── SeanceController.php
│       ├── CoursController.php
│       ├── StatutController.php
│       ├── CreneauController.php
│       ├── SalleController.php
│       ├── IndisponibiliteController.php
│       └── NotificationController.php
├── Models/
├── Middleware/
└── ...
```

## 🔧 Prérequis

- PHP >= 8.1
- Composer
- MySQL >= 5.7 ou PostgreSQL
- Laravel 10.x
- Node.js & npm (pour le frontend)

## 🚀 Installation

### 1. Cloner le repository

```bash
git clone <votre-repo-backend>
cd <nom-du-projet>
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Configuration de l'environnement

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurer la base de données

Modifiez le fichier `.env` :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=edt_database
DB_USERNAME=votre_user
DB_PASSWORD=votre_password
```

### 5. Configurer JWT

```bash
php artisan jwt:secret
```

### 6. Exécuter les migrations et seeders

```bash
php artisan migrate --seed
```

Cela créera automatiquement :
- Les rôles (Admin, Enseignant, Délégué, Étudiant)
- Les statuts (Programmé, Fait, Annulé, Reporté)
- Les créneaux horaires prédéfinis

### 7. Démarrer le serveur

```bash
php artisan serve
```

L'API sera accessible sur `http://localhost:8000/api`

### 8. Installer le Frontend

```bash
git clone https://github.com/VyDonald/EDT_Platforme_Web.git
cd EDT_Platforme_Web
npm install
npm start
```

## 📚 Documentation API

### Base URL
```
http://localhost:8000/api
```

---

## 🔐 Authentification

### 🔑 Connexion

```http
POST /login
```

**Body:**
```json
{
  "email": "admin@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": {
    "id": 1,
    "nom": "Admin",
    "email": "admin@example.com",
    "role": "admin"
  }
}
```

> 🔒 **Toutes les routes suivantes nécessitent un token JWT dans le header:**
> ```
> Authorization: Bearer {votre_token}
> ```

---

## 👥 Utilisateurs

### Lister les utilisateurs
```http
GET /utilisateurs
Authorization: Bearer {token}
```

### Créer un utilisateur
```http
POST /utilisateurs
Authorization: Bearer {token}
```

**Body:**
```json
{
  "nom": "Dupont",
  "prenom": "Jean",
  "email": "jean.dupont@example.com",
  "password": "password123",
  "role_id": 2,
  "filiere_id": 1
}
```

### Afficher un utilisateur
```http
GET /utilisateurs/{id}
Authorization: Bearer {token}
```

### Modifier un utilisateur
```http
PUT /utilisateurs/{id}
Authorization: Bearer {token}
```

### Supprimer un utilisateur
```http
DELETE /utilisateurs/{id}
Authorization: Bearer {token}
```

### Modifier son profil
```http
POST /utilisateurs/{id}/modifier-profil
Authorization: Bearer {token}
```

**Body:**
```json
{
  "nom": "Nouveau Nom",
  "prenom": "Nouveau Prénom",
  "email": "nouveau@email.com",
  "password": "nouveau_password"
}
```

---

## 🎓 Filières

### Lister les filières
```http
GET /filieres
Authorization: Bearer {token}
```

### Créer une filière
```http
POST /filieres
Authorization: Bearer {token}
```

**Body:**
```json
{
  "nom": "Licence Informatique",
  "code": "L3-INFO",
  "niveau": "L3",
  "description": "Formation en développement logiciel"
}
```

### Afficher une filière
```http
GET /filieres/{id}
Authorization: Bearer {token}
```

### Modifier une filière
```http
PUT /filieres/{id}
Authorization: Bearer {token}
```

### Supprimer une filière
```http
DELETE /filieres/{id}
Authorization: Bearer {token}
```

---

## 📖 Cours

> ⚠️ **Réservé aux administrateurs**

### Lister les cours
```http
GET /cours
Authorization: Bearer {token}
```

### Créer un cours
```http
POST /cours
Authorization: Bearer {token}
```

**Body:**
```json
{
  "nom": "Programmation Web",
  "code": "PROG-WEB-301",
  "filiere_id": 1,
  "enseignant_id": 3,
  "volume_horaire": 40
}
```

### Afficher un cours
```http
GET /cours/{id}
Authorization: Bearer {token}
```

### Modifier un cours
```http
PUT /cours/{id}
Authorization: Bearer {token}
```

### Supprimer un cours
```http
DELETE /cours/{id}
Authorization: Bearer {token}
```

---

## 📅 Emplois du Temps

### Lister les emplois du temps
```http
GET /emplois-du-temps
Authorization: Bearer {token}
```

### Créer un emploi du temps
```http
POST /emplois-du-temps
Authorization: Bearer {token}
```

**Body:**
```json
{
  "filiere_id": 1,
  "semestre": "S5",
  "annee_academique": "2024-2025",
  "date_debut": "2024-09-01",
  "date_fin": "2024-12-31"
}
```

### Afficher un emploi du temps
```http
GET /emplois-du-temps/{id}
Authorization: Bearer {token}
```

### Visualiser un emploi du temps
```http
GET /emplois-du-temps/{id}/visualiser
Authorization: Bearer {token}
```

**Response:**
```json
{
  "emploi_du_temps": {
    "id": 1,
    "filiere": "L3 Informatique",
    "semestre": "S5",
    "annee_academique": "2024-2025"
  },
  "seances": [
    {
      "id": 1,
      "cours": "Programmation Web",
      "enseignant": "Prof. Martin",
      "salle": "Salle A101",
      "creneau": "08:00 - 10:00",
      "jour": "Lundi",
      "statut": "Programmé"
    }
  ]
}
```

### Modifier un emploi du temps
```http
PUT /emplois-du-temps/{id}
Authorization: Bearer {token}
```

### Supprimer un emploi du temps
```http
DELETE /emplois-du-temps/{id}
Authorization: Bearer {token}
```

---

## 🕐 Séances

### Lister les séances
```http
GET /seances
Authorization: Bearer {token}
```

### Créer une séance
```http
POST /seances
Authorization: Bearer {token}
```

**Body:**
```json
{
  "emploi_du_temps_id": 1,
  "cours_id": 1,
  "enseignant_id": 3,
  "salle_id": 5,
  "creneau_id": 2,
  "jour": "Lundi",
  "date": "2024-09-15",
  "statut_id": 1
}
```

### Afficher une séance
```http
GET /seances/{id}
Authorization: Bearer {token}
```

### Modifier une séance
```http
PUT /seances/{id}
Authorization: Bearer {token}
```

### Supprimer une séance
```http
DELETE /seances/{id}
Authorization: Bearer {token}
```

### Annuler une séance (Enseignant)
```http
POST /seances/{id}/annuler
Authorization: Bearer {token}
```
> ⚠️ **Réservé aux enseignants**

**Body:**
```json
{
  "motif": "Absence pour raison médicale"
}
```

### Marquer une séance comme faite (Délégué)
```http
POST /seances/{id}/marquer-fait
Authorization: Bearer {token}
```
> ⚠️ **Réservé aux délégués**

### Remplacer l'enseignant (Admin)
```http
POST /seances/{id}/remplacer-enseignant
Authorization: Bearer {token}
```
> ⚠️ **Réservé aux administrateurs**

**Body:**
```json
{
  "nouvel_enseignant_id": 5,
  "motif": "Remplacement temporaire"
}
```

---

## 🏫 Salles

### Lister les salles
```http
GET /salles
Authorization: Bearer {token}
```

### Créer une salle
```http
POST /salles
Authorization: Bearer {token}
```

**Body:**
```json
{
  "nom": "Salle A101",
  "capacite": 50,
  "type": "Amphithéâtre",
  "equipements": "Projecteur, Tableau numérique"
}
```

### Afficher une salle
```http
GET /salles/{id}
Authorization: Bearer {token}
```

### Modifier une salle
```http
PUT /salles/{id}
Authorization: Bearer {token}
```

### Supprimer une salle
```http
DELETE /salles/{id}
Authorization: Bearer {token}
```

---

## 🚫 Indisponibilités

### Lister les indisponibilités
```http
GET /indisponibilites
Authorization: Bearer {token}
```

### Créer une indisponibilité
```http
POST /indisponibilites
Authorization: Bearer {token}
```

**Body:**
```json
{
  "enseignant_id": 3,
  "date_debut": "2024-09-20",
  "date_fin": "2024-09-22",
  "motif": "Formation professionnelle",
  "type": "Congé"
}
```

### Afficher une indisponibilité
```http
GET /indisponibilites/{id}
Authorization: Bearer {token}
```

### Modifier une indisponibilité
```http
PUT /indisponibilites/{id}
Authorization: Bearer {token}
```

### Supprimer une indisponibilité
```http
DELETE /indisponibilites/{id}
Authorization: Bearer {token}
```

---

## 🔔 Notifications

### Lister les notifications
```http
GET /notifications
Authorization: Bearer {token}
```

**Response:**
```json
[
  {
    "id": 1,
    "titre": "Séance annulée",
    "message": "La séance de Programmation Web du 15/09 est annulée",
    "type": "annulation",
    "lu": false,
    "created_at": "2024-09-14T10:30:00"
  }
]
```

### Créer une notification
```http
POST /notifications
Authorization: Bearer {token}
```

**Body:**
```json
{
  "utilisateur_id": 5,
  "titre": "Nouvelle séance",
  "message": "Une nouvelle séance a été programmée",
  "type": "info"
}
```

### Afficher une notification
```http
GET /notifications/{id}
Authorization: Bearer {token}
```

### Marquer comme lue
```http
POST /notifications/{id}/marquer-lue
Authorization: Bearer {token}
```

### Supprimer une notification
```http
DELETE /notifications/{id}
Authorization: Bearer {token}
```

---

## 📊 Données de référence (Lecture seule)

### Rôles
```http
GET /roles
Authorization: Bearer {token}
```

**Response:**
```json
[
  { "id": 1, "nom": "admin", "label": "Administrateur" },
  { "id": 2, "nom": "enseignant", "label": "Enseignant" },
  { "id": 3, "nom": "délégué", "label": "Délégué" },
  { "id": 4, "nom": "étudiant", "label": "Étudiant" }
]
```

### Statuts
```http
GET /statuts
Authorization: Bearer {token}
```

**Response:**
```json
[
  { "id": 1, "nom": "Programmé" },
  { "id": 2, "nom": "Fait" },
  { "id": 3, "nom": "Annulé" },
  { "id": 4, "nom": "Reporté" }
]
```

### Créneaux horaires
```http
GET /creneaux
Authorization: Bearer {token}
```

**Response:**
```json
[
  { "id": 1, "heure_debut": "08:00", "heure_fin": "10:00", "label": "08:00 - 10:00" },
  { "id": 2, "heure_debut": "10:15", "heure_fin": "12:15", "label": "10:15 - 12:15" },
  { "id": 3, "heure_debut": "14:00", "heure_fin": "16:00", "label": "14:00 - 16:00" },
  { "id": 4, "heure_debut": "16:15", "heure_fin": "18:15", "label": "16:15 - 18:15" }
]
```

---

## 🛡️ Gestion des rôles

L'API utilise un middleware de contrôle d'accès basé sur les rôles :

| Rôle | Permissions |
|------|-------------|
| 👑 **Administrateur** | Accès complet : gestion des cours, utilisateurs, remplacements d'enseignants |
| 👨‍🏫 **Enseignant** | Gérer ses indisponibilités, annuler ses séances |
| 🎓 **Délégué** | Marquer les séances comme faites, consulter l'emploi du temps de sa filière |
| 📚 **Étudiant** | Consulter l'emploi du temps de sa filière, ses notifications |

### Routes protégées par rôle

**Admin uniquement:**
- Gestion complète des cours (CRUD)
- Remplacement d'enseignants

**Enseignant:**
- Annulation de séances

**Délégué:**
- Marquer les séances comme faites

---

## 💡 Exemples d'utilisation

### Exemple avec cURL

```bash
# Connexion
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# Créer une séance
curl -X POST http://localhost:8000/api/seances \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "emploi_du_temps_id": 1,
    "cours_id": 1,
    "enseignant_id": 3,
    "salle_id": 5,
    "creneau_id": 2,
    "jour": "Lundi",
    "date": "2024-09-15"
  }'

# Visualiser un emploi du temps
curl -X GET http://localhost:8000/api/emplois-du-temps/1/visualiser \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Exemple avec JavaScript (Fetch)

```javascript
// Connexion
const login = async () => {
  const response = await fetch('http://localhost:8000/api/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      email: 'enseignant@example.com',
      password: 'password'
    })
  });
  
  const data = await response.json();
  localStorage.setItem('token', data.access_token);
  return data.access_token;
};

// Récupérer l'emploi du temps
const getEmploiDuTemps = async (id) => {
  const token = localStorage.getItem('token');
  
  const response = await fetch(`http://localhost:8000/api/emplois-du-temps/${id}/visualiser`, {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    }
  });
  
  return await response.json();
};

// Annuler une séance (Enseignant)
const annulerSeance = async (seanceId, motif) => {
  const token = localStorage.getItem('token');
  
  const response = await fetch(`http://localhost:8000/api/seances/${seanceId}/annuler`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ motif })
  });
  
  return await response.json();
};
```

### Exemple React (Frontend)

```jsx
import React, { useState, useEffect } from 'react';
import axios from 'axios';

const EmploiDuTemps = ({ filiereId }) => {
  const [emploiDuTemps, setEmploiDuTemps] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchEmploiDuTemps = async () => {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get(
          `http://localhost:8000/api/emplois-du-temps/${filiereId}/visualiser`,
          {
            headers: { Authorization: `Bearer ${token}` }
          }
        );
        setEmploiDuTemps(response.data);
      } catch (error) {
        console.error('Erreur:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchEmploiDuTemps();
  }, [filiereId]);

  if (loading) return <div>Chargement...</div>;

  return (
    <div className="emploi-du-temps">
      <h2>{emploiDuTemps?.filiere}</h2>
      <div className="seances">
        {emploiDuTemps?.seances.map(seance => (
          <div key={seance.id} className="seance-card">
            <h3>{seance.cours}</h3>
            <p>Enseignant: {seance.enseignant}</p>
            <p>Salle: {seance.salle}</p>
            <p>{seance.jour} - {seance.creneau}</p>
            <span className={`statut ${seance.statut}`}>
              {seance.statut}
            </span>
          </div>
        ))}
      </div>
    </div>
  );
};

export default EmploiDuTemps;
```

---

## 📝 Codes de statut HTTP

| Code | Description |
|------|-------------|
| `200` | ✅ Succès |
| `201` | ✅ Ressource créée |
| `204` | ✅ Suppression réussie |
| `400` | ❌ Requête invalide |
| `401` | ❌ Non authentifié / Token invalide |
| `403` | ❌ Accès refusé (permissions insuffisantes) |
| `404` | ❌ Ressource non trouvée |
| `422` | ❌ Erreur de validation |
| `500` | ❌ Erreur serveur |

---

## 🔒 Sécurité

- ✅ Authentification JWT
- ✅ Middleware de vérification des rôles
- ✅ Validation des données entrantes
- ✅ Protection CSRF
- ✅ Rate limiting
- ✅ Hashage des mots de passe (bcrypt)

---

## 🧪 Tests

```bash
# Lancer tous les tests
php artisan test

# Tests avec couverture
php artisan test --coverage

# Tests spécifiques
php artisan test --filter EmploiDuTempsTest
```

---

## 📦 Dépendances principales

```json
{
  "require": {
    "php": "^8.1",
    "laravel/framework": "^10.0",
    "tymon/jwt-auth": "^2.0"
  }
}
```

---

## 🚀 Déploiement

### Configuration de production

```bash
# Optimiser l'application
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migrations en production
php artisan migrate --force
```

### Variables d'environnement

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com

JWT_SECRET=votre_secret_jwt
JWT_TTL=60
```

---

## 📈 Roadmap

- [ ] 🔔 Notifications en temps réel (WebSockets)
- [ ] 📊 Dashboard analytics pour les admins
- [ ] 📧 Notifications par email
- [ ] 📱 Application mobile (React Native)
- [ ] 📄 Export PDF des emplois du temps
- [ ] 🔄 Synchronisation avec calendriers externes (Google Calendar, Outlook)
- [ ] 📈 Statistiques de présence
- [ ] 💬 Système de messagerie interne

---

## 🤝 Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1. Fork le projet
2. Créez votre branche (`git checkout -b feature/AmazingFeature`)
3. Committez vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

---

## 👥 Équipe

**Backend API**: Ce repository  
**Frontend React**: [VyDonald/EDT_Platforme_Web](https://github.com/VyDonald/EDT_Platforme_Web)

Développé avec ❤️ par **VyDonald**

---

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

---

## 📞 Contact

- 🌐 GitHub: [@VyDonald](https://github.com/VyDonald)
- 📧 Email: contact@edt-platform.com

---

**Développé avec 💙 pour faciliter la gestion des emplois du temps**
