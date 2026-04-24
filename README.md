# La Compagnie des Archers des Albères — Website Redesign

Refonte complète du site web d’un club de tir à l’arc, avec modernisation de l’interface, amélioration de l’expérience utilisateur et mise en place d’un back-office administrable.

---

## 🎯 Objectif du projet

Moderniser un site existant devenu difficile à maintenir :
- Interface vieillissante
- Absence d’administration simple
- Manque de structure technique

👉 Cette refonte apporte :
- Une architecture propre et scalable
- Une expérience utilisateur moderne
- Un système de gestion de contenu complet

---

## ✨ Améliorations apportées

### 🎨 Front-end
- Design moderne et responsive
- Slider dynamique en page d’accueil
- Sections structurées (club, équipe, actualités)
- Navigation simplifiée

### 📸 Galerie photos
- Introduction d’un système d’albums
- Gestion des photos par album
- Interface claire et évolutive

### 📰 Actualités
- Catégorisation (podium, événements, club…)
- Pagination
- Mise en avant du contenu

### 🔐 Back-office (EasyAdmin)
- Création / édition d’articles
- Gestion des albums et photos
- Gestion des membres, partenaires, statistiques
- Paramétrage du site

### ⚙️ Backend
- Refonte complète de l’architecture Symfony
- Relations Doctrine optimisées
- Code structuré et maintenable

---

## 🧱 Stack technique

- **Symfony 7**
- **PHP 8.4**
- **PostgreSQL**
- **Doctrine ORM**
- **EasyAdmin**
- **Twig**
- **CSS custom + Bootstrap**
- **Docker**
- **Zenstruck Foundry (fixtures)**

---

## 🧠 Architecture

### Entités principales

- `Article` → contenu éditorial
- `Album` → regroupement de photos
- `Photo` → images liées aux albums
- `User` → authentification
- `TeamMember`, `Partner`, `ClubStat`, `Setting`

### Relations

- Article → (optionnel) lié à un album
- Album → Photos (1:N)

---

## ⚙️ Installation

### 1. Cloner
```bash
git clone https://github.com/ton-repo/archers-alberes.git
cd archers-alberes
