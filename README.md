# Micro ENSAD Mohammedia - Static Podcast Website

موقع بودكاست رسمي لطلبة ENSAD Mohammedia، مبني ليعمل كموقع ثابت على GitHub Pages بدون قاعدة بيانات. يتم تعديل المحتوى محلياً من لوحة أدمن بسيطة، ثم يتم توليد نسخة HTML جاهزة للنشر داخل `dist/`.

Site web de podcasts pour les etudiants de l'ENSAD Mohammedia, concu pour etre publie comme site statique sur GitHub Pages, sans base de donnees. Le contenu est gere localement via un panneau admin, puis exporte en HTML dans `dist/`.

![Home page](docs/screenshots/home.png)

## العربية

### ماذا يقدم الموقع؟

- صفحة رئيسية بتصميم سينمائي، أزرار تشغيل، إحصاءات، وأحدث الحلقات.
- صفحة كل الحلقات مع البحث والتصفية.
- صفحة البودكاست / السلاسل مع بطاقات لكل إصدار.
- صفحة تفاصيل لكل حلقة، فيها الوصف، المشاركون، الفصول، وزر التشغيل.
- صفحة تفاصيل لكل بودكاست تعرض حلقاته.
- صفحة "حول" للتعريف بالمشروع والمؤسسة.
- مشغل صوت ثابت يعتمد على YouTube في الخلفية بدون عرض الفيديو.
- لوحة أدمن محلية فقط لإضافة وتعديل البودكاست والحلقات.
- نشر آلي على GitHub Pages عبر GitHub Actions.

### كيف يعمل بدون قاعدة بيانات؟

كل البيانات موجودة في:

```text
data/episodes.json
data/site-settings.json
```

لوحة الأدمن تعدل هذه الملفات محلياً فقط. عند الانتهاء، يتم تشغيل أداة البناء:

```bash
"C:\xampp\php\php.exe" tools/build-static.php
```

الأداة تنشئ مجلد `dist/` يحتوي فقط على HTML وCSS وJavaScript والصور، بدون `admin/` وبدون `data/` وبدون `api/`.

### تشغيل المشروع محلياً

```bash
"C:\xampp\php\php.exe" -S 127.0.0.1:8000 -t .
```

ثم افتح:

```text
http://127.0.0.1:8000
```

لوحة الأدمن:

```text
http://127.0.0.1:8000/admin/login.php
```

اللوحة محمية لتعمل من `localhost` أو `127.0.0.1` فقط. كلمة المرور الافتراضية للتطوير المحلي هي `ensad2026`، ويمكن تغييرها عبر المتغير:

```bash
MICRO_ENSAD_ADMIN_PASSWORD
```

### النشر على GitHub Pages

المشروع يحتوي على Workflow جاهز:

```text
.github/workflows/pages.yml
```

عند الدفع إلى فرع `main`، يقوم GitHub Actions بـ:

- تحميل ملفات Git LFS.
- توليد نسخة `dist/`.
- رفعها إلى GitHub Pages.
- نشر الموقع كصفحات HTML ثابتة.

في إعدادات GitHub، فعّل Pages من:

```text
Settings -> Pages -> Source: GitHub Actions
```

## Francais

### Fonctionnalites

- Accueil editorial avec hero, episode mis en avant, statistiques et animations.
- Catalogue complet des episodes avec recherche et filtrage.
- Catalogue des emissions / podcasts.
- Page detail pour chaque episode avec description, contributeurs, chapitres et lecture.
- Page detail pour chaque emission avec ses episodes.
- Page "A propos" pour presenter le projet ENSAD.
- Lecteur audio persistant base sur YouTube en arriere-plan.
- Admin local pour gerer les emissions et les episodes, sans base de donnees.
- Export statique compatible GitHub Pages.

### Architecture

```text
podcast-ensad/
├── admin/                  # Admin local, non publie dans dist/
├── assets/                 # CSS, JavaScript, images et audio
├── data/                   # Donnees JSON sources
├── includes/               # Fragments PHP partages
├── tools/
│   ├── build-static.php    # Genere le site statique
│   └── capture-screenshots.cjs
├── dist/                   # Sortie statique generee, ignoree par Git
├── .github/workflows/      # Deploiement GitHub Pages
└── *.php                   # Pages sources
```

### Pages principales

| Page source | Page statique | Role |
| --- | --- | --- |
| `index.php` | `index.html` | Accueil et episode vedette |
| `episodes.php` | `episodes.html` | Tous les episodes |
| `podcasts.php` | `podcasts.html` | Toutes les emissions |
| `episode-detail.php?id=X` | `episode-X.html` | Detail d'un episode |
| `podcast-detail.php?id=X` | `podcast-X.html` | Detail d'une emission |
| `about.php` | `about.html` | Presentation du projet |
| `welcome.php` | `welcome.html` | Page de bienvenue |

### Commandes utiles

Verifier PHP:

```bash
"C:\xampp\php\php.exe" -l index.php
```

Construire la version statique:

```bash
"C:\xampp\php\php.exe" tools/build-static.php
```

Construire avec une URL publique pour le sitemap:

```bash
"C:\xampp\php\php.exe" tools/build-static.php --site-url="https://votre-compte.github.io/podcast-ensad"
```

Mettre a jour les captures d'ecran:

```bash
node tools/capture-screenshots.cjs
```

## Screenshots / لقطات من الموقع

### Accueil / الصفحة الرئيسية

![Home](docs/screenshots/home.png)

### Episodes / الحلقات

![Episodes](docs/screenshots/episodes.png)

### Podcasts / البودكاست

![Podcasts](docs/screenshots/podcasts.png)

### Detail episode / تفاصيل حلقة

![Episode detail](docs/screenshots/episode-detail.png)

### A propos / حول المشروع

![About](docs/screenshots/about.png)

### Mobile

![Mobile home](docs/screenshots/mobile-home.png)

## Notes importantes

- `dist/` est genere automatiquement et reste ignore par Git.
- `admin/`, `data/` et `api/` ne sont pas copies dans la version publiee.
- Les images et fichiers audio sont suivis avec Git LFS; le workflow GitHub Pages active `lfs: true`.
- Les sauvegardes locales de l'admin sont placees dans `data/backups/` et ignorees par Git.
