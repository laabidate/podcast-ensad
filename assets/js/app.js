/**
 * MICRO ENSAD - Script Principal d'Interactions Cinématiques & Audio
 * Identité Visuelle Sombre Éditoriale ENSAD Casablanca
 * - Mode Sombre / Clair
 * - Filtrage dynamique instantané par thématique (sans rechargement)
 * - Recherche en direct dans la modale et sur le catalogue
 * - Partage Web / Copie Presse-Papier
 * - Modale de Détails avec Chapitres et Lecteur Audio
 */

document.addEventListener('DOMContentLoaded', () => {
    initThemeToggle();
    initDynamicFiltering();
    initLiveSearch();
    initModalLiveSearch();
    initNavbarBehaviors();
    initShareButtons();
    initEpisodeModal();
});

/* ==========================================================
   1. GESTION DU THÈME (DARK / LIGHT MODE)
   ========================================================== */
function initThemeToggle() {
    const desktopBtn     = document.getElementById('themeToggleBtn');
    const bottomTabBtn   = document.getElementById('tabMobileThemeToggle');
    const desktopIcon    = document.getElementById('themeToggleIcon');
    const bottomTabIcon  = document.getElementById('mobileTabThemeIcon');
    const bottomTabLabel = document.getElementById('mobileTabThemeLabel');
    const htmlEl         = document.documentElement;

    // Thème sombre par défaut pour l'esthétique cinématique
    const savedTheme = localStorage.getItem('ensad_theme');
    const currentTheme = savedTheme || 'dark';

    applyTheme(currentTheme);

    function toggleTheme() {
        const current = htmlEl.getAttribute('data-bs-theme') || 'dark';
        const next = current === 'dark' ? 'light' : 'dark';
        applyTheme(next);
        try {
            localStorage.setItem('ensad_theme', next);
        } catch (e) {}
    }

    function applyTheme(theme) {
        htmlEl.setAttribute('data-bs-theme', theme);
        htmlEl.setAttribute('data-theme', theme);

        const iconText = theme === 'dark' ? 'light_mode' : 'dark_mode';
        if (desktopIcon) desktopIcon.textContent = iconText;
        if (bottomTabIcon) bottomTabIcon.textContent = iconText;
        if (bottomTabLabel) bottomTabLabel.textContent = theme === 'dark' ? 'Clair' : 'Sombre';
        if (bottomTabBtn) {
            bottomTabBtn.setAttribute('title', theme === 'dark' ? 'Passer en Mode Clair' : 'Passer en Mode Sombre');
        }
    }

    if (desktopBtn) desktopBtn.addEventListener('click', toggleTheme);
    if (bottomTabBtn) bottomTabBtn.addEventListener('click', toggleTheme);
}

/* ==========================================================
   2. FILTRAGE DYNAMIQUE INSTANTANÉ PAR THÉMATIQUE
   ========================================================== */
function initDynamicFiltering() {
    const filterButtons = document.querySelectorAll('[data-filter-category]');
    const episodeCards  = document.querySelectorAll('.filterable-episode-card');

    if (filterButtons.length === 0 || episodeCards.length === 0) return;

    filterButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const targetCategory = btn.getAttribute('data-filter-category') || 'all';

            // Mettre à jour l'état actif des pilules
            filterButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Filtrer les cartes avec transition visuelle
            let visibleCount = 0;
            episodeCards.forEach(card => {
                const cardCat = card.getAttribute('data-category') || '';
                const match = (targetCategory === 'all' || cardCat.trim().toLowerCase() === targetCategory.trim().toLowerCase());

                if (match) {
                    card.style.display = '';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.96)';
                    card.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
                    requestAnimationFrame(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'scale(1)';
                    });
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
}

/* ==========================================================
   3. RECHERCHE EN DIRECT SUR LE CATALOGUE (EPISODES.PHP)
   ========================================================== */
function initLiveSearch() {
    const searchInput = document.getElementById('liveCatalogueSearch');
    if (!searchInput) return;

    const cards = document.querySelectorAll('.searchable-item');

    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim();
        cards.forEach(card => {
            const text = (card.innerText || card.textContent).toLowerCase();
            card.style.display = (!query || text.includes(query)) ? '' : 'none';
        });
    });
}

/* ==========================================================
   4. RECHERCHE INSTANTANÉE DANS LA MODALE GLOBALE
   ========================================================== */
