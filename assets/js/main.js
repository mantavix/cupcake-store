// Variáveis globais
let cartCount = 0;
let cartItems = [];

// Inicialização quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    // Só carregar produtos se estivermos numa página que tem a grade de produtos
    const productsGrid = document.getElementById('products-grid');
    if (productsGrid) {
        loadProducts();
    }
    
    updateCartCount();
    initializeEventListeners();
});

// Carregar produtos
async function loadProducts() {
    try {
        const response = await fetch(getApiPath('get_products.php'));
        const products = await response.json();
        
        if (products.success) {
            displayProducts(products.data);
        } else {
            showAlert('Erro ao carregar produtos: ' + products.message, 'error');
        }
    } catch (error) {
        console.error('Erro ao carregar produtos:', error);
        showAlert('Erro ao carregar produtos', 'error');
    }
}

// Exibir produtos na tela
function displayProducts(products) {
    const productsGrid = document.getElementById('products-grid');
    if (!productsGrid) return;
    
    productsGrid.innerHTML = '';
    
    products.forEach(product => {
        const productCard = createProductCard(product);
        productsGrid.appendChild(productCard);
    });
}

// Criar card do produto
function createProductCard(product) {
    const card = document.createElement('div');
    card.className = 'product-card';
    card.innerHTML = `
        <div class="product-image">
            <img src="assets/img/cupcake-/${product.image}" alt="${product.name}" 
                 style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;" 
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div style="display: none; width: 100%; height: 100%; align-items: center; justify-content: center; font-size: 4rem; color: var(--primary-green);">
                <i class="fas fa-birthday-cake"></i>
            </div>
        </div>
        <div class="product-info">
            <h3 class="product-name">${product.name}</h3>
            <p class="product-description">${product.description}</p>
            <div class="product-price">R$ ${parseFloat(product.price).toFixed(2)}</div>
            <div class="product-actions">
                <div class="quantity-selector">
                    <button class="quantity-btn" onclick="decreaseQuantity(${product.id})">-</button>
                    <input type="number" class="quantity-input" id="qty-${product.id}" value="1" min="1" max="${product.stock_quantity}">
                    <button class="quantity-btn" onclick="increaseQuantity(${product.id})">+</button>
                </div>
                <button class="add-to-cart" onclick="addToCart(${product.id})">
                    <i class="fas fa-cart-plus"></i> Adicionar
                </button>
            </div>
            <div class="stock-info">
                <small class="text-muted">Estoque: ${product.stock_quantity} unidades</small>
            </div>
        </div>
    `;
    return card;
}

// Aumentar quantidade
function increaseQuantity(productId) {
    const input = document.getElementById(`qty-${productId}`);
    const max = parseInt(input.getAttribute('max'));
    const current = parseInt(input.value);
    
    if (current < max) {
        input.value = current + 1;
    }
}

// Diminuir quantidade
function decreaseQuantity(productId) {
    const input = document.getElementById(`qty-${productId}`);
    const current = parseInt(input.value);
    
    if (current > 1) {
        input.value = current - 1;
    }
}

