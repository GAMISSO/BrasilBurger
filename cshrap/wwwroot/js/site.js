let prixBase = 0;
// Try to read a server-provided base price from the DOM (data-prix-base on #prixBase)
const prixBaseEl = document.getElementById('prixBase');
if (prixBaseEl) {
    const dataPrix = prixBaseEl.dataset.prixBase;
    if (dataPrix) {
        prixBase = parseFloat(dataPrix) || 0;
    } else {
        // fallback: parse digits from current text content if any (e.g. "12 000 FCFA")
        const text = prixBaseEl.textContent || '';
        const digits = text.replace(/[^0-9]/g, '');
        prixBase = digits ? parseInt(digits, 10) : 0;
    }
    // normalize displayed base price
    prixBaseEl.textContent = (prixBase).toLocaleString() + ' FCFA';
}
let prixComplements = 0;
let prixLivraison = 0;
let quantite = 1;

function calculerTotal() {
    const total = (prixBase * quantite) + prixComplements + prixLivraison;
    document.getElementById('prixBase').textContent = (prixBase * quantite).toLocaleString() + ' FCFA';
    document.getElementById('totalFinal').textContent = total.toLocaleString() + ' FCFA';
    document.getElementById('quantiteDisplay').textContent = quantite;
}

// Gestion des compléments
document.querySelectorAll('.complement-checkbox').forEach(cb => {
    cb.addEventListener('change', function () {
        const prix = parseFloat(this.dataset.prix);
        if (this.checked) {
            prixComplements += prix;
        } else {
            prixComplements -= prix;
        }
        document.getElementById('prixComplements').textContent = prixComplements.toLocaleString() + ' FCFA';
        document.getElementById('complementsTotal').style.display = prixComplements > 0 ? 'flex' : 'none';
        calculerTotal();
    });
});

// Gestion de la quantité
const quantiteInput = document.getElementById('quantite');
if (quantiteInput) {
    quantiteInput.addEventListener('input', function () {
        quantite = parseInt(this.value) || 1;
        calculerTotal();
    });
}

// Gestion du type de service
const typeServiceRadios = document.querySelectorAll('input[name="typeService"]');
if (typeServiceRadios && typeServiceRadios.length > 0) {
    typeServiceRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            const livraisonDetails = document.getElementById('livraisonDetails');
            if (!livraisonDetails) return;
            if (this.value === 'LIVRAISON') {
                livraisonDetails.style.display = 'block';
            } else {
                livraisonDetails.style.display = 'none';
                prixLivraison = 0;
                const livraisonTotalEl = document.getElementById('livraisonTotal');
                if (livraisonTotalEl) livraisonTotalEl.style.display = 'none';
                calculerTotal();
            }
        });
    });
}

// Gestion de la zone de livraison
const zoneSelect = document.getElementById('zoneSelect');
if (zoneSelect) {
    zoneSelect.addEventListener('change', function () {
        const option = this.options[this.selectedIndex];
        prixLivraison = parseFloat(option?.dataset?.prix) || 0;
        const prixLivraisonEl = document.getElementById('prixLivraison');
        if (prixLivraisonEl) prixLivraisonEl.textContent = prixLivraison.toLocaleString() + ' FCFA';
        const livraisonTotalEl = document.getElementById('livraisonTotal');
        if (livraisonTotalEl) livraisonTotalEl.style.display = prixLivraison > 0 ? 'flex' : 'none';
        calculerTotal();
    });
}

// Initialiser
calculerTotal();