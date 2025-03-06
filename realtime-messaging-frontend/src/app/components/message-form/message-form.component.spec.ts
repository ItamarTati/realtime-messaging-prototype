import { ComponentFixture, TestBed } from '@angular/core/testing';
import { MessageFormComponent } from './message-form.component';
import { ApiService } from '../../services/api.service';
import { UserService } from '../../services/user.service';
import { ToastrService } from 'ngx-toastr';
import { of, throwError } from 'rxjs';

describe('MessageFormComponent', () => {
  let component: MessageFormComponent;
  let fixture: ComponentFixture<MessageFormComponent>;
  let apiServiceMock: any;
  let userServiceMock: any;
  let toastrServiceMock: any;

  beforeEach(async () => {
    apiServiceMock = {
      sendMessage: jasmine.createSpy('sendMessage').and.returnValue(of({})),
    };
    userServiceMock = {
      getUserIdentifier: jasmine.createSpy('getUserIdentifier').and.returnValue('user-123'),
    };
    toastrServiceMock = {
      success: jasmine.createSpy('success'),
      error: jasmine.createSpy('error'),
    };

    await TestBed.configureTestingModule({
      imports: [MessageFormComponent],
      providers: [
        { provide: ApiService, useValue: apiServiceMock },
        { provide: UserService, useValue: userServiceMock },
        { provide: ToastrService, useValue: toastrServiceMock },
      ],
    }).compileComponents();

    fixture = TestBed.createComponent(MessageFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });

  it('should send a message successfully', () => {
    component.content = 'Hello, world!';
    component.sendMessage();

    expect(apiServiceMock.sendMessage).toHaveBeenCalledWith('Hello, world!');
    expect(toastrServiceMock.success).toHaveBeenCalledWith('Message sent successfully!');
    expect(component.content).toBe('');
    expect(component.error).toBe('');
  });

  it('should handle send message error', () => {
    apiServiceMock.sendMessage.and.returnValue(throwError({ error: { message: 'Failed to send message.' } }));
    component.content = 'Hello, world!';
    component.sendMessage();

    expect(apiServiceMock.sendMessage).toHaveBeenCalledWith('Hello, world!');
    expect(toastrServiceMock.error).toHaveBeenCalledWith('Failed to send message.');
    expect(component.error).toBe('Failed to send message.');
  });
});