function initModalLiveSearch() {
    const input = document.getElementById('liveSearchModalInput');
    const resultsContainer = document.getElementById('searchModalResults');
    const modalEl = document.getElementById('globalSearchModal');
    const quickTags = document.querySelectorAll('[data-quick-search]');

    if (!input || !resultsContainer) return;

    // Tags de suggestion rapide
    quickTags.forEach(tag => {
        tag.addEventListener('click', () => {
            const val = tag.getAttribute('data-quick-search');
            input.value = val;
            performSearch(val);
        });
    });

    input.addEventListener('input', (e) => {
        performSearch(e.target.value);
    });

    // Focus automatique à l'ouverture de la modale
    if (modalEl) {
        modalEl.addEventListener('shown.bs.modal', () => {
            input.focus();
        });
    }

    function performSearch(rawQuery) {
        const query = rawQuery.toLowerCase().trim();
        const episodes = window.ENSAD_EPISODES || [];

        if (!query) {
            resultsContainer.innerHTML = `
                <div class="text-center py-4 text-muted small">
                    Tapez quelques lettres pour rechercher parmi les 11 épisodes officiels de l'ENSAD Mohammedia.
                </div>
            `;
            return;
        }

        const filtered = episodes.filter(ep => {
            const title = (ep.title || '').toLowerCase();
            const desc = (ep.description || '').toLowerCase();
            const host = (ep.host || '').toLowerCase();
            const cat = (ep.podcast_category || '').toLowerCase();
            const podcast = (ep.podcast_title || '').toLowerCase();
            const contribs = Array.isArray(ep.contributors) ? ep.contributors.join(' ').toLowerCase() : '';

            return title.includes(query) || desc.includes(query) || host.includes(query) || cat.includes(query) || podcast.includes(query) || contribs.includes(query);
        });

        if (filtered.length === 0) {
            resultsContainer.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <span class="material-symbols-rounded fs-2 mb-2">search_off</span>
                    <p class="small mb-0">Aucun résultat trouvé pour "<strong>${escapeHtml(rawQuery)}</strong>"</p>
                </div>
            `;
            return;
        }

        let html = '';
        filtered.forEach(ep => {
            const epJson = encodeURIComponent(JSON.stringify(ep));
            html += `
                <div class="search-result-item d-flex align-items-center justify-content-between gap-3 p-2 rounded-3 mb-2"
                     style="background: #0B1F3A; border: 1px solid rgba(22, 44, 78, 0.7); cursor: pointer;"
                     data-search-result-row>
                    <div class="d-flex align-items-center gap-3 flex-grow-1 overflow-hidden"
                         data-open-episode-modal="${epJson}">
                        <img src="${ep.cover || 'assets/images/micro-ensad-icon.svg'}" 
                             alt="${escapeHtml(ep.title)}" 
                             style="width: 44px; height: 44px; border-radius: 8px; object-fit: cover; flex-shrink: 0;">
                        <div class="overflow-hidden">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge font-monospace text-bg-primary" style="font-size: 0.65rem;">${ep.ep_number || 'EP'}</span>
                                <span class="text-muted small text-truncate" style="font-size: 0.72rem;">${escapeHtml(ep.podcast_category || '')}</span>
                            </div>
                            <h6 class="mb-0 text-white text-truncate font-heading" style="font-size: 0.88rem;">${escapeHtml(ep.title)}</h6>
                            <div class="text-muted small text-truncate" style="font-size: 0.72rem;">${escapeHtml(ep.podcast_title)} • ${ep.duration || ''}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-card-play flex-shrink-0"
                            data-play-episode
                            data-episode-id="${ep.id}"
                            data-title="${escapeHtml(ep.title)}"
                            data-host="${escapeHtml(ep.host)}"
                            data-podcast="${escapeHtml(ep.podcast_title)}"
                            data-cover="${escapeHtml(ep.cover)}"
                            data-youtube-id="${escapeHtml(ep.youtube_id)}"
                            data-duration="${escapeHtml(ep.duration)}"
                            title="Écouter l'épisode">
                        <span class="material-symbols-rounded">play_arrow</span>
                    </button>
                </div>
            `;
        });

        resultsContainer.innerHTML = html;

        // Clic sur un résultat ferme la modale de recherche
        resultsContainer.querySelectorAll('[data-open-episode-modal]').forEach(item => {
            item.addEventListener('click', () => {
                if (modalEl) bootstrap.Modal.getInstance(modalEl)?.hide();
            });
        });
    }
}

/* ==========================================================
   5. COMPORTEMENTS NAVBAR (SCROLL & CTA ÉCOUTER)
   ========================================================== */
function initNavbarBehaviors() {
    const navbar = document.querySelector('.navbar-cinematic-top');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 25) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }, { passive: true });
    }

    // Bouton Écouter dans la barre de navigation
    const navListenBtn = document.getElementById('navListenBtn');
    if (navListenBtn) {
        navListenBtn.addEventListener('click', (e) => {
            e.preventDefault();
            // Chercher le premier bouton de lecture sur la page
            const firstPlayBtn = document.querySelector('[data-play-episode]');
            if (firstPlayBtn) {
                firstPlayBtn.click();
            } else {
                window.location.href = 'index.php';
            }
        });
    }
}

/* ==========================================================
   6. PARTAGE CINÉMATIQUE, QR CODE & CARTE STORY (9:16)
   ========================================================== */
let currentQrEpisode = null;

function initShareButtons() {
    // Boutons de partage sur les cartes, épisode vedette et pages
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-share-action, [data-share-btn]');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        let epData = null;

        // 1. Essayer d'extraire depuis data-episode-json
        const rawJson = btn.getAttribute('data-episode-json');
        if (rawJson) {
            try {
                epData = JSON.parse(decodeURIComponent(rawJson));
            } catch (_) {}
        }

        // 2. Essayer de trouver depuis l'ID dans window.ENSAD_EPISODES
        const epId = btn.getAttribute('data-episode-id');
        if (!epData && epId && window.ENSAD_EPISODES && Array.isArray(window.ENSAD_EPISODES)) {
            epData = window.ENSAD_EPISODES.find(item => String(item.id) === String(epId));
        }

        // 3. Fallback avec les attributs du bouton
        if (!epData) {
            epData = {
                id: epId || '1',
                title: btn.getAttribute('data-share-title') || document.title,
                cover: btn.getAttribute('data-cover') || 'assets/images/micro-ensad-icon.svg',
                podcast_title: btn.getAttribute('data-podcast') || 'Micro ENSAD',
                podcast_category: 'Podcast ENSAD',
                host: btn.getAttribute('data-host') || 'Étudiants ENSAD',
                department: 'DENSAD Bac+5 — ENSAD Casablanca'
            };
        }

        openQrShareModal(epData);
    });

    // Bouton de Téléchargement de l'Image Story PNG
    const btnDownload = document.getElementById('btnDownloadStoryPng');
    if (btnDownload) {
        btnDownload.addEventListener('click', () => {
            if (currentQrEpisode) {
                downloadStoryCardPng(currentQrEpisode);
            }
        });
    }

    // Bouton de Copie du Lien Direct
    const btnCopy = document.getElementById('btnCopyShareLink');
    if (btnCopy) {
        btnCopy.addEventListener('click', async () => {
            const input = document.getElementById('qrShareDirectUrl');
            if (!input) return;
            const textToCopy = input.value;

            const copyIcon = document.getElementById('copyBtnIcon');
            const copyText = document.getElementById('copyBtnText');

            try {
                await navigator.clipboard.writeText(textToCopy);
                showToast("Lien de l'épisode copié avec succès !");
                if (copyIcon) copyIcon.textContent = 'check';
                if (copyText) copyText.textContent = 'Copié !';
                setTimeout(() => {
                    if (copyIcon) copyIcon.textContent = 'content_copy';
                    if (copyText) copyText.textContent = 'Copier';
                }, 2500);
            } catch (_) {
                input.select();
                document.execCommand('copy');
                showToast("Lien sélectionné et copié !");
            }
        });
    }
}

/**
 * Ouvre la modale de partage avec QR Code et prévisualisation Story
 */
function openQrShareModal(epData) {
    if (!epData) return;

    // Si epData est un ID ou chaîne, résoudre l'objet complet
    if (typeof epData === 'string' || typeof epData === 'number') {
        const epId = String(epData);
        if (Array.isArray(window.ENSAD_EPISODES)) {
            const found = window.ENSAD_EPISODES.find(item => String(item.id) === epId);
            epData = found || { id: epId };
        } else if (window.ensadEngine && window.ensadEngine.currentTrack && String(window.ensadEngine.currentTrack.id) === epId) {
            epData = window.ensadEngine.currentTrack;
        } else {
            epData = { id: epId };
        }
    }

    // Normaliser les clés alternatives
    epData.podcast_title = epData.podcast_title || epData.podcast || 'Micro ENSAD';
    epData.title = epData.title || 'Micro ENSAD • Le Podcast';
    currentQrEpisode = epData;

    const modalEl = document.getElementById('qrShareModal');
    if (!modalEl) return;

    // Éléments du DOM
    const bgImg       = document.getElementById('storyCardBgImg');
    const coverImg    = document.getElementById('storyCardCoverImg');
    const epBadge     = document.getElementById('storyCardEpBadge');
    const showTitle   = document.getElementById('storyCardShowTitle');
    const titleEl     = document.getElementById('storyCardTitle');
    const categoryEl  = document.getElementById('storyCardCategory');
    const hostsEl     = document.getElementById('storyCardHosts');
    const deptEl      = document.getElementById('storyCardDept');
    const urlInput    = document.getElementById('qrShareDirectUrl');
    const qrMount     = document.getElementById('storyQrCodeMount');

    // Image de couverture (gestion des chemins relatifs et absolus)
    const rawCover = epData.cover || 'assets/images/micro-ensad-icon.svg';
    const cleanCover = rawCover.replace(/^(\.\.\/)+/, '');
    if (bgImg)    bgImg.src = cleanCover;
    if (coverImg) coverImg.src = cleanCover;

    // Badges et Métadonnées
    const epNumStr = epData.ep_number || ('EP. ' + String(epData.id).padStart(2, '0'));
    if (epBadge)    epBadge.textContent = epNumStr;
    if (showTitle)  showTitle.textContent = epData.podcast_title || 'Micro ENSAD';
    if (titleEl)    titleEl.textContent = epData.title || 'Titre du podcast';
    if (categoryEl) categoryEl.textContent = epData.podcast_category || epData.filiere || 'Podcast ENSAD';

    // Contributeurs
    const contributorsStr = Array.isArray(epData.contributors) && epData.contributors.length > 0
        ? epData.contributors.join(', ')
        : (epData.host || 'Étudiants ENSAD');
    if (hostsEl) hostsEl.textContent = contributorsStr;
    if (deptEl)  deptEl.textContent = epData.department || 'Cycle Bac+5 (DENSAD) • DGI / GDA';

    // Lien complet direct vers l'épisode
    const fullUrl = window.location.origin + '/episode-detail.php?id=' + epData.id;
    if (urlInput) urlInput.value = fullUrl;

    // Liens Réseaux Sociaux
    const shareText = `🎙️ Écoutez le podcast « ${epData.title} » sur Micro ENSAD Casablanca :`;
    const shareUrlEnc = encodeURIComponent(fullUrl);
    const shareTextEnc = encodeURIComponent(`${shareText} ${fullUrl}`);

    const btnWhatsapp = document.getElementById('shareBtnWhatsapp');
    if (btnWhatsapp) btnWhatsapp.href = `https://api.whatsapp.com/send?text=${shareTextEnc}`;

    const btnFacebook = document.getElementById('shareBtnFacebook');
    if (btnFacebook) btnFacebook.href = `https://www.facebook.com/sharer/sharer.php?u=${shareUrlEnc}`;

    const btnLinkedin = document.getElementById('shareBtnLinkedin');
    if (btnLinkedin) btnLinkedin.href = `https://www.linkedin.com/sharing/share-offsite/?url=${shareUrlEnc}`;

    const btnTwitter = document.getElementById('shareBtnTwitter');
    if (btnTwitter) btnTwitter.href = `https://twitter.com/intent/tweet?text=${encodeURIComponent('🎙️ « ' + epData.title + ' » — Micro ENSAD')}&url=${shareUrlEnc}`;

    // Partage Natif Mobile (Web Share API)
    const nativeShareWrap = document.getElementById('nativeShareWrap');
    const btnNativeWebShare = document.getElementById('btnNativeWebShare');
    if (nativeShareWrap && btnNativeWebShare) {
        if (navigator.share) {
            nativeShareWrap.classList.remove('d-none');
            btnNativeWebShare.onclick = async () => {
                try {
                    await navigator.share({
                        title: `Micro ENSAD — ${epData.title}`,
                        text: `Écoutez le podcast « ${epData.title} » des étudiants de l'ENSAD Casablanca`,
                        url: fullUrl
                    });
                } catch (_) {}
            };
        } else {
            nativeShareWrap.classList.add('d-none');
        }
    }

    // Génération instantanée du QR Code en local
    if (qrMount && typeof QRCode !== 'undefined') {
        qrMount.innerHTML = '';
        try {
            new QRCode(qrMount, {
                text: fullUrl,
                width: 112,
                height: 112,
                colorDark: '#050B14',
                colorLight: '#FFFFFF',
                correctLevel: QRCode.CorrectLevel.H
            });
        } catch (qrErr) {
            console.warn("QRCode generation warning:", qrErr);
        }
    }

    // Afficher la modale Bootstrap
    bootstrap.Modal.getOrCreateInstance(modalEl).show();
}

/**
 * Génère et télécharge une image Story (PNG 9:16 - 1080x1920) haute définition via HTML5 Canvas
 * Avec intégration des deux logos officiels (ENSAD Casablanca & Université Hassan II)
 */
async function downloadStoryCardPng(epData) {
    if (!epData) return;
    const progressEl  = document.getElementById('storyGenProgress');
    const btnDownload = document.getElementById('btnDownloadStoryPng');

    if (progressEl)  progressEl.classList.remove('d-none');
    if (btnDownload) btnDownload.disabled = true;

    try {
        const canvas = document.getElementById('storyExportCanvas');
        if (!canvas) throw new Error("Canvas introuvable");
        const ctx = canvas.getContext('2d');
        const W = 1080;
        const H = 1920;
        canvas.width  = W;
        canvas.height = H;

        // Helper pour charger une image de façon asynchrone sécurisée
        const loadImageSafe = (src) => new Promise((resolve) => {
            if (!src) return resolve(null);
            const image = new Image();
            image.crossOrigin = 'anonymous';
            image.onload = () => resolve(image);
            image.onerror = () => resolve(null);
            image.src = src;
        });

        // 1. Charger simultanément la pochette et les deux logos officiels
        const rawCover = epData.cover || 'assets/images/micro-ensad-icon.svg';
        const cleanCover = rawCover.replace(/^(\.\.\/)+/, '');

        const [coverImg, logoPrincImg, logoSecImg] = await Promise.all([
            loadImageSafe(cleanCover),
            loadImageSafe('assets/images/logo-principal.svg'),
            loadImageSafe('assets/images/logo-secondaire.svg')
        ]);

        // 2. Fond sombre de base
        ctx.fillStyle = '#050B14';
        ctx.fillRect(0, 0, W, H);

        // 3. Fond d'affiche flouté & cinématique
        if (coverImg && coverImg.width > 0) {
            ctx.save();
            ctx.filter = 'blur(48px) brightness(0.32) saturate(1.8)';
            ctx.drawImage(coverImg, -140, -140, W + 280, H + 280);
            ctx.restore();
        }

        // 4. Dégradé vignette sombre cinématique
        const vignette = ctx.createLinearGradient(0, 0, 0, H);
        vignette.addColorStop(0, 'rgba(5, 11, 20, 0.45)');
        vignette.addColorStop(0.35, 'rgba(5, 11, 20, 0.65)');
        vignette.addColorStop(0.70, 'rgba(5, 11, 20, 0.90)');
        vignette.addColorStop(1, 'rgba(5, 11, 20, 0.98)');
        ctx.fillStyle = vignette;
        ctx.fillRect(0, 0, W, H);

        // 5. Halo bleu électrique radial au centre de l'affiche
        const radialGlow = ctx.createRadialGradient(W / 2, 540, 50, W / 2, 540, 560);
        radialGlow.addColorStop(0, 'rgba(18, 100, 255, 0.38)');
        radialGlow.addColorStop(1, 'rgba(18, 100, 255, 0)');
        ctx.fillStyle = radialGlow;
        ctx.fillRect(0, 0, W, H);

        // Fonction utilitaire : rectangle à coins arrondis (parfaitement lissé sans distorsion)
        function drawRoundedRect(context, x, y, width, height, radius) {
            if (typeof context.roundRect === 'function') {
                context.beginPath();
                context.roundRect(x, y, width, height, radius);
                return;
            }
            context.beginPath();
            context.moveTo(x + radius, y);
            context.lineTo(x + width - radius, y);
            context.quadraticCurveTo(x + width, y, x + width, y + radius);
            context.lineTo(x + width, y + height - radius);
            context.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
            context.lineTo(x + radius, y + height);
            context.quadraticCurveTo(x, y + height, x, y + height - radius);
            context.lineTo(x, y + radius);
            context.quadraticCurveTo(x, y, x + radius, y);
            context.closePath();
        }

        // 6. EN-TÊTE SUPÉRIEUR AVEC LES DEUX LOGOS OFFICIELS (GAUCHE : ENSAD, DROITE : UH2C)
        const headerY = 70;
        const headerH = 110;
        ctx.save();
        drawRoundedRect(ctx, 60, headerY, W - 120, headerH, 28);
        ctx.fillStyle = 'rgba(5, 11, 20, 0.72)';
        ctx.fill();
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.2)';
        ctx.lineWidth = 2;
        ctx.stroke();
        ctx.restore();

        // Logo 1 (Gauche) : ENSAD Casablanca
        if (logoPrincImg && logoPrincImg.width > 0) {
            ctx.save();
            const lH = 64;
            const lW = (logoPrincImg.width / logoPrincImg.height) * lH;
            ctx.drawImage(logoPrincImg, 95, headerY + (headerH - lH) / 2, lW, lH);
            ctx.restore();
        } else {
            ctx.save();
            ctx.font = 'bold 26px "Plus Jakarta Sans", sans-serif';
            ctx.fillStyle = '#FFFFFF';
            ctx.fillText('ENSAD', 95, headerY + 65);
            ctx.restore();
        }

        // Logo 2 (Droite) : Université Hassan II Casablanca (Seulement les 2 logos, sans badge central)
        if (logoSecImg && logoSecImg.width > 0) {
            ctx.save();
            const sH = 54;
            const sW = (logoSecImg.width / logoSecImg.height) * sH;
            const sBoxW = sW + 28;
            const sBoxH = 72;
            const sBoxX = W - 95 - sBoxW;
            const sBoxY = headerY + (headerH - sBoxH) / 2;

            // Boîte blanche satinée pour le logo secondaire (garantit un contraste parfait)
            drawRoundedRect(ctx, sBoxX, sBoxY, sBoxW, sBoxH, 16);
            ctx.fillStyle = '#FFFFFF';
            ctx.fill();
            ctx.strokeStyle = 'rgba(31, 94, 173, 0.25)';
            ctx.lineWidth = 1.5;
            ctx.stroke();

            ctx.drawImage(logoSecImg, sBoxX + 14, sBoxY + (sBoxH - sH) / 2, sW, sH);
            ctx.restore();
        } else {
            ctx.save();
            ctx.font = 'bold 22px "Montserrat", sans-serif';
            ctx.fillStyle = '#FFFFFF';
            ctx.textAlign = 'right';
            ctx.fillText('UH2C', W - 95, headerY + 65);
            ctx.restore();
        }

        // 7. Badge Catégorie sous l'en-tête
        if (epData.podcast_category || epData.filiere) {
            ctx.save();
            const catText = (epData.podcast_category || epData.filiere || 'PODCAST').toUpperCase();
            ctx.font = 'bold 20px "Montserrat", sans-serif';
            const catWidth = ctx.measureText(catText).width + 48;
            const catX = (W - catWidth) / 2;
            drawRoundedRect(ctx, catX, 202, catWidth, 48, 24);
            ctx.fillStyle = 'rgba(18, 100, 255, 0.35)';
            ctx.fill();
            ctx.strokeStyle = 'rgba(47, 123, 255, 0.7)';
            ctx.lineWidth = 1.5;
            ctx.stroke();

            ctx.fillStyle = '#FFFFFF';
            ctx.textAlign = 'center';
            ctx.fillText(catText, W / 2, 233);
            ctx.restore();
        }

        // 8. Pochette Carrée 1:1 Centrale (560x560) avec Coins Arrondis et Ombre
        const coverSize = 560;
        const coverX = (W - coverSize) / 2;
        const coverY = 270;

        ctx.save();
        ctx.shadowColor = 'rgba(0, 0, 0, 0.75)';
        ctx.shadowBlur = 50;
        ctx.shadowOffsetY = 28;
        drawRoundedRect(ctx, coverX, coverY, coverSize, coverSize, 40);
        ctx.fillStyle = '#071426';
        ctx.fill();
        ctx.restore();

        // Image centrale clipsée
        if (coverImg && coverImg.width > 0) {
            ctx.save();
            drawRoundedRect(ctx, coverX, coverY, coverSize, coverSize, 40);
            ctx.clip();
            ctx.drawImage(coverImg, coverX, coverY, coverSize, coverSize);
            ctx.restore();
        }

        // Bordure blanche subtile
        ctx.save();
        drawRoundedRect(ctx, coverX, coverY, coverSize, coverSize, 40);
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.32)';
        ctx.lineWidth = 3.5;
        ctx.stroke();
        ctx.restore();

        // Badge Numéro d'épisode en or sur l'image
        const epBadgeStr = epData.ep_number || ('EP. ' + String(epData.id).padStart(2, '0'));
        ctx.save();
        drawRoundedRect(ctx, coverX + coverSize - 160, coverY + 22, 138, 52, 16);
        ctx.fillStyle = '#FFE52B';
        ctx.fill();
        ctx.fillStyle = '#092A5C';
        ctx.font = 'bold 24px "Montserrat", sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText(epBadgeStr, coverX + coverSize - 91, coverY + 57);
        ctx.restore();

        // 9. Titre & Métadonnées
        let textY = coverY + coverSize + 75;

        // Slogan / Émission
        ctx.save();
        ctx.font = 'bold 26px "Montserrat", sans-serif';
        ctx.fillStyle = '#2F7BFF';
        ctx.textAlign = 'center';
        ctx.fillText((epData.podcast_title || 'MICRO ENSAD').toUpperCase() + ' • LE PODCAST DES ÉTUDIANTS', W / 2, textY);
        ctx.restore();

        textY += 56;

        // Titre complet de l'épisode (avec retour à la ligne automatique)
        ctx.save();
        ctx.font = 'bold 44px "Plus Jakarta Sans", sans-serif';
        ctx.fillStyle = '#FFFFFF';
        ctx.textAlign = 'center';

        function wrapText(context, text, x, y, maxWidth, lineHeight, maxLines = 2) {
            const words = text.split(' ');
            let line = '';
            let lines = [];

            for (let n = 0; n < words.length; n++) {
                const testLine = line + words[n] + ' ';
                const metrics = context.measureText(testLine);
                if (metrics.width > maxWidth && n > 0) {
                    lines.push(line.trim());
                    line = words[n] + ' ';
                } else {
                    line = testLine;
                }
            }
            lines.push(line.trim());

            if (lines.length > maxLines) {
                lines = lines.slice(0, maxLines);
                lines[maxLines - 1] += '...';
            }

            for (let i = 0; i < lines.length; i++) {
                context.fillText(lines[i], x, y + (i * lineHeight));
            }
            return y + (lines.length * lineHeight);
        }

        textY = wrapText(ctx, epData.title || '', W / 2, textY, 920, 54, 2);
        ctx.restore();

        textY += 28;

        // Animateurs / Étudiants
        const contribs = Array.isArray(epData.contributors) && epData.contributors.length > 0
            ? epData.contributors.join(', ')
            : (epData.host || 'Étudiants ENSAD');
        ctx.save();
        ctx.font = '500 25px "Montserrat", sans-serif';
        ctx.fillStyle = '#A8B7CC';
        ctx.textAlign = 'center';
        ctx.fillText('🎙️ Animé par : ' + contribs, W / 2, textY);
        ctx.restore();

        // 10. Boîte blanche épurée du QR Code
        const qrBoxSize = 330;
        const qrBoxX = (W - qrBoxSize) / 2;
        const qrBoxY = 1180;

        ctx.save();
        ctx.shadowColor = 'rgba(0, 0, 0, 0.45)';
        ctx.shadowBlur = 32;
        ctx.shadowOffsetY = 16;
        drawRoundedRect(ctx, qrBoxX, qrBoxY, qrBoxSize, qrBoxSize, 32);
        ctx.fillStyle = '#FFFFFF';
        ctx.fill();

        ctx.strokeStyle = 'rgba(255, 255, 255, 0.5)';
        ctx.lineWidth = 2;
        ctx.stroke();
        ctx.restore();

        // Dessiner le QR code généré (canvas ou image)
        const qrMount = document.getElementById('storyQrCodeMount');
        const qrCanvasOrImg = qrMount ? qrMount.querySelector('canvas, img') : null;

        if (qrCanvasOrImg) {
            ctx.save();
            const qrPadding = 26;
            const innerQrSize = qrBoxSize - (qrPadding * 2);
            ctx.drawImage(qrCanvasOrImg, qrBoxX + qrPadding, qrBoxY + qrPadding, innerQrSize, innerQrSize);
            ctx.restore();
        }

        // Tag discret sous le QR code (sans icône de caméra ni texte de scan)
        ctx.save();
        ctx.font = '600 22px "Montserrat", sans-serif';
        ctx.fillStyle = 'rgba(255, 255, 255, 0.6)';
        ctx.textAlign = 'center';
        ctx.fillText(window.location.host + ' • Podcast ENSAD', W / 2, qrBoxY + qrBoxSize + 48);
        ctx.restore();

        // 11. Footer Institutionnel
        ctx.save();
        ctx.font = '600 24px "Montserrat", sans-serif';
        ctx.fillStyle = 'rgba(255, 255, 255, 0.88)';
        ctx.textAlign = 'center';
        ctx.fillText('École Nationale Supérieure d\'Art et de Design • Casablanca', W / 2, 1815);

        ctx.font = '500 20px "Montserrat", sans-serif';
        ctx.fillStyle = 'rgba(255, 255, 255, 0.55)';
        ctx.fillText('Université Hassan II • DENSAD Bac+5', W / 2, 1852);
        ctx.restore();

        // Téléchargement du fichier PNG
        const dataUrl = canvas.toDataURL('image/png');
        const safeTitle = (epData.title || 'Episode').replace(/[^a-zA-Z0-9_-]/g, '_').substring(0, 30);
        const filename = `MicroENSAD_EP${epData.id}_${safeTitle}_Story.png`;

        const link = document.createElement('a');
        link.download = filename;
        link.href = dataUrl;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        showToast("✅ Carte Story PNG haute définition téléchargée avec succès !");
    } catch (err) {
        console.error("Erreur lors de la génération Story :", err);
        showToast("Échec de la génération automatique. Copiez le lien pour partager.");
    } finally {
        if (progressEl)  progressEl.classList.add('d-none');
        if (btnDownload) btnDownload.disabled = false;
    }
}

