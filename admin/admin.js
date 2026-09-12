/**
 * Admin JavaScript - Micro ENSAD
 * Fonctions pour l'ajout/suppression dynamique d'étudiants, chapitres,
 * et modale de confirmation de suppression.
 */

// Suppression de ligne dynamique (contributeur ou chapitre)
function removeRow(btn) {
    const row = btn.closest('.contrib-row, .chapter-row');
    if (row) {
        const parent = row.parentElement;
        if (parent.children.length > 1) {
            row.remove();
        } else {
            // S'il ne reste qu'une seule ligne, vider les champs plutôt que supprimer
            row.querySelectorAll('input').forEach(input => input.value = '');
        }
    }
}

// Ajouter un champ étudiant contributeur
function addContrib() {
    const list = document.getElementById('contribList');
    if (!list) return;

    const row = document.createElement('div');
    row.className = 'contrib-row';
    row.innerHTML = `
        <input type="text" name="contributors[]" class="form-control" placeholder="Prénom Nom de l'étudiant(e)">
        <button type="button" class="btn-icon del" onclick="removeRow(this)" title="Retirer">
            <i class="bi bi-x-lg"></i>
        </button>
    `;
    list.appendChild(row);
    row.querySelector('input').focus();
}

// Ajouter un chapitre
function addChapter() {
    const list = document.getElementById('chapterList');
    if (!list) return;

    const row = document.createElement('div');
    row.className = 'chapter-row';
    row.innerHTML = `
        <input type="text" name="chapter_time[]" class="form-control font-monospace" placeholder="00:00">
        <input type="text" name="chapter_title[]" class="form-control" placeholder="Titre du chapitre...">
        <button type="button" class="btn-icon del" onclick="removeRow(this)" title="Retirer">
            <i class="bi bi-x-lg"></i>
        </button>
    `;
    list.appendChild(row);
    row.querySelector('input').focus();
}

// Ouvrir la modale de confirmation de suppression
function confirmDelete(id, title) {
    const idInput = document.getElementById('deleteEpisodeId');
    const titleEl = document.getElementById('deleteModalTitle');
    const modalEl = document.getElementById('deleteModal');

    if (idInput) idInput.value = id;
    if (titleEl) titleEl.textContent = '« ' + title + ' »';
    if (modalEl) {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }
}

// Prévisualisation interactive de l'image de couverture avec Dropzone
document.addEventListener('DOMContentLoaded', () => {
    const coverSelect   = document.getElementById('coverSelect');
    const coverUpload   = document.getElementById('coverFileInput');
    const dropzone      = document.getElementById('adminDropzone');
    const previewImg    = document.getElementById('coverPreviewImg');
    const previewBadge  = document.getElementById('coverPreviewBadge');
    const fileInfo      = document.getElementById('coverFileInfo');

    function formatBytes(bytes, decimals = 1) {
        if (!+bytes) return '0 Octets';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Octets', 'Ko', 'Mo', 'Go'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
    }

    function handleFile(file) {
        if (!file || !file.type.startsWith('image/')) {
            alert('Veuillez sélectionner un fichier image valide (JPG, PNG, WebP, GIF).');
            return;
        }
        const reader = new FileReader();
        reader.onload = (e) => {
            if (previewImg) previewImg.src = e.target.result;
            if (previewBadge) {
                previewBadge.textContent = 'Nouvelle Image Prête';
                previewBadge.className = 'badge bg-success font-monospace';
            }
            if (fileInfo) {
                fileInfo.textContent = file.name + ' (' + formatBytes(file.size) + ')';
            }
            if (coverSelect) coverSelect.value = '';
        };
        reader.readAsDataURL(file);
    }

    if (dropzone && coverUpload) {
        // Clic sur la zone de dropzone déclenche le sélecteur
        dropzone.addEventListener('click', () => {
            coverUpload.click();
        });

        // Gestion du drag and drop
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('dragover');
            }, false);
        });

        dropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files.length > 0) {
                coverUpload.files = files;
                handleFile(files[0]);
            }
        });
    }

    if (coverUpload) {
        coverUpload.addEventListener('change', (e) => {
            if (e.target.files && e.target.files.length > 0) {
                handleFile(e.target.files[0]);
            }
        });
    }

    if (coverSelect && previewImg) {
        coverSelect.addEventListener('change', () => {
            if (coverSelect.value) {
                previewImg.src = '../' + coverSelect.value;
                if (previewBadge) {
                    previewBadge.textContent = 'Image Bibliothèque';
                    previewBadge.className = 'badge bg-info text-dark font-monospace';
                }
                if (fileInfo) {
                    fileInfo.textContent = coverSelect.options[coverSelect.selectedIndex].text;
                }
                if (coverUpload) coverUpload.value = '';
            }
        });
    }
});

