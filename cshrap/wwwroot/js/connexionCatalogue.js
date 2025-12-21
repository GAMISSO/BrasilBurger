// Gestion des catégories
const categoryBtns = document.querySelectorAll('.category-btn');
categoryBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        categoryBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    });
});

// Gestion des modals
function showLoginModal(itemType, itemId) {
    closeAllModals();
    document.getElementById('loginModal').classList.add('active');

    var returnInput = document.getElementById('loginReturnUrl');
    if (itemType === 'Order') {
        returnInput.value = '/Order/Create';
    } else if (itemType && itemId) {
        returnInput.value = '/Order/Create?itemType=' + encodeURIComponent(itemType) + '&itemId=' + encodeURIComponent(itemId);
    } else {
        returnInput.value = '';
    }

    document.body.style.overflow = 'hidden';
}

function showInscriptionModal(itemType, itemId) {
    closeAllModals();
    document.getElementById('inscriptionModal').classList.add('active');

    var returnInput = document.getElementById('registerReturnUrl');
    // If called without args, copy the login returnUrl so registration keeps the intended destination
    if (typeof itemType === 'undefined') {
        var loginReturn = document.getElementById('loginReturnUrl');
        if (loginReturn) returnInput.value = loginReturn.value || '';
        else returnInput.value = '';
    }
    else if (itemType === 'Order') {
        returnInput.value = '/Order/Create';
    } else if (itemType && itemId) {
        returnInput.value = '/Order/Create?itemType=' + encodeURIComponent(itemType) + '&itemId=' + encodeURIComponent(itemId);
    } else {
        returnInput.value = '';
    }

    document.body.style.overflow = 'hidden';
}

function closeAllModals() {
    document.getElementById('loginModal').classList.remove('active');
    document.getElementById('inscriptionModal').classList.remove('active');
    document.body.style.overflow = 'auto';
}

function closeModalOnOverlay(event, modalId) {
    if (event.target.id === modalId) {
        closeAllModals();
    }
}

function handleLogin() {
    const login = document.getElementById('login').value;
    const password = document.getElementById('password').value;

    if (login && password) {
        document.getElementById('loginForm').submit();
    } else {
        alert('Veuillez remplir tous les champs.');
    }
}

function handleInscription() {
    const nom = document.getElementById('nom').value;
    const prenom = document.getElementById('prenom').value;
    const adresse = document.getElementById('adresse').value;
    const telephone = document.getElementById('telephone').value;
    const email = document.getElementById('email').value;
    const login = document.getElementById('login-inscription').value;
    const password = document.getElementById('password-inscription').value;

    if (nom && prenom && adresse && telephone && email && login && password) {
        document.getElementById('registerForm').submit();
    } else {
        alert('Veuillez remplir tous les champs.');
    }
}

// Fermer les modals avec la touche Échap
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeAllModals();
    }
});


let quantity = 1;
        const unitPrice = 1200;

        function updateTotal() {
            const total = quantity * unitPrice;
            document.getElementById('totalPrice').textContent = total + ' FCFA';
            document.querySelectorAll('.total-amount').forEach(el => {
                el.textContent = total + ' FCFA';
            });
        }

        function increaseQuantity() {
            quantity++;
            document.getElementById('quantity').textContent = quantity;
            updateTotal();
        }

        function decreaseQuantity() {
            if (quantity > 1) {
                quantity--;
                document.getElementById('quantity').textContent = quantity;
                updateTotal();
            }
        }

        function goBack() {
            window.history.back();
        }

        function goBack() {
            window.history.back();
        }

        function commander() {
            alert('Veuillez d\'abord payer votre commande avant de valider !');
        }