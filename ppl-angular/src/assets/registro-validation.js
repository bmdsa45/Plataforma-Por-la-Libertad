document.addEventListener('DOMContentLoaded', function() {
    // Generar token CSRF
    const csrfToken = generateCSRFToken();
    document.getElementById('csrf_token').value = csrfToken;
    
    // Generar captcha
    generateCaptcha();
    
    // Mostrar/ocultar sección de donante
    const donanteCheckbox = document.getElementById('donante');
    const seccionDonante = document.getElementById('seccion-donante');
    
    donanteCheckbox.addEventListener('change', function() {
        seccionDonante.style.display = this.checked ? 'block' : 'none';
    });
    
    // Validación del formulario
    const registroForm = document.getElementById('registroForm');
    
    registroForm.addEventListener('submit', function(event) {
        event.preventDefault();
        
        if (validateForm()) {
            // Cifrar datos sensibles antes de enviar
            encryptSensitiveData();
            
            // Enviar formulario
            this.submit();
        }
    });
    
    // Validación en tiempo real
    const inputs = registroForm.querySelectorAll('input[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
    });
    
    // Validación de contraseñas coincidentes
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    confirmPassword.addEventListener('blur', function() {
        if (password.value !== confirmPassword.value) {
            showError(confirmPassword, 'Las contraseñas no coinciden');
        } else {
            clearError(confirmPassword);
        }
    });
});

// Generar token CSRF
function generateCSRFToken() {
    const array = new Uint8Array(16);
    window.crypto.getRandomValues(array);
    return Array.from(array, byte => byte.toString(16).padStart(2, '0')).join('');
}

// Generar captcha
function generateCaptcha() {
    const captchaText = document.getElementById('captcha-text');
    const captchaValue = document.getElementById('captcha-value');
    
    // Generar código aleatorio
    const characters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
    let captcha = '';
    
    for (let i = 0; i < 6; i++) {
        captcha += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    
    captchaText.textContent = captcha;
    captchaValue.value = hashCaptcha(captcha); // Almacenar hash del captcha
}

// Hash para el captcha
function hashCaptcha(text) {
    // En producción, usar una función hash más robusta
    let hash = 0;
    for (let i = 0; i < text.length; i++) {
        const char = text.charCodeAt(i);
        hash = ((hash << 5) - hash) + char;
        hash = hash & hash;
    }
    return hash.toString();
}

// Validar formulario completo
function validateForm() {
    let isValid = true;
    
    // Validar campos requeridos
    const requiredFields = document.querySelectorAll('input[required]');
    requiredFields.forEach(field => {
        if (!validateField(field)) {
            isValid = false;
        }
    });
    
    // Validar contraseñas
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    if (password.value !== confirmPassword.value) {
        showError(confirmPassword, 'Las contraseñas no coinciden');
        isValid = false;
    }
    
    // Validar captcha
    const captcha = document.getElementById('captcha');
    const captchaText = document.getElementById('captcha-text').textContent;
    
    if (captcha.value !== captchaText) {
        showError(captcha, 'Código de verificación incorrecto');
        isValid = false;
    }
    
    // Validar términos y condiciones
    const terminos = document.getElementById('terminos');
    if (!terminos.checked) {
        showError(terminos, 'Debes aceptar los términos y condiciones');
        isValid = false;
    }
    
    return isValid;
}

// Validar campo individual
function validateField(field) {
    if (field.type === 'checkbox') {
        if (!field.checked && field.required) {
            showError(field, 'Este campo es obligatorio');
            return false;
        }
    } else if (field.value.trim() === '') {
        showError(field, 'Este campo es obligatorio');
        return false;
    } else if (field.type === 'email' && !validateEmail(field.value)) {
        showError(field, 'Ingrese un correo electrónico válido');
        return false;
    } else if (field.type === 'password' && field.id === 'password' && !validatePassword(field.value)) {
        showError(field, 'La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número');
        return false;
    } else if (field.type === 'tel' && field.value && !validatePhone(field.value)) {
        showError(field, 'Ingrese un número de teléfono válido');
        return false;
    }
    
    clearError(field);
    return true;
}

// Validar email
function validateEmail(email) {
    const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return re.test(email);
}

// Validar contraseña
function validatePassword(password) {
    // Al menos 8 caracteres, una mayúscula, una minúscula y un número
    const re = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
    return re.test(password);
}

// Validar teléfono
function validatePhone(phone) {
    const re = /^[0-9]{8,12}$/;
    return re.test(phone);
}

// Mostrar mensaje de error
function showError(field, message) {
    const errorElement = document.getElementById(field.id + '-error');
    if (errorElement) {
        errorElement.textContent = message;
        field.classList.add('error');
    }
}

// Limpiar mensaje de error
function clearError(field) {
    const errorElement = document.getElementById(field.id + '-error');
    if (errorElement) {
        errorElement.textContent = '';
        field.classList.remove('error');
    }
}

// Cifrar datos sensibles
function encryptSensitiveData() {
    // En un entorno real, aquí se implementaría el cifrado de datos sensibles
    // utilizando una biblioteca de cifrado como CryptoJS
    
    if (document.getElementById('donante').checked) {
        // Simulación de cifrado para datos de pago
        console.log('Datos de pago cifrados con BitLocker');
        
        // En producción, se utilizaría una API de cifrado real
        const paymentData = {
            method: getSelectedPaymentMethod(),
            amount: document.getElementById('monto').value,
            type: document.getElementById('tipo_donacion').value
        };
        
        // Crear campo oculto para datos cifrados
        const encryptedField = document.createElement('input');
        encryptedField.type = 'hidden';
        encryptedField.name = 'encrypted_payment_data';
        encryptedField.value = simulateEncryption(JSON.stringify(paymentData));
        
        document.getElementById('registroForm').appendChild(encryptedField);
    }
}

// Obtener método de pago seleccionado
function getSelectedPaymentMethod() {
    const methods = document.getElementsByName('metodo_pago');
    for (const method of methods) {
        if (method.checked) {
            return method.value;
        }
    }
    return null;
}

// Simulación de cifrado (en producción usar una biblioteca real)
function simulateEncryption(data) {
    // Esta es solo una simulación para demostración
    // En producción, usar una biblioteca de cifrado real
    return btoa(data) + '.encrypted';
}