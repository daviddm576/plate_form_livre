
# 📚 Plateforme Livre — Guide d’installation

## 📖 Description

Ce projet est une application web de gestion et de vente de livres développée en **PHP + MySQL**.
Elle permet notamment :

* l’ajout de livres
* l’administration
* la gestion panier
* les opérations sur les livres

---

## ⚙️ Prérequis

Avant d’exécuter le projet, assure-toi d’avoir installé :

* XAMPP (ou tout serveur Apache + MySQL + PHP)
* Navigateur web
* Git

---

## 📥 Installation du projet

### 1️⃣ Cloner le repository

```bash
git clone https://github.com/USERNAME/plate_forme_livre.git
```
generalement le fichier ou dossier creer ca se trouve dans le bureau les gars
---

### 2️⃣ Placer le dossier dans htdocs

Copie le dossier cloné dans :

```
C:\xampp\htdocs\
```

Tu dois obtenir :

```
C:\xampp\htdocs\plate_forme_livre
```

---

### 3️⃣ Démarrer le serveur

Lance **XAMPP Control Panel** puis démarre :

* Apache
* MySQL

---

### 4️⃣ Créer la base de données

Ouvre :

```
http://localhost/phpmyadmin
```

Créer une base nommée :

```
livre_v
```

---

### 5️⃣ Importer la base (si fichier SQL fourni)

Si un fichier `.sql` est présent :

1. Clique sur la base `livre_v`
2. Onglet **Importer**
3. Sélectionner le fichier SQL
4. Exécuter

---

### 6️⃣ Lancer le projet

Dans ton navigateur :

```
http://localhost/plate_forme_livre
```

---

## 🛠 Configuration base de données

Si problème de connexion, vérifier le fichier de config :

```php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "livre_v";
```

---

## ❗ Problèmes fréquents

### Erreur SQLSTATE 2002

➡ MySQL non démarré → lancer MySQL dans XAMPP

### Unknown database

➡ Créer la base `livre_v`

### Page blanche

➡ Activer les erreurs PHP ou vérifier Apache

---

## 👨‍💻 Collaboration

Pour contribuer :

```bash
git pull
git checkout -b ma-branche
```

Puis push + Pull Request.

---

## 📌 Auteur

Projet développé dans le cadre d’un travail collaboratif académique.


Je peux aussi adapter le README selon :
👉 projet scolaire
👉 projet open source
👉 portfolio

Lequel veux-tu ?
