import { TestBed } from '@angular/core/testing';
import { UserService } from './user.service';

describe('UserService', () => {
  let service: UserService;

  beforeEach(() => {
    let store: { [key: string]: string } = {};
    spyOn(localStorage, 'getItem').and.callFake((key: string) => store[key] || null);
    spyOn(localStorage, 'setItem').and.callFake((key: string, value: string) => {
      store[key] = value;
    });

    TestBed.configureTestingModule({});
  });

  it('should be created', () => {
    service = TestBed.inject(UserService);
    expect(service).toBeTruthy();
  });

  it('should generate a user identifier if none exists', () => {
    localStorage.removeItem('user_identifier');

    service = TestBed.inject(UserService);

    const userIdentifier = service.getUserIdentifier();
    expect(userIdentifier).toBeTruthy(); 
  });

  it('should return the existing user identifier', () => {
    localStorage.setItem('user_identifier', 'existing-user-123');

    service = TestBed.inject(UserService);

    const userIdentifier = service.getUserIdentifier();
    expect(userIdentifier).toBe('existing-user-123'); 
  });
});