/* ==========================================================
   7. MODALE DE DÉTAILS D'ÉPISODE
   ========================================================== */
function initEpisodeModal() {
    const modalEl = document.getElementById('episodeDetailModal');
    if (!modalEl) return;

    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-open-episode-modal]');
        if (!trigger) return;

        e.preventDefault();
        e.stopPropagation();

        const raw = trigger.dataset.openEpisodeModal;
        if (!raw) return;

        let ep;
        try {
            ep = JSON.parse(decodeURIComponent(raw));
        } catch (_) {
            return;
        }

        populateModal(ep);
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    });

    // Bouton « Écouter » dans la modale
    const playBtn = document.getElementById('modalPlayBtn');
    if (playBtn) {
        playBtn.addEventListener('click', () => {
            const epId      = playBtn.dataset.episodeId;
            const title     = playBtn.dataset.title;
            const host      = playBtn.dataset.host;
            const podcast   = playBtn.dataset.podcast;
            const cover     = playBtn.dataset.cover;
            const ytId      = playBtn.dataset.youtubeId;
            const duration  = playBtn.dataset.duration;

            if (!ytId) return;

            const syntheticBtn = document.createElement('button');
            syntheticBtn.dataset.playEpisode  = '';
            syntheticBtn.dataset.episodeId    = epId;
            syntheticBtn.dataset.title        = title;
            syntheticBtn.dataset.host         = host;
            syntheticBtn.dataset.podcast      = podcast;
            syntheticBtn.dataset.cover        = cover;
            syntheticBtn.dataset.youtubeId    = ytId;
            syntheticBtn.dataset.duration     = duration;
            document.body.appendChild(syntheticBtn);
            syntheticBtn.click();
            syntheticBtn.remove();

            bootstrap.Modal.getOrCreateInstance(modalEl).hide();
        });
    }
}

