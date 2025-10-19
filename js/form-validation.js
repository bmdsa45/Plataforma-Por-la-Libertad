document.addEventListener('DOMContentLoaded', function() {
    // Generar token CSRF
    const csrfToken = generateRandomToken(32);
    document.getElementById('csrf_token').value = csrfToken;
    
    // Almacenar token en sessionStorage
    sessionStorage.setItem('csrf_token', csrfToken);
    
    // Generar captcha
    generateCaptcha();
    
    // Validación del formulario
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            if (validateForm()) {
                submitForm();
            }
        });
    }
    
    // Validación en tiempo real
    const inputs = document.querySelectorAll('#contactForm input, #contactForm textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
    });
});

// Generar token aleatorio para CSRF
function generateRandomToken(length) {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let token = '';
    for (let i = 0; i < length; i++) {
        token += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return token;
}

// Generar captcha simple
function generateCaptcha() {
    const num1 = Math.floor(Math.random() * 10) + 1;
    const num2 = Math.floor(Math.random() * 10) + 1;
    const captchaQuestion = document.getElementById('captcha-question');
    
    if (captchaQuestion) {
        captchaQuestion.textContent = `${num1} + ${num2}`;
        // Almacenar respuesta en sessionStorage
        sessionStorage.setItem('captcha_answer', (num1 + num2).toString());
    }
}

// Validar campo individual
function validateField(field) {
    const fieldId = field.id;
    const errorElement = document.getElementById(`${fieldId}-error`);
    let isValid = field.checkValidity();
    
    // Validaciones adicionales
    if (fieldId === 'name' && field.value.trim().length < 2) {
        isValid = false;
        errorElement.textContent = 'El nombre debe tener al menos 2 caracteres';
    } else if (fieldId === 'email' && field.value.trim() !== '') {
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailPattern.test(field.value)) {
            isValid = false;
            errorElement.textContent = 'Por favor ingresa un correo electrónico válido';
        }
    } else if (fieldId === 'captcha') {
        const captchaAnswer = sessionStorage.getItem('captcha_answer');
        if (field.value !== captchaAnswer) {
            isValid = false;
            errorElement.textContent = 'La respuesta es incorrecta';
        }
    }
    
    // Mostrar u ocultar mensaje de error
    if (!isValid) {
        field.classList.add('invalid');
        errorElement.style.display = 'block';
    } else {
        field.classList.remove('invalid');
        errorElement.textContent = '';
        errorElement.style.display = 'none';
    }
    
    return isValid;
}

// Validar todo el formulario
function validateForm() {
    const fields = ['name', 'email', 'subject', 'message', 'captcha'];
    let isValid = true;
    
    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            const fieldValid = validateField(field);
            isValid = isValid && fieldValid;
        }
    });
    
    return isValid;
}

// Enviar formulario mediante AJAX
function submitForm() {
    const form = document.getElementById('contactForm');
    const formData = new FormData(form);
    
    // Añadir token CSRF y respuesta de captcha
    formData.append('csrf_token', sessionStorage.getItem('csrf_token'));
    formData.append('captcha_answer', sessionStorage.getItem('captcha_answer'));
    
    // Deshabilitar botón de envío
    const submitBtn = document.getElementById('submit-btn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Enviando...';
    
    fetch('process_form.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar mensaje de éxito
            document.getElementById('form-success').style.display = 'block';
            form.reset();
            // Generar nuevo captcha
            generateCaptcha();
        } else {
            // Mostrar mensaje de error
            document.getElementById('form-error').textContent = data.message || 'Ha ocurrido un error. Por favor, intenta nuevamente.';
            document.getElementById('form-error').style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('form-error').textContent = 'Ha ocurrido un error de conexión. Por favor, intenta nuevamente.';
        document.getElementById('form-error').style.display = 'block';
    })
    .finally(() => {
        // Habilitar botón de envío
        submitBtn.disabled = false;
        submitBtn.textContent = 'Enviar Mensaje';
    });
}