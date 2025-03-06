import { ApplicationConfig, provideZoneChangeDetection } from '@angular/core';
import { provideRouter } from '@angular/router';
import { provideAnimations } from '@angular/platform-browser/animations';
import { provideToastr } from 'ngx-toastr';
import { routes } from './app.routes';
import { provideHttpClient } from '@angular/common/http';

export const appConfig: ApplicationConfig = {
  providers: [
    provideZoneChangeDetection({ eventCoalescing: true }), 
    provideRouter(routes), 
    provideHttpClient(), 
    provideAnimations(), // Required for toastr
    provideToastr({
      positionClass: 'toast-top-right', // Position the toasts at the top-right
      timeOut: 3000, // Toast disappears after 3 seconds
      preventDuplicates: true, // Prevent duplicate toasts
      progressBar: true, // Show a progress bar
      closeButton: true, // Show a close button
    }),
  ]
};
