# 🌍 PROJET : **B-SSAHTY**

### Plateforme communautaire sportive géolocalisée

---

## 🧠 CONCEPT GLOBAL

**B-SSAHTY** est une application web sociale et géolocalisée qui permet aux utilisateurs de :

* Trouver des partenaires sportifs à proximité
* Poser des questions et interagir avec la communauté
* Découvrir et évaluer des lieux sportifs (spots)
* Organiser des événements sportifs en temps réel
* Communiquer via messagerie privée

👉 L’objectif est de **connecter les sportifs dans la vraie vie**, pas seulement en ligne.

---

## 👥 ACTEURS DU SYSTÈME

### 🔹 Utilisateur (User)

Un utilisateur peut :

* Créer des questions (ex: “Qui veut jouer foot ?”)
* Commenter et réagir (like/dislike)
* Ajouter des amis
* Discuter en privé
* Participer à des événements
* Consulter les spots sur la carte

---

### 🔹 Administrateur (Admin)

* Gérer les utilisateurs (ban/unban)
* Supprimer contenu abusif
* Valider ou refuser les spots

---

## ⚙️ FONCTIONNALITÉS PRINCIPALES

---

### 1. ❓ Système de Questions (Core)

* Publication de questions sportives
* Catégorisation par sport :

  * Football, Tennis, Running…
* Interaction via :

  * Commentaires
  * Réactions (Like / Dislike)

---

### 2. 🗺️ Carte Interactive & Spots

* Visualisation des lieux sportifs
* Ajout de nouveaux spots
* Validation par admin
* Recherche de spots proches

👉 Utilisation de coordonnées GPS (Point)

---

### 3. 📅 Événements Sportifs

* Transformation d’une question en événement
* Définition :

  * Date
  * Nombre de participants
* Statuts :

  * OPEN / FULL / CLOSED

---

### 4. 🤝 Réseau Social (Friendship)

* Ajout d’amis
* Gestion des relations :

  * Demande
  * Acceptation
  * Blocage

👉 Chaque utilisateur possède :

* Une liste d’amis
* Un réseau social interne

---

### 5. 💬 Messagerie Privée

* Chat en temps réel
* Conversations entre amis
* Messages avec statut "lu / non lu"

---

### 6. ❤️ Interactions Sociales

* Réactions :

  * LIKE
  * DISLIKE
* Sur :

  * Questions
  * Commentaires

---

## 🧱 ARCHITECTURE TECHNIQUE

* Backend : **Laravel (API)** (PHP 8.4-laravel 13 - controler base + models + migration + formRequest)
* Frontend : **Vue.js 3** (javascript + vue-router + pinia + tailwindcss)
* Base de données : **PostgreSQL + PostGIS**
* Temps réel : **WebSockets**
* Carte : **Leaflet + OpenStreetMap**
* Infra : **Docker + Nginx**

---

## 🌐 DIMENSION INNOVANTE

Ce projet combine :

* 📍 **Géolocalisation**
* 💬 **Réseau social**
* ⚡ **Temps réel**
* 🏃 **Activité physique réelle**

👉 Contrairement aux réseaux sociaux classiques,
**B-SSAHTY pousse à sortir et faire du sport dans la vraie vie.**

---

## 🎯 OBJECTIF FINAL

Créer une plateforme qui :

* Réduit l’isolement sportif
* Facilite l’organisation spontanée
* Valorise les espaces sportifs locaux
* Crée une vraie communauté

---

## 🧩 RÉSUMÉ SIMPLE (1 phrase)

👉
**B-SSAHTY est un réseau social sportif géolocalisé qui permet de rencontrer des partenaires, organiser des matchs et découvrir des spots autour de soi.**


