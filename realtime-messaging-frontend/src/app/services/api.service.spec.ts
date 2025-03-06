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

    localStorage.setItem('user_identifier', 'existing-user-123');

    TestBed.configureTestingModule({});
    service = TestBed.inject(UserService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });

  it('should generate a user identifier if none exists', () => {
    // Clear localStorage for this test
    localStorage.removeItem('user_identifier');
    const userIdentifier = service.getUserIdentifier();
    expect(userIdentifier).toBeTruthy(); 
  });

  it('should return the existing user identifier', () => {
    const userIdentifier = service.getUserIdentifier();
    expect(userIdentifier).toBe('existing-user-123'); 
  });
});