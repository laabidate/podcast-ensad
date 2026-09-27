/**
 * MICRO ENSAD MOHAMMEDIA - MOTEUR AUDIO YOUTUBE & PODCAST
 * 1. Moteur Audio YouTube Pur sans affichage vidéo.
 * 2. Mode Desktop (>= 768px) : Barre flottante avec ancrage interactif (Left / Center / Right) & Volume complet.
 * 3. Mode Mobile (< 768px) : Mini-lecteur compact + Lecteur Plein Écran Style Spotify (Inspiré fidèlement de la maquette).
 * 4. Découpage Précis en Chapitres YouTube & Tiroir Interactif des Parties.
 * 5. Persistance Complète & Système de Téléchargement / Mise en Cache Hors-Ligne.
 */

class EnsadPodcastEngine {
    constructor() {
        this.currentTrack = null;
        this.isPlaying = false;
        this.ytPlayer = null;
        this.isYtReady = false;
        this.html5Audio = new Audio();
        this.useYouTube = true;
        this.timer = null;
        this.lastSavedTime = 0;
        this.isScrubbing = false;
        this.seekLockUntil = 0;
        this.lastSeekTime = 0;
        this.currentSpeed = 1.0;
        this.currentChapters = [];
        this.renderedDuration = 0;
        this.pendingYouTubeTrack = null;

        // ── 1. ÉLÉMENTS DU LECTEUR DESKTOP ──
        this.playerBar = document.getElementById('ensadPlayerBar');
        this.playBtn = document.getElementById('playerPlayBtn');
        this.playIcon = document.getElementById('playerPlayIcon');
        this.coverImg = document.getElementById('playerCover');
        this.titleEl = document.getElementById('playerTitle');
        this.artistEl = document.getElementById('playerArtist');
        this.currentTimeEl = document.getElementById('playerCurrentTime');
        this.durationEl = document.getElementById('playerDuration');
        this.progressBar = document.getElementById('playerProgressBar');
        this.volumeSlider = document.getElementById('playerVolumeSlider');
        this.volText = document.getElementById('playerVolText');
        this.muteBtn = document.getElementById('playerMuteBtn');
        this.muteIcon = document.getElementById('playerMuteIcon');
        this.waveBars = document.getElementById('playerWaveBars');
        this.desktopDownloadBtn = document.getElementById('playerDesktopDownloadBtn');
        this.btnOpenDesktopChapters = document.getElementById('btnOpenDesktopChapters');

        // Chapitres Desktop
        this.chapterHeader = document.getElementById('playerChapterHeader');
        this.chapterPillNum = document.getElementById('playerChapterPillNum');
        this.chapterTitleText = document.getElementById('playerCurrentChapterTitle');
        this.chaptersTrack = document.getElementById('playerChaptersTrack');
        this.chapterTooltip = document.getElementById('playerChapterTooltip');
        this.tooltipNum = document.getElementById('tooltipChapterNum');
        this.tooltipTitle = document.getElementById('tooltipChapterTitle');
        this.tooltipTime = document.getElementById('tooltipChapterTime');

        // ── 2. ÉLÉMENTS DU MINI-LECTEUR MOBILE ──
        this.mobileMiniPlayer = document.getElementById('mobileMiniPlayer');
        this.miniPlayerOpenTrigger = document.getElementById('miniPlayerOpenTrigger');
        this.miniPlayerCover = document.getElementById('miniPlayerCover');
        this.miniPlayerTitle = document.getElementById('miniPlayerTitle');
        this.miniPlayerArtist = document.getElementById('miniPlayerArtist');
        this.miniPlayerChapterBadge = document.getElementById('miniPlayerChapterBadge');
        this.miniPlayerPlayBtn = document.getElementById('miniPlayerPlayBtn');
        this.miniPlayerPlayIcon = document.getElementById('miniPlayerPlayIcon');
        this.miniPlayerExpandBtn = document.getElementById('miniPlayerExpandBtn');
        this.miniPlayerProgressFill = document.getElementById('miniPlayerProgressFill');

        // ── 3. ÉLÉMENTS DU LECTEUR SPOTIFY MOBILE ──
        this.spotifyPlayer = document.getElementById('spotifyMobilePlayer');
        this.spotifyCloseBtn = document.getElementById('spotifyCloseBtn');
        this.spotifyPodcastName = document.getElementById('spotifyPodcastName');
        this.spotifyCover = document.getElementById('spotifyCover');
        this.spotifyTitle = document.getElementById('spotifyTitle');
        this.spotifyArtist = document.getElementById('spotifyArtist');
        this.spotifyLikeBtn = document.getElementById('spotifyLikeBtn');
        this.spotifyLikeIcon = document.getElementById('spotifyLikeIcon');
        this.spotifyChapterPillBtn = document.getElementById('spotifyChapterPillBtn');
        this.spotifyChapterTitle = document.getElementById('spotifyChapterTitle');
        this.spotifyOfflineBadge = document.getElementById('spotifyOfflineBadge');
        this.spotifyChaptersTrack = document.getElementById('spotifyChaptersTrack');
        this.spotifyProgressBar = document.getElementById('spotifyProgressBar');
        this.spotifyCurrentTime = document.getElementById('spotifyCurrentTime');
        this.spotifyDuration = document.getElementById('spotifyDuration');
        this.spotifyPlayBtn = document.getElementById('spotifyPlayBtn');
        this.spotifyPlayIcon = document.getElementById('spotifyPlayIcon');
        this.spotifyBack15 = document.getElementById('spotifyBack15');
        this.spotifyFwd15 = document.getElementById('spotifyFwd15');
        this.spotifyPrevChBtn = document.getElementById('spotifyPrevChBtn');
        this.spotifyNextChBtn = document.getElementById('spotifyNextChBtn');
        this.spotifySpeedBtn = document.getElementById('spotifySpeedBtn');
        this.spotifySpeedText = document.getElementById('spotifySpeedText');
        this.spotifyChaptersListBtn = document.getElementById('spotifyChaptersListBtn');
        this.spotifyDownloadBtn = document.getElementById('spotifyDownloadBtn');
        this.spotifyDownloadIcon = document.getElementById('spotifyDownloadIcon');
        this.spotifyShareActionBtn = document.getElementById('spotifyShareActionBtn');
        this.spotifyHeaderShareBtn = document.getElementById('spotifyHeaderShareBtn');
        this.desktopShareBtn = document.getElementById('playerDesktopShareBtn');

        // ── 4. ÉLÉMENTS DU TIROIR DES CHAPITRES ──
        this.chaptersDrawer = document.getElementById('playerChaptersDrawer');
        this.chaptersDrawerOverlay = document.getElementById('chaptersDrawerOverlay');
        this.chaptersDrawerCloseBtn = document.getElementById('chaptersDrawerCloseBtn');
        this.chaptersDrawerList = document.getElementById('chaptersDrawerList');

        this.init();
    }

