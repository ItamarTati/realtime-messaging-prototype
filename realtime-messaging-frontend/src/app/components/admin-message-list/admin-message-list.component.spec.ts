import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AdminMessageListComponent } from './admin-message-list.component';

describe('AdminMessageListComponent', () => {
  let component: AdminMessageListComponent;
  let fixture: ComponentFixture<AdminMessageListComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AdminMessageListComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(AdminMessageListComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
