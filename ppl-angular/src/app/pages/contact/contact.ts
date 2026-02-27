import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-contact',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './contact.html',
  styleUrl: './contact.css'
})
export class ContactComponent implements OnInit {
  contactForm!: FormGroup;
  captchaQuestion = '';
  private captchaAnswer = 0;
  formSubmitted = false;
  formError = false;

  constructor(private fb: FormBuilder) {}

  ngOnInit() {
    this.generateCaptcha();
    this.contactForm = this.fb.group({
      name: ['', [Validators.required, Validators.minLength(2), Validators.maxLength(100), Validators.pattern(/[A-Za-záéíóúüñÁÉÍÓÚÜÑ\s]+/)]],
      email: ['', [Validators.required, Validators.email]],
      subject: ['', [Validators.required, Validators.minLength(3), Validators.maxLength(100)]],
      message: ['', [Validators.required, Validators.minLength(10), Validators.maxLength(1000)]],
      captcha: ['', [Validators.required]]
    });
  }

  generateCaptcha() {
    const num1 = Math.floor(Math.random() * 10);
    const num2 = Math.floor(Math.random() * 10);
    this.captchaQuestion = `${num1} + ${num2}`;
    this.captchaAnswer = num1 + num2;
  }

  onSubmit() {
    if (this.contactForm.valid && parseInt(this.contactForm.value.captcha) === this.captchaAnswer) {
      console.log('Form submitted:', this.contactForm.value);
      this.formSubmitted = true;
      this.formError = false;
      this.contactForm.reset();
      this.generateCaptcha();
    } else {
      this.formError = true;
      this.formSubmitted = false;
      if (parseInt(this.contactForm.value.captcha) !== this.captchaAnswer) {
        this.contactForm.get('captcha')?.setErrors({ incorrect: true });
      }
    }
  }
}