    init() {
        this.loadYouTubeAPI();
        this.bindEvents();
        this.bindMobileEvents();
        this.bindChaptersDrawerEvents();
        this.bindPageButtons();
        this.initDockControls();
        this.initVolumeStepButtons();
        window.addEventListener('beforeunload', () => this.saveState());
    }

    loadYouTubeAPI() {
        let wrap = document.getElementById('hiddenYoutubePlayerContainer');
        if (!wrap) {
            wrap = document.createElement('div');
            wrap.id = 'hiddenYoutubePlayerContainer';
            wrap.innerHTML = '<div id="ytAudioPlayerTarget"></div>';
            document.body.appendChild(wrap);
        }

        if (!window.YT) {
            const tag = document.createElement('script');
            tag.src = "https://www.youtube.com/iframe_api";
            const firstScriptTag = document.getElementsByTagName('script')[0];
            firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
        }

        window.onYouTubeIframeAPIReady = () => {
            this.ytPlayer = new YT.Player('ytAudioPlayerTarget', {
                height: '1',
                width: '1',
                playerVars: {
                    'playsinline': 1,
                    'controls': 0,
                    'disablekb': 1,
                    'fs': 0,
                    'origin': window.location.origin
                },
                events: {
                    'onReady': () => {
                        this.isYtReady = true;
                        const defaultVol = this.volumeSlider ? parseInt(this.volumeSlider.value, 10) : 85;
                        this.setVolumeLevel(defaultVol);
                        if (this.pendingYouTubeTrack) {
                            const pending = this.pendingYouTubeTrack;
                            this.pendingYouTubeTrack = null;
                            this.startYouTubePlayback(pending.youtubeId, pending.startAt || 0);
                        } else {
                            this.restoreState();
                        }
                    },
                    'onStateChange': (event) => this.onPlayerStateChange(event),
                    'onError': () => {
                        this.pendingYouTubeTrack = null;
                        this.setPlayState(false);
                        if (typeof showToast === 'function') {
                            showToast("Lecture YouTube indisponible pour cet episode.");
                        }
                    }
                }
            });
        };
    }

    bindEvents() {
        // Play / Pause Desktop
        if (this.playBtn) {
            this.playBtn.addEventListener('click', () => this.togglePlay());
        }

        // Saut 15s Desktop
        const back15 = document.getElementById('playerBack15');
        if (back15) back15.addEventListener('click', () => this.seekBy(-15));

        const fwd15 = document.getElementById('playerFwd15');
        if (fwd15) fwd15.addEventListener('click', () => this.seekBy(15));



        // Partage / Carte Story & QR Code Desktop
        if (this.desktopShareBtn) {
            this.desktopShareBtn.addEventListener('click', () => {
                if (this.currentTrack && typeof window.openQrShareModal === 'function') {
                    window.openQrShareModal(this.currentTrack);
                }
            });
        }

        // Bouton ouverture tiroir des chapitres sur Desktop
        if (this.btnOpenDesktopChapters) {
            this.btnOpenDesktopChapters.addEventListener('click', () => this.openChaptersDrawer());
        }

        // Scrubber Desktop
        if (this.progressBar) {
            this.setupScrubber(this.progressBar);
        }

        // Survol Tooltip Desktop
        const timelineWrap = document.getElementById('playerTimelineInteractiveWrap');
        if (timelineWrap) {
            timelineWrap.addEventListener('mousemove', (e) => {
                const rect = timelineWrap.getBoundingClientRect();
                if (rect.width <= 0) return;
                const pos = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
                const duration = this.getDuration() || this.renderedDuration;
                if (duration > 0) {
                    const hoverSec = pos * duration;
                    this.showHoverTooltipAt(hoverSec, pos * rect.width);
                }
            });
            timelineWrap.addEventListener('mouseleave', () => {
                this.hideChapterTooltip();
            });
        }

        // Volume Desktop
        if (this.volumeSlider) {
            this.volumeSlider.addEventListener('input', (e) => {
                const vol = parseInt(e.target.value, 10);
                this.setVolumeLevel(vol);
            });
        }

        // Mute Desktop
        if (this.muteBtn) {
            this.muteBtn.addEventListener('click', () => this.toggleMute());
        }

        // Fallback HTML5 Audio
        this.html5Audio.addEventListener('timeupdate', () => this.onTimeUpdate());
        this.html5Audio.addEventListener('ended', () => this.setPlayState(false));
    }

