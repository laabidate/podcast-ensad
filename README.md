# Micro ENSAD Mohammedia — Plateforme de Podcasts (Édition Statique Pure)

Plateforme web de podcasts pour l'**École Nationale Supérieure d'Art et de Design de Mohammedia (ENSAD)** — Université Hassan II de Casablanca.

Développée selon les exigences strictes de l'administration et de l'utilisateur :
- **100% Statique & Sans Base de Données** : Aucune configuration SQL / SQLite requise. Les données sont stockées sous format JSON propre (`data/episodes.json`). Prêt à être déployé sur n'importe quel hébergeur web.
- **Moteur Audio YouTube (Sans Vidéo)** : La vidéo YouTube est décodée en arrière-plan de façon totalement invisible (`display: none`). L'utilisateur bénéficie d'une véritable interface de lecteur audio podcast (pochette, titre, barre de progression interactive, saut de 15 secondes, contrôle du volume et égaliseur visuel).
- **Design Light Mode UX/UI Haute Définition** : Respect strict de la charte officielle de l'ENSAD Mohammedia (Bleu ENSAD `#1F5EAD`, Bleu Foncé `#092A5C`, Jaune `#FFE52B`, Blanc `#FFFFFF`) avec animations fluides d'ondes sonores SVG.
- **Technologies utilisées** : PHP 8+, HTML5, CSS3, Bootstrap 5.3, JavaScript ES6+.

---

## 🚀 Démarrage Immédiat

### Avec le serveur PHP intégré (Actuellement en cours d'exécution) :
```bash
"C:\xampp\php\php.exe" -S localhost:8000
```
Ouvrez votre navigateur sur : **[http://localhost:8000](http://localhost:8000)**.

### Avec Apache (XAMPP / WAMP / Production) :
Copiez simplement le dossier `podcast-ensad` dans `htdocs` ou à la racine de votre hébergeur web. Le site fonctionnera immédiatement sans aucune étape d'installation.

---

## 🎵 Fonctionnalité YouTube Audio-Only

1. **Extraction Audio Invisible** : Chaque épisode est lié à un identifiant ou lien YouTube. Le script charge le flux sonore via l'API officielle YouTube en arrière-plan sans jamais afficher le cadre vidéo.
2. **Ajout Direct d'un Lien** : Un bouton **"Ajouter Podcast YouTube"** dans la barre de navigation et un champ de conversion rapide sur la page d'accueil permettent de coller n'importe quel lien YouTube pour en écouter instantanément la piste audio.

---

## 📁 Structure du Projet

```
podcast-ensad/
├── data/
│   └── episodes.json            # Base de données statique JSON
├── includes/
│   ├── header.php               # En-tête Light Mode HTML5
│   ├── navbar.php               # Barre de navigation & bouton YouTube
│   ├── footer.php               # Pied de page institutionnel sans réseaux sociaux
│   ├── player.php               # Lecteur audio persistant (YouTube Audio Bridge)
│   └── functions.php            # Fonctions utilitaires statiques
├── assets/
│   ├── css/
│   │   └── style.css            # Charte graphique Light Mode ENSAD Mohammedia
│   ├── js/
│   │   ├── app.js               # Filtres de recherche en direct
│   │   └── player.js            # Moteur de conversion audio YouTube
│   └── images/
│       ├── micro-ensad-icon.svg # Logo officiel vectoriel du microphone ENSAD
│       └── ensad-logo.png       # Logo de l'école
├── index.php                    # Page d'accueil (Hero animé, carte à la une, 4 épisodes)
├── podcasts.php                 # Catalogue des émissions par département
├── podcast-detail.php           # Détail d'une émission
├── episodes.php                 # Catalogue complet des épisodes
├── episode-detail.php           # Détail d'un épisode avec chapitres interactifs
├── about.php                    # Présentation de l'ENSAD et studio d'enregistrement
└── api/
    ├── episodes.php             # API JSON des épisodes
    ├── search.php               # API JSON recherche en direct
    └── play.php                 # API JSON comptage d'écoutes
```
