function showToast(message, isError = false) {
    // Tenta pegar o elemento existente
    let toast = document.getElementById('feedback-toast');

    // Se não existir, cria dinamicamente
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'feedback-toast';
        document.body.appendChild(toast);
    }

    toast.textContent = message;
    toast.style.display = 'block';
    toast.style.background = isError ? '#e74c3c' : '#27ae60';
    toast.style.color = '#fff';
    toast.style.padding = '12px 24px';
    toast.style.position = 'fixed';
    toast.style.bottom = '32px';
    toast.style.right = '32px';
    toast.style.borderRadius = '8px';
    toast.style.zIndex = '9999';
    
    setTimeout(() => { toast.style.display = 'none'; }, 3000);
}
