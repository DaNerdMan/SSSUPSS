// Fingerprint generieren
function generateFingerprint() {
    Fingerprint2.get(function(components) {
        var values = components.map(function(component) { return component.value });
        var fingerprint = Fingerprint2.x64hash128(values.join(''), 31);
        document.getElementById('fingerprint').value = fingerprint;
    });
}

// Fingerprint beim Laden der Seite generieren
if (window.requestIdleCallback) {
    requestIdleCallback(generateFingerprint);
} else {
    setTimeout(generateFingerprint, 500);
}

function submitForm(action) {
    const form = document.getElementById('contentForm');
    const formData = new FormData(form);
    formData.append('action', action);

    // URL-Validierung f«är den URL-Shortener
    if (action === 'url') {
        const urlRegex = /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/;
        if (!urlRegex.test(formData.get('content'))) {
            alert(translations.invalid_url);
            return;
        }
    }

    fetch('index.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
        } else {
            document.getElementById('viewLink').value = data.viewLink;
            document.getElementById('deleteLink').value = data.deleteLink;
            document.getElementById('result').style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(translations.error_occurred);
    });
}

function generateRandomKey() {
    const array = new Uint8Array(16);
    window.crypto.getRandomValues(array);
    return Array.from(array, byte => byte.toString(16).padStart(2, '0')).join('');
}

function generateNewKey() {
    document.getElementById('encryption_key').value = generateRandomKey();
}

document.addEventListener('DOMContentLoaded', (event) => {
    generateNewKey();
});