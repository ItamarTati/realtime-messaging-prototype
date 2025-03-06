import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { UserService } from './user.service';

@Injectable({
  providedIn: 'root',
})
export class ApiService {
  private baseUrl = 'http://localhost:8000/api';

  constructor(private http: HttpClient, private userService: UserService) {}

  sendMessage(content: string): Observable<any> {
    const userIdentifier = this.userService.getUserIdentifier();

    if (!userIdentifier) {
      throw new Error('User identifier is required.');
    }

    return this.http.post(`${this.baseUrl}/send-message`, { content, user_identifier: userIdentifier });
  }

  getMessages(): Observable<any> {
    const userIdentifier = this.userService.getUserIdentifier();

    if (!userIdentifier) {
      throw new Error('User identifier is required.');
    }

    return this.http.get(`${this.baseUrl}/messages`, { params: { user_identifier: userIdentifier } });
  }

  markMessageAsCompleted(id: number): Observable<any> {
    return this.http.post(`${this.baseUrl}/messages/${id}/complete`, {});
  }
}