    bindMobileEvents() {
        // Mini-lecteur clics
        if (this.miniPlayerOpenTrigger) {
            this.miniPlayerOpenTrigger.addEventListener('click', () => this.openSpotifyPlayer());
        }
        if (this.miniPlayerExpandBtn) {
            this.miniPlayerExpandBtn.addEventListener('click', () => this.openSpotifyPlayer());
        }
        if (this.miniPlayerPlayBtn) {
            this.miniPlayerPlayBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.togglePlay();
            });
        }

        // Lecteur Spotify clics
        if (this.spotifyCloseBtn) {
            this.spotifyCloseBtn.addEventListener('click', () => this.closeSpotifyPlayer());
        }
        if (this.spotifyPlayBtn) {
            this.spotifyPlayBtn.addEventListener('click', () => this.togglePlay());
        }
        if (this.spotifyBack15) {
            this.spotifyBack15.addEventListener('click', () => this.seekBy(-15));
        }
        if (this.spotifyFwd15) {
            this.spotifyFwd15.addEventListener('click', () => this.seekBy(15));
        }
        if (this.spotifyPrevChBtn) {
            this.spotifyPrevChBtn.addEventListener('click', () => this.jumpChapter(-1));
        }
        if (this.spotifyNextChBtn) {
            this.spotifyNextChBtn.addEventListener('click', () => this.jumpChapter(1));
        }

        // Scrubber Spotify Mobile
        if (this.spotifyProgressBar) {
            this.setupScrubber(this.spotifyProgressBar);
        }

        // Pastille Chapitre Spotify -> Ouvre le tiroir
        if (this.spotifyChapterPillBtn) {
            this.spotifyChapterPillBtn.addEventListener('click', () => this.openChaptersDrawer());
        }
        if (this.spotifyChaptersListBtn) {
            this.spotifyChaptersListBtn.addEventListener('click', () => this.openChaptersDrawer());
        }

        // Changement de vitesse
        if (this.spotifySpeedBtn) {
            this.spotifySpeedBtn.addEventListener('click', () => this.cyclePlaybackSpeed());
        }



        // Like / Favori Spotify
        if (this.spotifyLikeBtn) {
            this.spotifyLikeBtn.addEventListener('click', () => this.toggleFavorite());
        }

        // Partage QR Spotify
        const triggerShare = () => {
            if (this.currentTrack && typeof window.openQrShareModal === 'function') {
                window.openQrShareModal(this.currentTrack);
            } else if (navigator.share && this.currentTrack) {
                navigator.share({
                    title: this.currentTrack.title,
                    text: `Écoutez ${this.currentTrack.title} sur Micro ENSAD !`,
                    url: window.location.href
                }).catch(() => {});
            }
        };

        if (this.spotifyShareActionBtn) this.spotifyShareActionBtn.addEventListener('click', triggerShare);
        if (this.spotifyHeaderShareBtn) this.spotifyHeaderShareBtn.addEventListener('click', triggerShare);

        // Gesture tactile : Glisser vers le bas pour réduire le lecteur Spotify (Mobile Swipe-Down)
        if (this.spotifyPlayer) {
            let startY = 0;
            let currentDiffY = 0;
            let isSwiping = false;

            const swipeTarget = this.spotifyPlayer.querySelector('.spotify-header') || this.spotifyPlayer;
            swipeTarget.addEventListener('touchstart', (e) => {
                if (this.spotifyPlayer.scrollTop <= 5) {
                    startY = e.touches[0].clientY;
                    currentDiffY = 0;
                    isSwiping = true;
                }
            }, { passive: true });

            swipeTarget.addEventListener('touchmove', (e) => {
                if (!isSwiping) return;
                const diff = e.touches[0].clientY - startY;
                if (diff > 0) {
                    currentDiffY = diff;
                    this.spotifyPlayer.style.transform = `translateY(${Math.min(diff, 280)}px)`;
                    this.spotifyPlayer.style.transition = 'none';
                }
            }, { passive: true });

            const endSwipe = () => {
                if (!isSwiping) return;
                isSwiping = false;
                this.spotifyPlayer.style.transition = '';
                if (currentDiffY > 80) {
                    this.closeSpotifyPlayer();
                } else {
                    this.spotifyPlayer.style.transform = '';
                }
                startY = 0;
                currentDiffY = 0;
            };

            swipeTarget.addEventListener('touchend', endSwipe);
            swipeTarget.addEventListener('touchcancel', endSwipe);
        }
    }

    bindChaptersDrawerEvents() {
        if (this.chaptersDrawerCloseBtn) {
            this.chaptersDrawerCloseBtn.addEventListener('click', () => this.closeChaptersDrawer());
        }
        if (this.chaptersDrawerOverlay) {
            this.chaptersDrawerOverlay.addEventListener('click', () => this.closeChaptersDrawer());
        }

        // Gesture tactile : Glisser vers le bas sur la poignée du tiroir pour le fermer
        if (this.chaptersDrawer) {
            const drawerContent = this.chaptersDrawer.querySelector('.chapters-drawer-content');
            const handleBar = this.chaptersDrawer.querySelector('.drawer-drag-handle-bar');
            if (drawerContent && handleBar) {
                let startY = 0;
                let currentDiffY = 0;
                let isDraggingDrawer = false;

                handleBar.addEventListener('touchstart', (e) => {
                    startY = e.touches[0].clientY;
                    currentDiffY = 0;
                    isDraggingDrawer = true;
                }, { passive: true });

                handleBar.addEventListener('touchmove', (e) => {
                    if (!isDraggingDrawer) return;
                    const diff = e.touches[0].clientY - startY;
                    if (diff > 0) {
                        currentDiffY = diff;
                        drawerContent.style.transform = `translateY(${diff}px)`;
                        drawerContent.style.transition = 'none';
                    }
                }, { passive: true });

                const endDrawerSwipe = () => {
                    if (!isDraggingDrawer) return;
                    isDraggingDrawer = false;
                    drawerContent.style.transition = '';
                    if (currentDiffY > 60) {
                        this.closeChaptersDrawer();
                    } else {
                        drawerContent.style.transform = '';
                    }
                    startY = 0;
                    currentDiffY = 0;
                };

                handleBar.addEventListener('touchend', endDrawerSwipe);
                handleBar.addEventListener('touchcancel', endDrawerSwipe);
            }
        }
    }

    setupScrubber(sliderEl) {
        const startScrub = () => {
            this.isScrubbing = true;
        };
        sliderEl.addEventListener('mousedown', startScrub);
        sliderEl.addEventListener('touchstart', startScrub, { passive: true });

        sliderEl.addEventListener('input', (e) => {
            this.isScrubbing = true;
            const percent = parseFloat(e.target.value);
            this.syncSliders(percent);
            const duration = this.getDuration() || this.renderedDuration;
            if (duration > 0) {
                const previewSec = (percent / 100) * duration;
                if (this.currentTimeEl) this.currentTimeEl.textContent = this.formatTime(previewSec);
                if (this.spotifyCurrentTime) this.spotifyCurrentTime.textContent = this.formatTime(previewSec);
                this.updateChaptersProgress(previewSec, duration);
            }
        });

        const commitSeek = () => {
            const percent = parseFloat(sliderEl.value);
            const duration = this.getDuration() || this.renderedDuration;
            if (duration > 0) {
                const targetSec = (percent / 100) * duration;
                this.seekTo(targetSec);
            }
            setTimeout(() => {
                this.isScrubbing = false;
            }, 150);
        };

        sliderEl.addEventListener('change', commitSeek);
        sliderEl.addEventListener('mouseup', commitSeek);
        sliderEl.addEventListener('touchend', commitSeek);

        window.addEventListener('mouseup', () => {
            if (this.isScrubbing) commitSeek();
        });
        window.addEventListener('touchend', () => {
            if (this.isScrubbing) commitSeek();
        });
    }

    syncSliders(percent) {
        if (this.progressBar) {
            this.progressBar.value = percent;
            this.progressBar.style.setProperty('--progress', `${percent}%`);
        }
        if (this.spotifyProgressBar) {
            this.spotifyProgressBar.value = percent;
            this.spotifyProgressBar.style.setProperty('--progress', `${percent}%`);
        }
        if (this.miniPlayerProgressFill) {
            this.miniPlayerProgressFill.style.width = `${percent}%`;
        }
    }

    openSpotifyPlayer() {
        if (!this.spotifyPlayer) return;
        this.spotifyPlayer.style.transform = '';
        this.spotifyPlayer.classList.add('active');
        this.spotifyPlayer.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden'; // Verrouille le défilement de fond
    }

    closeSpotifyPlayer() {
        if (!this.spotifyPlayer) return;
        this.spotifyPlayer.style.transform = '';
        this.spotifyPlayer.classList.remove('active');
        this.spotifyPlayer.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    openChaptersDrawer() {
        if (!this.chaptersDrawer || !this.chaptersDrawerList) return;
        const drawerContent = this.chaptersDrawer.querySelector('.chapters-drawer-content');
        if (drawerContent) drawerContent.style.transform = '';
        this.renderChaptersDrawerList();
        this.chaptersDrawer.classList.remove('d-none');
        this.chaptersDrawer.setAttribute('aria-hidden', 'false');
    }

    closeChaptersDrawer() {
        if (!this.chaptersDrawer) return;
        const drawerContent = this.chaptersDrawer.querySelector('.chapters-drawer-content');
        if (drawerContent) drawerContent.style.transform = '';
        this.chaptersDrawer.classList.add('d-none');
        this.chaptersDrawer.setAttribute('aria-hidden', 'true');
    }

    renderChaptersDrawerList() {
        this.chaptersDrawerList.innerHTML = '';
        if (!this.currentChapters || this.currentChapters.length === 0) {
            this.chaptersDrawerList.innerHTML = '<p class="text-muted small text-center my-3">Aucun chapitre disponible pour cet épisode.</p>';
            return;
        }

        const current = this.getCurrentTime();
        this.currentChapters.forEach((ch, idx) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'drawer-chapter-item';
            const isActive = current >= (ch._start || 0) && current < (ch._end || 999999);
            if (isActive) btn.classList.add('active');

            const startSec = ch._start != null ? ch._start : (ch.seconds || 0);
            btn.innerHTML = `
                <span class="drawer-chapter-num-badge">Partie ${idx + 1}</span>
                <div class="flex-grow-1 text-truncate">
                    <div class="fw-bold small text-truncate text-white">${ch.title || 'Chapitre ' + (idx + 1)}</div>
                    <div class="drawer-chapter-time font-monospace">${this.formatTime(startSec)}</div>
                </div>
                <span class="material-symbols-rounded ${isActive ? 'text-primary' : 'text-muted'}" style="font-size: 1.2rem;">
                    ${isActive ? 'equalizer' : 'play_arrow'}
                </span>
            `;

            btn.addEventListener('click', () => {
                this.seekTo(startSec);
                this.closeChaptersDrawer();
            });

            this.chaptersDrawerList.appendChild(btn);
        });
    }

    jumpChapter(direction) {
        if (!this.currentChapters || this.currentChapters.length === 0) return;
        const current = this.getCurrentTime();
        let currentIdx = 0;
        for (let i = 0; i < this.currentChapters.length; i++) {
            if (current >= (this.currentChapters[i]._start || 0)) {
                currentIdx = i;
            }
        }
        const targetIdx = Math.max(0, Math.min(this.currentChapters.length - 1, currentIdx + direction));
        const targetSec = this.currentChapters[targetIdx]._start || 0;
        this.seekTo(targetSec);
    }

    cyclePlaybackSpeed() {
        const speeds = [1.0, 1.25, 1.5, 1.75, 2.0];
        let idx = speeds.indexOf(this.currentSpeed);
        idx = (idx + 1) % speeds.length;
        this.currentSpeed = speeds[idx];

        if (this.useYouTube && this.isYtReady && this.ytPlayer && this.ytPlayer.setPlaybackRate) {
            this.ytPlayer.setPlaybackRate(this.currentSpeed);
        } else {
            this.html5Audio.playbackRate = this.currentSpeed;
        }

        if (this.spotifySpeedText) {
            this.spotifySpeedText.textContent = `${this.currentSpeed}x`;
        }
    }

    toggleFavorite() {
        if (!this.currentTrack) return;
        const favs = JSON.parse(localStorage.getItem('ensad_favorites') || '[]');
        const trackId = String(this.currentTrack.id);
        const idx = favs.indexOf(trackId);
        let isFav = false;

        if (idx > -1) {
            favs.splice(idx, 1);
            isFav = false;
        } else {
            favs.push(trackId);
            isFav = true;
        }

        localStorage.setItem('ensad_favorites', JSON.stringify(favs));
        this.updateFavoriteUI(isFav);
    }

    updateFavoriteUI(isFav) {
        if (this.spotifyLikeIcon) {
            this.spotifyLikeIcon.textContent = isFav ? 'favorite' : 'favorite_border';
            this.spotifyLikeIcon.style.color = isFav ? '#EF4444' : '';
        }
    }

    downloadOrCacheEpisode() {
        if (!this.currentTrack) return;
        const trackId = String(this.currentTrack.id);
        const cacheKey = 'ensad_offline_episodes';
        const cached = JSON.parse(localStorage.getItem(cacheKey) || '[]');

        if (!cached.includes(trackId)) {
            cached.push(trackId);
            localStorage.setItem(cacheKey, JSON.stringify(cached));
        }

        // Sauvegarder également les métadonnées pour reprise hors-ligne
        localStorage.setItem('ensad_offline_meta_' + trackId, JSON.stringify({
            track: this.currentTrack,
            chapters: this.currentChapters,
            cachedAt: Date.now()
        }));

        // Mettre à jour l'icône et le badge
        if (this.spotifyDownloadIcon) {
            this.spotifyDownloadIcon.textContent = 'download_done';
            this.spotifyDownloadIcon.style.color = '#10B981';
        }
        if (this.desktopDownloadBtn) {
            this.desktopDownloadBtn.innerHTML = '<span class="material-symbols-rounded text-success" style="font-size: 1.15rem;">download_done</span>';
        }
        if (this.spotifyOfflineBadge) {
            this.spotifyOfflineBadge.classList.remove('d-none');
        }

        // Si une URL audio directe existe, proposer le téléchargement navigateur
        if (this.currentTrack.audioUrl && !this.currentTrack.audioUrl.includes('youtube.com')) {
            const a = document.createElement('a');
            a.href = this.currentTrack.audioUrl;
            a.download = `${this.currentTrack.title || 'podcast'}.mp3`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        // Notification visuelle douce
        this.showToast('✅ Épisode mémorisé ! Vous pouvez l\'écouter de manière fluide sans interruption.');
    }

    showToast(msg) {
        let toast = document.getElementById('ensadPlayerToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'ensadPlayerToast';
            toast.style.cssText = 'position: fixed; bottom: 85px; left: 50%; transform: translateX(-50%); background: #1264FF; color: white; padding: 10px 20px; border-radius: 99px; font-size: 0.85rem; font-weight: 600; z-index: 1090; box-shadow: 0 10px 25px rgba(0,0,0,0.4); pointer-events: none; transition: opacity 0.3s ease; text-align: center; max-width: 90vw;';
            document.body.appendChild(toast);
        }
        toast.textContent = msg;
        toast.style.opacity = '1';
        setTimeout(() => {
            toast.style.opacity = '0';
        }, 3500);
    }

    initVolumeStepButtons() {
        const volDown = document.getElementById('playerVolDownBtn');
        const volUp = document.getElementById('playerVolUpBtn');

        if (volDown) {
            volDown.addEventListener('click', () => {
                const cur = this.volumeSlider ? parseInt(this.volumeSlider.value, 10) : 85;
                this.setVolumeLevel(Math.max(0, cur - 10));
            });
        }

        if (volUp) {
            volUp.addEventListener('click', () => {
                const cur = this.volumeSlider ? parseInt(this.volumeSlider.value, 10) : 85;
                this.setVolumeLevel(Math.min(100, cur + 10));
            });
        }
    }

    setVolumeLevel(vol) {
        if (this.volumeSlider) {
            this.volumeSlider.value = vol;
            this.volumeSlider.style.setProperty('--progress', `${vol}%`);
        }
        if (this.volText) this.volText.textContent = vol + '%';

        if (this.isYtReady && this.ytPlayer && this.useYouTube) {
            if (this.ytPlayer.isMuted && this.ytPlayer.isMuted() && vol > 0) {
                this.ytPlayer.unMute();
            }
            this.ytPlayer.setVolume(vol);
        } else {
            if (this.html5Audio.muted && vol > 0) {
                this.html5Audio.muted = false;
            }
            this.html5Audio.volume = vol / 100;
        }

        this.updateVolumeIcon(vol);
        this.saveState();
    }

    toggleMute() {
        if (this.isYtReady && this.ytPlayer && this.useYouTube) {
            if (this.ytPlayer.isMuted()) {
                this.ytPlayer.unMute();
                const v = this.volumeSlider ? parseInt(this.volumeSlider.value, 10) : 80;
                this.updateVolumeIcon(v);
                if (this.volText) this.volText.textContent = v + '%';
            } else {
                this.ytPlayer.mute();
                this.updateVolumeIcon(0);
                if (this.volText) this.volText.textContent = '0%';
            }
        } else {
            if (this.html5Audio.muted) {
                this.html5Audio.muted = false;
                const v = this.volumeSlider ? parseInt(this.volumeSlider.value, 10) : 80;
                this.updateVolumeIcon(v);
                if (this.volText) this.volText.textContent = v + '%';
            } else {
                this.html5Audio.muted = true;
                this.updateVolumeIcon(0);
                if (this.volText) this.volText.textContent = '0%';
            }
        }
    }

    initDockControls() {
        const savedDock = localStorage.getItem('ensad_player_dock') || 'center';
        this.setDockPosition(savedDock);

        document.querySelectorAll('[data-dock-action]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const dock = btn.dataset.dockAction;
                this.setDockPosition(dock);
            });
        });
    }

    setDockPosition(dock) {
        if (!this.playerBar) return;
        this.playerBar.classList.remove('dock-left', 'dock-center', 'dock-right');
        this.playerBar.classList.add('dock-' + dock);
        localStorage.setItem('ensad_player_dock', dock);

        document.querySelectorAll('[data-dock-action]').forEach(b => {
            b.classList.toggle('active', b.dataset.dockAction === dock);
        });
    }

    bindPageButtons() {
        document.addEventListener('click', (e) => {
            const playBtn = e.target.closest('[data-play-episode]');
            if (playBtn) {
                e.preventDefault();
                const startAt = parseInt(playBtn.dataset.chapterSeconds || playBtn.dataset.seekTime || '0', 10) || 0;
                const trackData = {
                    id: playBtn.dataset.episodeId,
                    title: playBtn.dataset.title,
                    host: playBtn.dataset.host,
                    podcast: playBtn.dataset.podcast || 'Micro ENSAD',
                    cover: playBtn.dataset.cover,
                    youtubeId: playBtn.dataset.youtubeId,
                    audioUrl: playBtn.dataset.audioUrl,
                    duration: playBtn.dataset.duration || '00:00'
                };
                this.loadAndPlay(trackData, startAt);
                return;
            }

            const seekBtn = e.target.closest('[data-chapter-seconds], [data-seek-time]');
            if (!seekBtn) return;
            const seconds = parseInt(seekBtn.dataset.chapterSeconds || seekBtn.dataset.seekTime, 10);
            if (!isNaN(seconds)) {
                this.seekTo(seconds);
            }
        });
    }

    loadAndPlay(track, startAt = 0) {
        if (this.currentTrack && String(this.currentTrack.id) === String(track.id) && startAt <= 0) {
            this.togglePlay();
            return;
        }

        this.currentTrack = track;

        // Récupérer les chapitres
        let epData = null;
        if (window.ENSAD_EPISODES && Array.isArray(window.ENSAD_EPISODES)) {
            epData = window.ENSAD_EPISODES.find(e => String(e.id) === String(track.id));
        }
        this.currentChapters = (track.chapters && track.chapters.length) ? track.chapters : (epData && epData.chapters ? epData.chapters : []);
        const estDur = this.parseDurationSec(track.duration);
        if (estDur > 0) {
            this.renderChapters(estDur);
        }

        this.saveState();

        // YouTube ID
        let ytId = track.youtubeId;
        if (!ytId && track.audioUrl && (track.audioUrl.includes('youtube.com') || track.audioUrl.includes('youtu.be'))) {
            ytId = this.extractYouTubeId(track.audioUrl);
        }

        const coverSrc = track.cover || (ytId ? `https://img.youtube.com/vi/${ytId}/hqdefault.jpg` : 'assets/images/micro-ensad-icon.svg');

        // ── MISE À JOUR DESKTOP ──
        if (this.titleEl) this.titleEl.textContent = track.title;
        if (this.artistEl) this.artistEl.textContent = track.host;
        if (this.coverImg) this.coverImg.src = coverSrc;
        if (this.durationEl) this.durationEl.textContent = track.duration;
        if (this.progressBar) {
            this.progressBar.value = 0;
            this.progressBar.style.setProperty('--progress', '0%');
        }
        if (this.currentTimeEl) this.currentTimeEl.textContent = '00:00';
        if (this.playerBar) this.playerBar.classList.remove('d-none');

        // ── MISE À JOUR MINI-LECTEUR MOBILE ──
        if (this.miniPlayerTitle) this.miniPlayerTitle.textContent = track.title;
        if (this.miniPlayerArtist) this.miniPlayerArtist.textContent = track.host;
        if (this.miniPlayerCover) this.miniPlayerCover.src = coverSrc;
        if (this.miniPlayerProgressFill) this.miniPlayerProgressFill.style.width = '0%';
        if (this.mobileMiniPlayer) this.mobileMiniPlayer.classList.remove('d-none');

        // ── MISE À JOUR SPOTIFY MOBILE ──
        if (this.spotifyTitle) this.spotifyTitle.textContent = track.title;
        if (this.spotifyArtist) this.spotifyArtist.textContent = track.host;
        if (this.spotifyCover) this.spotifyCover.src = coverSrc;
        if (this.spotifyPodcastName) this.spotifyPodcastName.textContent = track.podcast || 'Micro ENSAD';
        if (this.spotifyDuration) this.spotifyDuration.textContent = track.duration;
        if (this.spotifyCurrentTime) this.spotifyCurrentTime.textContent = '00:00';
        if (this.spotifyProgressBar) {
            this.spotifyProgressBar.value = 0;
            this.spotifyProgressBar.style.setProperty('--progress', '0%');
        }

        // Vérifier l'état Favori & Hors-ligne
        const favs = JSON.parse(localStorage.getItem('ensad_favorites') || '[]');
        this.updateFavoriteUI(favs.includes(String(track.id)));

        const cached = JSON.parse(localStorage.getItem('ensad_offline_episodes') || '[]');
        const isOffline = cached.includes(String(track.id));
        if (this.spotifyOfflineBadge) {
            this.spotifyOfflineBadge.classList.toggle('d-none', !isOffline);
        }
        if (this.spotifyDownloadIcon) {
            this.spotifyDownloadIcon.textContent = isOffline ? 'download_done' : 'cloud_download';
            this.spotifyDownloadIcon.style.color = isOffline ? '#10B981' : '';
        }

        // Sur mobile, ouvrir directement l'interface Spotify pour une immersion immédiate
        if (window.innerWidth < 768) {
            this.openSpotifyPlayer();
        }

        // Lancement audio
        if (ytId && this.isYtReady && this.ytPlayer) {
            this.useYouTube = true;
            this.html5Audio.pause();
            this.startYouTubePlayback(ytId, startAt);
        } else if (ytId) {
            this.useYouTube = true;
            this.pendingYouTubeTrack = { youtubeId: ytId, startAt };
            this.setPlayState(false);
            if (typeof showToast === 'function') {
                showToast("Preparation du lecteur YouTube...");
            }
        } else if (track.audioUrl) {
            this.useYouTube = false;
            if (this.isYtReady && this.ytPlayer) this.ytPlayer.pauseVideo();
            this.html5Audio.src = track.audioUrl;
            this.html5Audio.play().then(() => this.setPlayState(true)).catch(() => {});
        } else {
            this.setPlayState(false);
            if (typeof showToast === 'function') {
                showToast("Aucune source audio disponible pour cet episode.");
            }
        }
    }

    startYouTubePlayback(ytId, startAt = 0) {
        if (!ytId || !this.isYtReady || !this.ytPlayer) return;
        this.useYouTube = true;
        this.html5Audio.pause();
        this.ytPlayer.loadVideoById({ videoId: ytId, startSeconds: Math.max(0, startAt || 0) });
        this.setPlayState(true);
        this.startTimer();
    }

    togglePlay() {
        if (this.useYouTube && this.isYtReady && this.ytPlayer) {
            const state = this.ytPlayer.getPlayerState();
            if (state === 1) {
                this.ytPlayer.pauseVideo();
                this.setPlayState(false);
            } else {
                this.ytPlayer.playVideo();
                this.setPlayState(true);
            }
        } else {
            if (this.html5Audio.paused) {
                this.html5Audio.play();
                this.setPlayState(true);
            } else {
                this.html5Audio.pause();
                this.setPlayState(false);
            }
        }
    }

    seekTo(seconds) {
        const duration = this.getDuration() || this.renderedDuration;
        if (!duration || duration <= 0) return;

        let safeSec = seconds;
        if (safeSec >= duration && this.currentTrack) {
            const statedSec = this.parseDurationSec(this.currentTrack.duration);
            if (statedSec > duration) {
                safeSec = (safeSec / statedSec) * duration;
            }
        }
        safeSec = Math.max(0, Math.min(duration > 0.5 ? duration - 0.5 : duration, safeSec));

        this.lastSeekTime = safeSec;
        this.seekLockUntil = Date.now() + 1000;

        if (this.useYouTube && this.isYtReady && this.ytPlayer) {
            this.ytPlayer.seekTo(safeSec, true);
        } else {
            this.html5Audio.currentTime = safeSec;
        }

        const progress = (safeSec / duration) * 100;
        this.syncSliders(progress);

        if (this.currentTimeEl) this.currentTimeEl.textContent = this.formatTime(safeSec);
        if (this.spotifyCurrentTime) this.spotifyCurrentTime.textContent = this.formatTime(safeSec);

        this.updateChaptersProgress(safeSec, duration);
    }

    seekBy(seconds) {
        const current = this.getCurrentTime();
        this.seekTo(Math.max(0, current + seconds));
    }

    getCurrentTime() {
        if (Date.now() < this.seekLockUntil) {
            return this.lastSeekTime;
        }
        if (this.useYouTube && this.isYtReady && this.ytPlayer && this.ytPlayer.getCurrentTime) {
            return this.ytPlayer.getCurrentTime() || 0;
        }
        return this.html5Audio.currentTime || 0;
    }

    getDuration() {
        if (this.useYouTube && this.isYtReady && this.ytPlayer && this.ytPlayer.getDuration) {
            return this.ytPlayer.getDuration() || 0;
        }
        return this.html5Audio.duration || 0;
    }

    onPlayerStateChange(event) {
        if (event.data === 1) { // PLAYING
            this.setPlayState(true);
            this.startTimer();
            this.saveState();
        } else if (event.data === 2) { // PAUSED
            this.setPlayState(false);
            this.stopTimer();
            this.saveState();
        } else if (event.data === 0) { // ENDED
            this.setPlayState(false);
            this.stopTimer();
            this.syncSliders(100);
        }
    }

    startTimer() {
        this.stopTimer();
        this.timer = setInterval(() => {
            this.onTimeUpdate();
        }, 500);
    }

    stopTimer() {
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
    }

    onTimeUpdate() {
        if (this.isScrubbing) return;
        if (Date.now() < this.seekLockUntil) return;

        const current = this.getCurrentTime();
        const duration = this.getDuration();
        if (!duration || duration <= 0) return;

        if (Math.abs(duration - this.renderedDuration) > 1.5) {
            this.renderChapters(duration);
        }

        this.updateChaptersProgress(current, duration);

        const progress = (current / duration) * 100;
        this.syncSliders(progress);

        const formattedCur = this.formatTime(current);
        const formattedDur = this.formatTime(duration);

        if (this.currentTimeEl) this.currentTimeEl.textContent = formattedCur;
        if (this.durationEl && duration > 10) this.durationEl.textContent = formattedDur;

        if (this.spotifyCurrentTime) this.spotifyCurrentTime.textContent = formattedCur;
        if (this.spotifyDuration && duration > 10) this.spotifyDuration.textContent = formattedDur;

        const now = Date.now();
        if (now - this.lastSavedTime >= 5000) {
            this.lastSavedTime = now;
            this.saveState();
        }
    }

    setPlayState(playing) {
        this.isPlaying = playing;
        const iconName = playing ? 'pause' : 'play_arrow';

        if (this.playIcon) this.playIcon.textContent = iconName;
        if (this.miniPlayerPlayIcon) this.miniPlayerPlayIcon.textContent = iconName;
        if (this.spotifyPlayIcon) this.spotifyPlayIcon.textContent = iconName;

        if (this.waveBars) {
            this.waveBars.classList.toggle('playing', playing);
        }

        // Mettre à jour les boutons de la page
        document.querySelectorAll('[data-play-episode]').forEach(btn => {
            const icon = btn.querySelector('.material-symbols-rounded') || btn.querySelector('i');
            if (btn.dataset.episodeId === (this.currentTrack && String(this.currentTrack.id))) {
                if (icon) {
                    if (icon.classList.contains('material-symbols-rounded')) {
                        icon.textContent = iconName;
                    } else {
                        icon.className = playing ? 'bi bi-pause-fill' : 'bi bi-play-fill';
                    }
                }
            } else if (icon) {
                if (icon.classList.contains('material-symbols-rounded')) {
                    icon.textContent = 'play_arrow';
                } else {
                    icon.className = 'bi bi-play-fill';
                }
            }
        });
    }

    updateVolumeIcon(vol) {
        if (!this.muteIcon) return;
        if (vol === 0) {
            this.muteIcon.textContent = 'volume_off';
        } else if (vol < 50) {
            this.muteIcon.textContent = 'volume_down';
        } else {
            this.muteIcon.textContent = 'volume_up';
        }
    }

    formatTime(sec) {
        if (isNaN(sec)) return '00:00';
        const mins = Math.floor(sec / 60);
        const secs = Math.floor(sec % 60);
        return (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs;
    }

    saveState() {
        if (!this.currentTrack) return;
        try {
            const state = {
                youtubeId: this.currentTrack.youtubeId,
                title:     this.currentTrack.title,
                host:      this.currentTrack.host,
                podcast:   this.currentTrack.podcast,
                cover:     this.currentTrack.cover,
                duration:  this.currentTrack.duration,
                episodeId: this.currentTrack.id,
                chapters:  this.currentChapters,
                currentTime: this.getCurrentTime(),
                volume:    this.volumeSlider ? parseInt(this.volumeSlider.value, 10) : 85,
                isPlaying: this.isPlaying,
                savedAt:   Date.now()
            };
            localStorage.setItem('ensad_player_state', JSON.stringify(state));

            // Sauvegarde de la progression par épisode individuel
            localStorage.setItem('ensad_progress_' + this.currentTrack.id, JSON.stringify({
                currentTime: state.currentTime,
                duration: this.getDuration(),
                date: Date.now()
            }));
        } catch (e) {}
    }

    restoreState() {
        try {
            const raw = localStorage.getItem('ensad_player_state');
            if (!raw) return;

            const state = JSON.parse(raw);
            const MAX_AGE_MS = 24 * 60 * 60 * 1000;
            if (!state || !state.savedAt || (Date.now() - state.savedAt) > MAX_AGE_MS) return;
            if (!state.youtubeId) return;

            this.currentTrack = {
                id:        state.episodeId,
                youtubeId: state.youtubeId,
                title:     state.title,
                host:      state.host,
                podcast:   state.podcast,
                cover:     state.cover,
                duration:  state.duration
            };

            if (state.chapters && Array.isArray(state.chapters) && state.chapters.length > 0) {
                this.currentChapters = state.chapters;
            } else {
                let epData = null;
                if (window.ENSAD_EPISODES && Array.isArray(window.ENSAD_EPISODES)) {
                    epData = window.ENSAD_EPISODES.find(e => String(e.id) === String(state.episodeId));
                }
                this.currentChapters = epData && epData.chapters ? epData.chapters : [];
            }
            const estDur = this.parseDurationSec(state.duration);
            if (estDur > 0) {
                this.renderChapters(estDur);
            }

            const coverSrc = state.cover || `https://img.youtube.com/vi/${state.youtubeId}/hqdefault.jpg`;

            // Desktop
            if (this.titleEl)    this.titleEl.textContent   = state.title  || '';
            if (this.artistEl)   this.artistEl.textContent  = state.host   || '';
            if (this.durationEl) this.durationEl.textContent = state.duration || '';
            if (this.coverImg)   this.coverImg.src = coverSrc;
            if (this.playerBar)  this.playerBar.classList.remove('d-none');

            // Mobile Mini
            if (this.miniPlayerTitle)  this.miniPlayerTitle.textContent = state.title || '';
            if (this.miniPlayerArtist) this.miniPlayerArtist.textContent = state.host || '';
            if (this.miniPlayerCover)  this.miniPlayerCover.src = coverSrc;
            if (this.mobileMiniPlayer) this.mobileMiniPlayer.classList.remove('d-none');

            // Mobile Spotify
            if (this.spotifyTitle)       this.spotifyTitle.textContent = state.title || '';
            if (this.spotifyArtist)      this.spotifyArtist.textContent = state.host || '';
            if (this.spotifyCover)       this.spotifyCover.src = coverSrc;
            if (this.spotifyPodcastName) this.spotifyPodcastName.textContent = state.podcast || 'Micro ENSAD';
            if (this.spotifyDuration)    this.spotifyDuration.textContent = state.duration || '';

            this.setVolumeLevel(state.volume != null ? state.volume : 85);

            const savedDock = localStorage.getItem('ensad_player_dock') || 'center';
            this.setDockPosition(savedDock);

            this.useYouTube = true;
            this.ytPlayer.loadVideoById(state.youtubeId);

            setTimeout(() => {
                if (!this.ytPlayer) return;
                this.ytPlayer.seekTo(state.currentTime || 0, true);
                if (state.isPlaying) {
                    this.ytPlayer.playVideo();
                } else {
                    this.ytPlayer.pauseVideo();
                }
            }, 500);

        } catch (e) {}
    }

    parseDurationSec(durStr) {
        if (!durStr || typeof durStr !== 'string') return 0;
        const parts = durStr.trim().split(':').map(Number);
        if (parts.length === 3) return (parts[0] || 0) * 3600 + (parts[1] || 0) * 60 + (parts[2] || 0);
        if (parts.length === 2) return (parts[0] || 0) * 60 + (parts[1] || 0);
        return 0;
    }

    renderChapters(totalDuration) {
        if (!totalDuration || totalDuration <= 0) return;
        this.renderedDuration = totalDuration;

        const tracks = [this.chaptersTrack, this.spotifyChaptersTrack].filter(Boolean);
        tracks.forEach(trackEl => { trackEl.innerHTML = ''; });

        if (!this.currentChapters || this.currentChapters.length === 0) {
            if (this.chapterHeader) this.chapterHeader.classList.add('d-none');
            tracks.forEach(trackEl => {
                const singleSeg = document.createElement('div');
                singleSeg.className = 'player-chapter-segment';
                singleSeg.style.width = '100%';
                singleSeg.dataset.start = 0;
                singleSeg.dataset.end = totalDuration;
                singleSeg.innerHTML = '<div class="player-chapter-segment-fill"></div>';
                trackEl.appendChild(singleSeg);
            });
            return;
        }

        const chs = [...this.currentChapters].map(c => ({ ...c })).sort((a, b) => (a.seconds || 0) - (b.seconds || 0));
        const numChapters = chs.length;

        // Détection de dépassement de durée
        const maxChSec = Math.max(...chs.map(c => c.seconds || 0));
        let scaleFactor = 1;
        if (maxChSec >= totalDuration && totalDuration > 10) {
            const statedSec = this.currentTrack ? this.parseDurationSec(this.currentTrack.duration) : 0;
            const refDuration = Math.max(statedSec, maxChSec + 60);
            scaleFactor = totalDuration / refDuration;
        }

        for (let i = 0; i < numChapters; i++) {
            const rawStart = (chs[i].seconds || 0) * scaleFactor;
            const minAllowedStart = i > 0 ? chs[i - 1]._start + 2 : 0;
            const maxAllowedStart = totalDuration - (numChapters - i) * 2;
            chs[i]._start = Math.max(minAllowedStart, Math.min(rawStart, Math.max(minAllowedStart, maxAllowedStart)));
        }

        for (let i = 0; i < numChapters; i++) {
            let end = (i < numChapters - 1) ? chs[i + 1]._start : totalDuration;
            if (end <= chs[i]._start) {
                end = Math.min(totalDuration, chs[i]._start + 2);
            }
            chs[i]._end = end;
        }

        // Rendu des segments sur desktop et mobile
        tracks.forEach(trackEl => {
            chs.forEach((ch, idx) => {
                const segDuration = Math.max(1, ch._end - ch._start);
                const widthPercent = (segDuration / totalDuration) * 100;

                const seg = document.createElement('div');
                seg.className = 'player-chapter-segment';
                seg.dataset.chIndex = idx;
                seg.dataset.start = ch._start;
                seg.dataset.end = ch._end;
                seg.style.width = `${widthPercent.toFixed(3)}%`;
                seg.innerHTML = '<div class="player-chapter-segment-fill"></div>';

                trackEl.appendChild(seg);
            });
        });

        this.currentChapters = chs;

        const firstTitle = chs[0].title || 'Introduction';
        if (this.chapterHeader) this.chapterHeader.classList.remove('d-none');
        if (this.chapterPillNum) this.chapterPillNum.textContent = `Partie 1/${chs.length}`;
        if (this.chapterTitleText) this.chapterTitleText.textContent = firstTitle;
        if (this.spotifyChapterTitle) this.spotifyChapterTitle.textContent = `Partie 1/${chs.length} • ${firstTitle}`;
        if (this.miniPlayerChapterBadge) {
            this.miniPlayerChapterBadge.textContent = `Partie 1/${chs.length}`;
            this.miniPlayerChapterBadge.classList.remove('d-none');
        }
    }

    updateChaptersProgress(current, duration) {
        const tracks = [this.chaptersTrack, this.spotifyChaptersTrack].filter(Boolean);
        if (tracks.length === 0) return;

        if (!this.currentChapters || this.currentChapters.length === 0) {
            tracks.forEach(trackEl => {
                const fill = trackEl.querySelector('.player-chapter-segment-fill');
                if (fill && duration > 0) {
                    const pct = Math.min(100, Math.max(0, (current / duration) * 100));
                    fill.style.width = `${pct.toFixed(2)}%`;
                }
            });
            return;
        }

        let activeIdx = 0;
        tracks.forEach(trackEl => {
            const segs = trackEl.querySelectorAll('.player-chapter-segment');
            segs.forEach((seg, idx) => {
                const start = parseFloat(seg.dataset.start) || 0;
                const end = parseFloat(seg.dataset.end) || duration;
                const fill = seg.querySelector('.player-chapter-segment-fill');
                if (!fill) return;

                if (current >= end) {
                    fill.style.width = '100%';
                    seg.classList.remove('active');
                } else if (current < start) {
                    fill.style.width = '0%';
                    seg.classList.remove('active');
                } else {
                    activeIdx = idx;
                    const segLen = end - start;
                    const pct = segLen > 0 ? Math.min(100, Math.max(0, ((current - start) / segLen) * 100)) : 0;
                    fill.style.width = `${pct.toFixed(2)}%`;
                    seg.classList.add('active');
                }
            });
        });

        if (this.currentChapters[activeIdx]) {
            const activeCh = this.currentChapters[activeIdx];
            const chName = activeCh.title || `Chapitre ${activeIdx + 1}`;
            const partNumText = `Partie ${activeIdx + 1}/${this.currentChapters.length}`;

            if (this.chapterHeader) this.chapterHeader.classList.remove('d-none');
            if (this.chapterPillNum) this.chapterPillNum.textContent = partNumText;
            if (this.chapterTitleText) this.chapterTitleText.textContent = chName;

            if (this.spotifyChapterTitle) {
                this.spotifyChapterTitle.textContent = `${partNumText} • ${chName}`;
            }
            if (this.miniPlayerChapterBadge) {
                this.miniPlayerChapterBadge.textContent = partNumText;
                this.miniPlayerChapterBadge.classList.remove('d-none');
            }
        }
    }

    showHoverTooltipAt(hoverSec, pixelX) {
        if (!this.chapterTooltip) return;
        const duration = this.getDuration() || this.renderedDuration || 1;
        const safeSec = Math.max(0, Math.min(duration, hoverSec));

        let foundCh = null;
        let foundIdx = 0;
        if (this.currentChapters && this.currentChapters.length > 0) {
            for (let i = 0; i < this.currentChapters.length; i++) {
                const ch = this.currentChapters[i];
                if (safeSec >= (ch._start != null ? ch._start : (ch.seconds || 0))) {
                    foundCh = ch;
                    foundIdx = i;
                }
            }
        }

        if (foundCh) {
            if (this.tooltipNum) this.tooltipNum.textContent = `Partie ${foundIdx + 1}/${this.currentChapters.length}`;
            if (this.tooltipTitle) this.tooltipTitle.textContent = foundCh.title || '';
            if (this.tooltipTime) this.tooltipTime.textContent = `${this.formatTime(safeSec)} (${this.formatTime(foundCh._start || 0)} - ${this.formatTime(foundCh._end || duration)})`;
        } else {
            if (this.tooltipNum) this.tooltipNum.textContent = 'Lecture';
            if (this.tooltipTitle) this.tooltipTitle.textContent = this.currentTrack ? this.currentTrack.title : '';
            if (this.tooltipTime) this.tooltipTime.textContent = this.formatTime(safeSec);
        }

        this.chapterTooltip.style.left = `${pixelX}px`;
        this.chapterTooltip.classList.remove('d-none');
    }

    hideChapterTooltip() {
        if (this.chapterTooltip) {
            this.chapterTooltip.classList.add('d-none');
        }
    }

    extractYouTubeId(url) {
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
        const match = url.match(regExp);
        return (match && match[2].length === 11) ? match[2] : null;
    }
}

// Initialisation globale
document.addEventListener('DOMContentLoaded', () => {
    window.ensadPlayer = new EnsadPodcastEngine();
});
