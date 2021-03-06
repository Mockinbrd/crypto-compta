# 🚀 crypto-compta

Front projet UF Web en React. Thème noyau : un gestionnaire de trésorerie d'actif crypto.

Binance, coinbase, cryptocom sont certes d'excellentes plateformes pour le trading de crypto-monnaie mais elles n'offrent qu'un spectre limité de son investissement total avec très peu voire aucun indices statistiques.

C'est là que notre application entre en jeu, le but : regrouper ses investissements en crypto-monnaie dans un même lieu et offrir à l'utilisateur des données macro/micro synthétisées pour une meilleure vision de l'ensemble de ses investissements.

## 💻 Technologies

- **Back :**

  - Symfony 5.2.6
  - ApiPlatform
  - BDD SQL MySQL 5.7

- **Front :**
  - React 17.0.2
  - TailwindCSS 2.1.0
  - Twin Macro 2.3.3

## 🔱 Routing

```php
    - { path: '^/web/login', roles: IS_AUTHENTICATED_ANONYMOUSLY }
    - { path: '^/web/register', roles: IS_AUTHENTICATED_ANONYMOUSLY }
    - { path: '^/api/docs', roles: IS_AUTHENTICATED_ANONYMOUSLY }
    - { path: '^/api/auth', roles: IS_AUTHENTICATED_ANONYMOUSLY }
    - { path: '^/api/auth/refresh', roles: IS_AUTHENTICATED_ANONYMOUSLY }
    - { path: '^/api/register', roles: IS_AUTHENTICATED_ANONYMOUSLY }
    - { path: '^/web', roles: IS_AUTHENTICATED_FULLY }
    - { path: '^/api', roles: IS_AUTHENTICATED_FULLY }
    - { path: '^/', roles: IS_AUTHENTICATED_FULLY }
```

## ⚙️ Fonctionnalités et spécificités techniques

