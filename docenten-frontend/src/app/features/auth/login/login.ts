import { Component, inject, signal  } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '@core/auth/auth-service';

@Component({
  selector: 'app-login',
  imports: [ReactiveFormsModule],
  templateUrl: './login.html',
  styleUrl: './login.css',
})
export class Login {

  private formBuilder = inject(FormBuilder);
  private authService = inject(AuthService);
  private router = inject(Router);

  isLoading = signal(false);
  errorMessage = signal<string | null>(null);

  loginForm = this.formBuilder.nonNullable.group({
    login: ['', Validators.required],
    password: ['', Validators.required]
  });

  async onSubmit() {
    if (this.loginForm.invalid) return;

    this.isLoading.set(true);
    this.errorMessage.set(null);

    try {

      const response = await this.authService.login(this.loginForm.getRawValue());

      if (response.user.role === 'Administrator') {
        this.router.navigate(['/admin-dashboard']);
      } else {
        this.router.navigate(['/viewer-dashboard']);
      }

    } catch (err: any) {

      if (err.status === 422) {
        this.errorMessage.set('Invalid credentials. Please try again.');
      } else {
        this.errorMessage.set('An error occurred. Is your backend running?');
      }

    } finally {
      this.isLoading.set(false);
    }
  }

}
