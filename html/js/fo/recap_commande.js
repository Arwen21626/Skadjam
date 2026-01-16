function verifierCGV() {
    const checkbox = document.getElementById('case');
    const message = document.getElementById('error-cgv');

    if (!checkbox.checked) {
        message.classList.remove('hidden');
        checkbox.classList.add('border-red-500');
        return false; // bloque l'envoi
    }

    message.classList.add('hidden');
    checkbox.classList.remove('border-red-500');
    return true;
}