function populateModal(ep) {
    const set = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.textContent = val || '';
    };

    // Cover carrée principale (1:1)
    const coverImg = document.getElementById('modalCoverImg');
    if (coverImg) {
        coverImg.src = ep.cover || 'assets/images/micro-ensad-icon.svg';
        coverImg.alt = ep.title || '';
    }

    // Arrière-plan flou d'ambiance
    const coverBg = document.getElementById('modalCoverBg');
    if (coverBg) {
        coverBg.src = ep.cover || 'assets/images/micro-ensad-icon.svg';
    }

    // Badges & Tag d'angle carré
    const epNum = ep.ep_number || ('EP. ' + String(ep.id).padStart(2, '0'));
    set('modalEpTagCorner', epNum);
    set('modalEpTag',       epNum);
    set('modalCategoryTag', ep.podcast_category || '');

    // Titre
    set('episodeModalTitle', ep.title || '');

    // Infos d'en-tête
    set('modalHeaderPodcastTitle', ep.podcast_title || '');
    set('modalHeaderDuration',     ep.duration || '');

    // Infos du corps
    set('modalPodcastTitle', ep.podcast_title || '');
    set('modalDate',         ep.date || '');
    set('modalDuration',     ep.duration || '');

    // Filière
    const filiereEl = document.getElementById('modalFiliere');
    if (filiereEl) {
        filiereEl.textContent = ep.filiere ? 'Filière : ' + ep.filiere : '';
    }

    // Contributeurs
    const contribWrap   = document.getElementById('modalContributorsWrap');
    const contribBadges = document.getElementById('modalContributorBadges');
    if (contribBadges) {
        contribBadges.innerHTML = '';
        const contributors = ep.contributors || [];
        if (contributors.length > 0) {
            contributors.forEach(name => {
                const span = document.createElement('span');
                span.className = 'badge-contrib';
                span.textContent = name;
                contribBadges.appendChild(span);
            });
            if (contribWrap) contribWrap.classList.remove('d-none');
        } else {
            if (contribWrap) contribWrap.classList.add('d-none');
        }
    }

    // Description
    set('modalDescription', ep.description || '');

    // Chapitres
    const chapWrap = document.getElementById('modalChaptersWrap');
    const chapList = document.getElementById('modalChaptersList');
    if (chapList) {
        chapList.innerHTML = '';
        const chapters = ep.chapters || [];
        if (chapters.length > 0 && chapWrap) {
            chapters.forEach(ch => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'chapter-pill-btn text-start';
                btn.dataset.seekTime = ch.seconds || 0;
                btn.innerHTML = `
                    <span class="material-symbols-rounded text-primary" style="font-size: 1rem;">play_circle</span>
                    <span class="chapter-time font-monospace">${ch.time || '00:00'}</span>
                    <span class="chapter-label">${escapeHtml(ch.title || '')}</span>
                `;
                btn.addEventListener('click', () => {
                    if (window.ensadPlayer && typeof window.ensadPlayer.seekTo === 'function') {
                        window.ensadPlayer.seekTo(ch.seconds || 0);
                    }
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('episodeDetailModal')).hide();
                });
                chapList.appendChild(btn);
            });
            chapWrap.classList.remove('d-none');
        } else if (chapWrap) {
            chapWrap.classList.add('d-none');
        }
    }

    // Bouton « Écouter »
    const playBtn = document.getElementById('modalPlayBtn');
    if (playBtn) {
        playBtn.dataset.episodeId  = ep.id || '';
        playBtn.dataset.title      = ep.title || '';
        playBtn.dataset.host       = ep.host || '';
        playBtn.dataset.podcast    = ep.podcast_title || '';
        playBtn.dataset.cover      = ep.cover || '';
        playBtn.dataset.youtubeId  = ep.youtube_id || '';
        playBtn.dataset.duration   = ep.duration || '';
        playBtn.disabled = !ep.youtube_id;
    }

    // Lien « Fiche complète »
    const detailLink = document.getElementById('modalDetailLink');
    if (detailLink) {
        detailLink.href = ep.id ? `episode-detail.php?id=${ep.id}` : '#';
    }

    // Bouton « Partager / QR » dans la modale
    const shareQrBtn = document.getElementById('modalShareQrBtn');
    if (shareQrBtn) {
        shareQrBtn.onclick = () => {
            const epModal = bootstrap.Modal.getInstance(document.getElementById('episodeDetailModal'));
            if (epModal) epModal.hide();
            setTimeout(() => {
                openQrShareModal(ep);
            }, 300);
        };
    }
}

/* ==========================================================
   8. NOTIFICATION TOAST
   ========================================================== */
function showToast(message) {
    let container = document.getElementById('ensadToastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'ensadToastContainer';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1090';
        document.body.appendChild(container);
    }

    const toastEl = document.createElement('div');
    toastEl.className = 'toast align-items-center text-bg-primary border-0 show shadow-lg rounded-4';
    toastEl.setAttribute('role', 'alert');
    toastEl.innerHTML = `
        <div class="d-flex p-2 align-items-center">
            <div class="toast-body d-flex align-items-center gap-2 flex-grow-1">
                <span class="material-symbols-rounded text-warning fs-5">check_circle</span>
                <span class="fw-semibold">${escapeHtml(message)}</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    container.appendChild(toastEl);
    setTimeout(() => toastEl.remove(), 3500);
}

function escapeHtml(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}

// Exposer globalement pour le lecteur audio et les déclencheurs
window.openQrShareModal = openQrShareModal;
window.downloadStoryCardPng = downloadStoryCardPng;

