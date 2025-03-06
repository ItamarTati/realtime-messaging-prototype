import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root',
})
export class UserService {
  private userIdentifier: string | null = null;

  constructor() {
    // Check if user identifier exists in localStorage
    this.userIdentifier = localStorage.getItem('user_identifier');

    // If not, generate a new one and store it
    if (!this.userIdentifier) {
      this.userIdentifier = crypto.randomUUID();
      localStorage.setItem('user_identifier', this.userIdentifier);
    }
  }

  getUserIdentifier(): string | null {
    return this.userIdentifier;
  }
}