# 🍔 BrasilBurger - Déploiement sur Render

## 📋 Prérequis

1. **Compte Render** : https://render.com
2. **Compte Cloudinary** : https://cloudinary.com
3. **Base de données PostgreSQL** (Render propose un plan gratuit)

## 🚀 Étapes de déploiement

### 1. Créer la base de données PostgreSQL sur Render

1. Allez sur https://dashboard.render.com
2. Cliquez sur **"New +"** → **"PostgreSQL"**
3. Configurez :
   - **Name** : `brasilburger-db`
   - **Database** : `brasilburger`
   - **User** : `brasilburger`
   - **Region** : Oregon (ou votre région préférée)
   - **Plan** : Free
4. Cliquez sur **"Create Database"**
5. Copiez l'**Internal Database URL** (vous en aurez besoin)

### 2. Déployer l'application Web

1. Sur Render, cliquez sur **"New +"** → **"Web Service"**
2. Connectez votre repository GitHub `GAMISSO/BrasilBurger`
3. Configurez :
   - **Name** : `brasilburger-symfony`
   - **Region** : Oregon (même région que la DB)
   - **Branch** : `symfony`
   - **Runtime** : Docker
   - **Plan** : Free

### 3. Configurer les variables d'environnement

Dans la section **Environment**, ajoutez :

```bash
# Symfony
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=<générer une clé aléatoire longue>
APP_SHARE_DIR=var/share

# Database (copier depuis votre PostgreSQL Render)
DATABASE_URL=<Internal Database URL>

# Cloudinary (depuis https://cloudinary.com/console)
CLOUDINARY_CLOUD_NAME=<votre_cloud_name>
CLOUDINARY_API_KEY=<votre_api_key>
CLOUDINARY_API_SECRET=<votre_api_secret>

# Messenger
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0

# Mailer
MAILER_DSN=null://null
```

### 4. Déployer

1. Cliquez sur **"Create Web Service"**
2. Render va :
   - Builder l'image Docker
   - Installer les dépendances
   - Démarrer l'application
3. Attendez que le statut passe à **"Live"** (🟢)

### 5. Vérifier le déploiement

1. Cliquez sur l'URL fournie par Render (ex: `https://brasilburger-symfony.onrender.com`)
2. Vous devriez voir la page de login
3. Connectez-vous avec : `admin1` / `Admin01`

## 🔧 Commandes utiles

### Voir les logs
```bash
# Dans le dashboard Render, cliquez sur "Logs"
```

### Redéployer manuellement
```bash
# Dans le dashboard, cliquez sur "Manual Deploy" → "Deploy latest commit"
```

### Accéder au shell du container
```bash
# Dans le dashboard, cliquez sur "Shell"
```

## 📝 Notes importantes

- **Premier déploiement** : Peut prendre 5-10 minutes
- **Plan gratuit Render** : L'application s'endort après 15 minutes d'inactivité
- **Base de données gratuite** : Limitée à 90 jours puis supprimée
- **Images Cloudinary** : Organisées dans `burgerBrasil/burgers`, `burgerBrasil/menus`, `burgerBrasil/complements`

## 🐛 Dépannage

### L'application ne démarre pas
- Vérifiez les logs dans le dashboard Render
- Assurez-vous que toutes les variables d'environnement sont définies
- Vérifiez que DATABASE_URL est l'Internal URL (pas l'External)

### Erreurs de connexion à la DB
- Assurez-vous que la base de données est dans la même région
- Utilisez l'Internal Database URL, pas l'External

### Images ne s'affichent pas
- Vérifiez les credentials Cloudinary
- Testez l'upload d'une nouvelle image depuis l'admin

## 📞 Support

Pour toute question, ouvrir une issue sur GitHub.
