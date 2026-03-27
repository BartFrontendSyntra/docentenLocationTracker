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
      // 1. Await the login method from our service
      const response = await this.authService.login(this.loginForm.getRawValue());

      // 2. If successful, route based on role_id
      if (response.user.role_id === 1) {
        this.router.navigate(['/admin-dashboard']);
      } else {
        this.router.navigate(['/viewer-dashboard']);
      }

    } catch (err: any) {
      // 3. If an error is thrown, handle it here
      if (err.status === 422) {
        this.errorMessage.set('Invalid credentials. Please try again.');
      } else {
        this.errorMessage.set('An error occurred. Is your backend running?');
      }

    } finally {
      // 4. This block runs regardless of success or failure
      this.isLoading.set(false);
    }
  }

}
