import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  templateUrl: './register.html',
  styleUrl: './register.css'
})
export class RegisterComponent implements OnInit {
  registerForm!: FormGroup;
  formSubmitted = false;
  captchaValue = '';

  constructor(private fb: FormBuilder) {}

  ngOnInit() {
    this.generateCaptcha();
    this.registerForm = this.fb.group({
      nombre: ['', [Validators.required, Validators.minLength(3), Validators.maxLength(100)]],
      email: ['', [Validators.required, Validators.email]],
      telefono: ['', [Validators.pattern(/[0-9]{8,12}/)]],
      password: ['', [Validators.required, Validators.minLength(8), Validators.pattern(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/)]],
      confirm_password: ['', [Validators.required]],
      donante: [false],
      tipo_donacion: ['mensual'],
      monto: [100],
      metodo_pago: ['tarjeta'],
      terminos: [false, [Validators.requiredTrue]],
      captcha: ['', [Validators.required]]
    }, { validators: this.passwordMatchValidator });
  }

  passwordMatchValidator(g: FormGroup) {
    return g.get('password')?.value === g.get('confirm_password')?.value
      ? null : { 'mismatch': true };
  }

  generateCaptcha() {
    const chars = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
    let result = '';
    for (let i = 0; i < 6; i++) {
      result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    this.captchaValue = result;
  }

  onSubmit() {
    if (this.registerForm.valid && this.registerForm.value.captcha === this.captchaValue) {
      console.log('Register submitted:', this.registerForm.value);
      this.formSubmitted = true;
      this.registerForm.reset({
        tipo_donacion: 'mensual',
        monto: 100,
        metodo_pago: 'tarjeta',
        donante: false
      });
      this.generateCaptcha();
    } else {
      if (this.registerForm.value.captcha !== this.captchaValue) {
        this.registerForm.get('captcha')?.setErrors({ incorrect: true });
      }
    }
  }
}
