security.php
$isHex64 => à supprimer si on n'utilise pas de token en bin2hex

PLAN DU SITE

imdb-project/
├── public/                          # Point d'entrée web (document root)
│   ├── index.php                    # Page d'accueil                       Marie
│   ├── search.php                   # Page de recherche                      |
│   ├── category.php                 # Pages catégories (action/drama)        |
│   ├── movie.php                    # Détails d'un film                      |
│   ├── director.php                 # Films par réalisateur                  |
│   ├── cart.php                     # Panier                                 |
│   ├── orders.php                    # Historique des achats                 |
│   ├── login.php                    # Connexion                            Kenza
│   ├── register.php                 # Inscription                            |
│   ├── logout.php                   # Déconnexion                            |
│   ├── assets/                      # Ressources statiques                 Commun
│   │   ├── css/
│   │   │   └── style.css
│   │   ├── js/
│   │   │   └── main.js
│   │   └── images/
│   │       └── movies/              # Images des films
│   └── .htaccess                    # Configuration Apache (optionnel)
│
├── actions/                                                              Kenza
│       ├── cart_add.php                                                    |
│       ├── cart_remove.php                                                 |
│       └── cart_clear.php                                                  |
│ 
├── includes/                    # Fichiers réutilisables                 
│   ├── header.php                                                         Marie
│   ├── footer.php                                                          |
│   ├── navigation.php                                                      |
│   ├── db.php                   # Configuration BDD                      Kenza
│   ├── config.php               # Fonctions générales                      |
│   ├── security.php             # Validation/sanitization                  |
│   ├── auth.php                 # Gestion de session                       |
│   └── functions.php            # Fonctions helpers générales            Commun
│
├── src/                             # Code PHP organisé
│   ├── controllers/                 # Logique métier (RECOMMANDÉ)
│   │   ├── MovieController.php                                            Marie
│   │   ├── DirectorController.php                                           |
│   │   ├── SearchController.php                                             |
│   │   ├── UserController.php                                             Kenza
│   │   └── CartController.php                                               |
│   │
│   └── models/                      # Classes métier
│       ├── Movie.php                                                      Marie
│       ├── User.php                                                       Kenza
│       ├── Director.php                                                   Marie
│       └── Cart.php                                                       Kenza
│
├── database/                                                              Marie
│   ├── schema.sql                   # Structure de la BDD                   |
│   ├── seed.sql                     # Données de test                       |
│   └── README.md                    # Instructions d'installation BDD        
│
├── docs/                            # Documentation
│   ├── installation.md
│   ├── database-schema.md
│   └── user-guide.md
│
├── .env
├── .env.example
├── .gitignore
├── composer.json                    # Si vous utilisez Composer
└── README.md                        # Documentation principale


## 📊 Répartition des tâches clarifiée

### **Kenza (Authentification + Panier + Config)**
```
✅ Système d'authentification complet
   - includes/db.php
   - includes/config.php
   - includes/auth.php
   - includes/security.php
   - public/login.php
   - public/register.php
   - public/logout.php
   - src/models/User.php

✅ Système de panier
   - actions/cart_add.php
   - actions/cart_remove.php
   - actions/cart_clear.php
   - src/models/Cart.php

✅ Configuration
   - .env.example
```

### **Marie (Films + Interface + BDD)**
```
✅ Pages d'affichage films
   - public/index.php
   - public/search.php
   - public/category.php
   - public/movie.php
   - public/director.php
   - public/cart.php (affichage)
   - public/orders.php (affichage)

✅ Modèles films
   - src/models/Movie.php
   - src/models/Director.php

✅ Templates
   - includes/header.php
   - includes/footer.php
   - includes/navigation.php

✅ Base de données
   - database/schema.sql
   - database/seed.sql
```

### **Commun**
```
✅ Styles et scripts
   - assets/css/style.css
   - assets/js/main.js (si nécessaire)

✅ Fonctions partagées
   - includes/functions.php

✅ Documentation
   - README.md
   - .gitignore
🚨 Points d'attention
1. Dépendances entre vous deux
Kenza doit finir EN PREMIER :

includes/db.php → utilisé par Marie pour toutes les pages
includes/auth.php → utilisé par Marie pour cart.php, orders.php
src/models/User.php → utilisé par Marie pour afficher username

Marie peut commencer en parallèle :

Structure HTML/CSS
Base de données
Pages statiques (index, search sans fonctionnalité)

🎯 Ordre de développement recommandé
Semaine 1

Kenza : config.php, db.php, schema.sql de base
Marie : Structure HTML, header/footer, CSS de base
Ensemble : Valider la connexion BDD

Semaine 2

Kenza : Login/Register complet
Marie : Pages films (index, movie, director) avec données statiques
Ensemble : functions.php partagées

Semaine 3

Kenza : Système panier complet
Marie : Intégrer BDD dans les pages, search, categories
Ensemble : Tests et debug

Semaine 4

Les deux : Finalisation, responsive, sécurité, documentation