- **BACK** :

  - API :

    - Séparée en deux parties : api `/api/*` et web `/web/*`
    - Doc OpenApi `/api/docs`
    - Routes customs (ex: `/api/me`, `/api/transactions/{user}`, `/api/get_token_payloads}`...)
    - Filtres : `Pagination, PropertyFilter, SearchFilter, DateFilter`...
    - Custom fields (ex: `isMe: true`)
    - Custom validation (ex: `isValidOwner`)
    - Custom normalizer (ex: `owner:read`)
    - Entity Listeners
    - Data Persisters
    - Custom group context (ex: `admin:read`)
    - AutoGroup pour les contextes de normalization/denormalization
    - Code et messages d'erreurs adaptés

  - API CoinGecko v3:

    - Base URL : `api.coingecko.com/api/v3`
    - Très complète et performante
    - Utilisée pour récupérer toutes sortes d'informations liées aux crypto-monnaies (ex: `monnaies, valeurs, dates, icons..`)

  - Security :

    - Firewall _JWTGuard_ pour `/api/*` et _SymfonyHttpOnlyGuard_ pour `/web/*`
    - JWT Refresh token
    - Customs voter (ex: `['EDIT'] Portfolio Voter`)
    - Restrictions d'accès selon Roles ou Auth à des url patterns
    - Gestion de la propriété sur certaines ressources
    - ID utilisateurs de type Ulid

  - Base de données :

    - Node mySQL 5.7
    - Requêtes depuis API uniquement avec l'ORM Doctrine
    - MCD : [Lien de l'image](https://drive.google.com/file/d/1kFUwTS-wEeVqmd1bkeAiavbvap1JaM_F/view?usp=sharing)
    - Les jeux de données seront aussi complétés avec ceux de l'API de CoinGecko

- **FRONT** :

  - UX :
    
    - Composants responsive centrés au milieu du viewport
    - Affichage d'un loader pour indiquer à l'utilisateur un traitement en cours
    - Affichage des erreurs
    - Sidebar en slide depuis la gauche avec ajout dynamique de sous-menu si l'utilisateur est connecté
    - Affichage des composants en fadeIn pour une expérience de navigation plus agréable
    - Dark mode

  - Authentification (`/register` & `/login`) :

    - Authentification JWT avec ApiPlatform
    - JWT refresh token pour éviter de devoir retaper ses logs

  - Gestion d'utiisateurs :

    - Session utilisateur stockée en sessionStorage (donc re-auth à chaque fermeture du client web)
    - User infos requêtable avec `GET /me` dans l'API

  - Security :

    - Restrictions de certaines routes aux anonymes
    - Nettoyage des valeurs des states
    - User passé en sessionStorage au lieu du localStorage

  - Composant MyAssets (`/myassets`) :

    - Screenshot de la page : [Image](https://drive.google.com/file/d/1n1oIi6huJe8WfIjoggos6FLwVmL3e8D6/view?usp=sharing)
    - C'est ici que l'utilisateur aura des statistiques de tous ses portfolios réunis.
    - Il pourra aussi ajouter une nouvelle transaction dans un de ses portfolios en cliquant sur le +, plus de détails ci-dessous.

  - Composant Ajouter une nouvelle Transaction :

    - Formulaire dynamique avec :

      1. Text input ``coin`` avec auto-complétion qui boucle sur les données des coins de l'API CoinGecko
      2. Select input ``currency`` : ['EUR', 'USD']
      3. Number input ``amount-paid`` où l'utilisateur rentre la somme qu'il a dépensé pour acheter sa crypto-monnaie
      4. Date input ``transaction-date`` où l'utilisateur rentre la date à laquelle il a acheté sa crypto-monnaie
      5. L'utilisateur peut maintenant ``submit`` le formulaire.

    - Les données sont ensuite envoyées à l'API Symfony pour subire différents traitements :

      1. La somme d'argent dépensée est convertit en crypto à l'aide de l'API Coingecko dans le field ``token_qty_received``
      2. L'url de l'``icon`` de la crypto-monnaie est récupérée s'il est disponible
      3. La transaction est insérée en base dans le portfolio adéquate

  - Page portfolio (`/portfolio`) :

    - Screenshot de la page : [Image](https://drive.google.com/file/d/1rE2nQzaUXoHSy1sfSbcbBoPbsDIBbMy9/view?usp=sharing)
    - C'est ici que l'utilisateur aura des statistiques d'un portfolio précis.

  - Page profil utilisateur (`/profile`) :

    - Profil utilisateur regroupant ses infos personnelles

  - Styling :

    - Styles des composants avec les librairie styled-components et twin.macro pour une personnalisation plus poussées et des performances accrues.

  - Animations :

    - Implentation d'animations avec la librairie react-animations couplée avec styled-components.

  - Dark Mode :

    - Possibilité de switch de thème `["light","dark"]` depuis tout le site.
    - Le composant va d'abord vérifier si l'utilisateur n'a pas déjà par défaut des préférences de thème définit.
    - Le thème est manipulé de part contexte `<ThemeContext />` et est stocké dans le localStorage.

## 🧬 Parcours utilisateur

1.  [Landing page](https://drive.google.com/file/d/1mLFUKByyAz0E3rIbz-Yhe9AGZoqNRFwv/view?usp=sharing) L'utilisateur arrive sur la landing page, il a le choix de se connecter, ou de s'enregistrer.

    - [Page 'Se connecter'](https://drive.google.com/file/d/1prC13mJ2XQ5feiwtIvKb-JyqVZ6hJU7V/view?usp=sharing)
    - [Page 'Inscription'](https://drive.google.com/file/d/1sh9WPDFc7jADORuuXT4Y7oORdp5N1yfn/view?usp=sharing)

2.  Une fois connecté, l'utilisateur est redirigé vers sa page home, la page synthétisant tous ses assets.

    - [Page 'My Assets'](https://drive.google.com/file/d/1n1oIi6huJe8WfIjoggos6FLwVmL3e8D6/view?usp=sharing)

3.  Depuis cette page, l'utilisateur à la possibilité de créer son premier portfolio s'in n'en possède pas ou d'ajouter de nouvelles transactions dans un de ses portfolios.

    - [Formulaire ajout de transaction](https://drive.google.com/file/d/1th4w1fGFR4sR91CsWog5tyXMJ0oCCk0X/view?usp=sharing)
    - [Page d'un portfolio](https://drive.google.com/file/d/1rE2nQzaUXoHSy1sfSbcbBoPbsDIBbMy9/view?usp=sharing)

<hr>

<hr>

<hr>

## Available Scripts

  - Installations des dépendances :

  ```bash
    composer install && npm install
  ```

  - Compilation des assets : 

  ```bash
    npm run build
  ```

  - Lancer un serveur depuis la CLI symfony:

  ```bash
    symfony serve
  ```

  - Initialiser la base de données avec des données test :

  ```bash
      php bin/console d:d:c --if-not-exists && php bin/console d:s:u -f && php bin/console d:f:l
  ```