// Adicionar ao carrinho
async function addToCart(productId) {
    // Verificar se o usuário está logado ANTES de tentar adicionar
    if (!window.isUserLoggedIn) {
        showLoginModal();
        return;
    }
    
    const quantityInput = document.getElementById(`qty-${productId}`);
    const quantity = parseInt(quantityInput.value);
    
    try {
        const response = await fetch(getApiPath('add_to_cart.php'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Produto adicionado ao carrinho!', 'success');
            updateCartCount();
            quantityInput.value = 1; // Reset quantity
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        console.error('Erro ao adicionar ao carrinho:', error);
        showAlert('Erro ao adicionar produto ao carrinho', 'error');
    }
}

// Atualizar contador do carrinho
async function updateCartCount() {
    try {
        const response = await fetch(getApiPath('get_cart_count.php'));
        const result = await response.json();
        
        if (result.success) {
            cartCount = result.count;
            const cartCountElement = document.getElementById('cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = cartCount;
            }
        }
    } catch (error) {
        console.error('Erro ao atualizar contador do carrinho:', error);
    }
}

// Remover do carrinho
// Função para obter o caminho correto da API
function getApiPath(apiFile) {
    // Verificar se estamos em uma subpasta (pages/, admin/, etc.)
    const path = window.location.pathname;
    if (path.includes('/pages/') || path.includes('/admin/') || path.includes('/install/')) {
        return '../api/' + apiFile;
    }
    return 'api/' + apiFile;
}

async function removeFromCart(productId) {
    if (!confirm('Tem certeza que deseja remover este item do carrinho?')) {
        return;
    }
    
    try {
        const response = await fetch(getApiPath('remove_from_cart.php'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Item removido do carrinho!', 'success');
            location.reload(); // Recarregar a página do carrinho
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        console.error('Erro ao remover do carrinho:', error);
        showAlert('Erro ao remover item do carrinho', 'error');
    }
}

// Esvaziar carrinho
async function clearCart() {
    if (!confirm('Tem certeza que deseja esvaziar o carrinho?')) {
        return;
    }
    
    try {
        const response = await fetch(getApiPath('clear_cart.php'), {
            method: 'POST'
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Carrinho esvaziado!', 'success');
            location.reload();
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        console.error('Erro ao esvaziar carrinho:', error);
        showAlert('Erro ao esvaziar carrinho', 'error');
    }
}

// Atualizar quantidade no carrinho
async function updateCartQuantity(productId, quantity) {
    if (quantity < 1) return;
    
    try {
        const response = await fetch(getApiPath('update_cart.php'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            location.reload(); // Recarregar para atualizar totais
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        console.error('Erro ao atualizar carrinho:', error);
        showAlert('Erro ao atualizar quantidade', 'error');
    }
}

// Validar formulários
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const inputs = form.querySelectorAll('input[required], select[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = '#d9534f';
            isValid = false;
        } else {
            input.style.borderColor = '#ddd';
        }
    });
    
    return isValid;
}

// Validar CPF
function validateCPF(cpf) {
    cpf = cpf.replace(/[^\d]+/g, '');
    
    if (cpf.length !== 11 || 
        cpf === "00000000000" ||
        cpf === "11111111111" ||
        cpf === "22222222222" ||
        cpf === "33333333333" ||
        cpf === "44444444444" ||
        cpf === "55555555555" ||
        cpf === "66666666666" ||
        cpf === "77777777777" ||
        cpf === "88888888888" ||
        cpf === "99999999999") {
        return false;
    }
    
    // Validar primeiro dígito verificador
    let add = 0;
    for (let i = 0; i < 9; i++) {
        add += parseInt(cpf.charAt(i)) * (10 - i);
    }
    let rev = 11 - (add % 11);
    if (rev === 10 || rev === 11) rev = 0;
    if (rev !== parseInt(cpf.charAt(9))) return false;
    
    // Validar segundo dígito verificador
    add = 0;
    for (let i = 0; i < 10; i++) {
        add += parseInt(cpf.charAt(i)) * (11 - i);
    }
    rev = 11 - (add % 11);
    if (rev === 10 || rev === 11) rev = 0;
    if (rev !== parseInt(cpf.charAt(10))) return false;
    
    return true;
}

// Validar email
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Formatar CPF
function formatCPF(cpf) {
    cpf = cpf.replace(/\D/g, '');
    return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
}

// Formatar telefone
function formatPhone(phone) {
    phone = phone.replace(/\D/g, '');
    return phone.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
}

// Mostrar alertas
function showAlert(message, type = 'info') {
    // Remover alertas existentes
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `
        <span>${message}</span>
        <button type="button" class="close" onclick="this.parentElement.remove()">×</button>
    `;
    
    // Adicionar no topo da página
    document.body.insertBefore(alert, document.body.firstChild);
    
    // Remover automaticamente após 5 segundos
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

// Inicializar event listeners
function initializeEventListeners() {
    // Formatação automática de campos
    const cpfInputs = document.querySelectorAll('input[name="cpf"]');
    cpfInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.value = formatCPF(this.value);
        });
    });
    
    const phoneInputs = document.querySelectorAll('input[name="phone"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.value = formatPhone(this.value);
        });
    });
    
    // Smooth scroll para links âncora
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Função para fazer logout
async function logout() {
    try {
        const response = await fetch('actions/logout.php');
        window.location.href = 'index.php';
    } catch (error) {
        console.error('Erro ao fazer logout:', error);
    }
}

// Função para pesquisar produtos
function searchProducts(query) {
    const products = document.querySelectorAll('.product-card');
    
    products.forEach(product => {
        const name = product.querySelector('.product-name').textContent.toLowerCase();
        const description = product.querySelector('.product-description').textContent.toLowerCase();
        
        if (name.includes(query.toLowerCase()) || description.includes(query.toLowerCase())) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
}

// Função para filtrar produtos por preço
function filterByPrice(minPrice, maxPrice) {
    const products = document.querySelectorAll('.product-card');
    
    products.forEach(product => {
        const priceText = product.querySelector('.product-price').textContent;
        const price = parseFloat(priceText.replace('R$ ', '').replace(',', '.'));
        
        if (price >= minPrice && price <= maxPrice) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
}

// Funções utilitárias
const Utils = {
    // Formatar moeda
    formatCurrency: function(value) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(value);
    },
    
    // Formatar data
    formatDate: function(date) {
        return new Intl.DateTimeFormat('pt-BR').format(new Date(date));
    },
    
    // Debounce para otimizar pesquisas
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
};

// Loading state para botões
function setButtonLoading(button, loading = true) {
    if (loading) {
        button.disabled = true;
        button.innerHTML = '<span class="loading"></span> Carregando...';
    } else {
        button.disabled = false;
        button.innerHTML = button.getAttribute('data-original-text') || 'Enviar';
    }
}

// Funções do Modal de Login
function showLoginModal() {
    const modal = document.getElementById('loginModal');
    if (modal) {
        modal.classList.add('show');
        // Prevenir scroll da página de fundo
        document.body.style.overflow = 'hidden';
    }
}

function closeLoginModal() {
    const modal = document.getElementById('loginModal');
    if (modal) {
        modal.classList.remove('show');
        // Restaurar scroll da página
        document.body.style.overflow = 'auto';
    }
}

// Fechar modal clicando fora do conteúdo
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('loginModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeLoginModal();
            }
        });
    }
});

// Fechar modal com tecla ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLoginModal();
    }
});
