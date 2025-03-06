import { Routes } from '@angular/router';
import { MessageFormComponent } from './components/message-form/message-form.component';
import { MessageListComponent } from './components/message-list/message-list.component';
import { AdminMessageListComponent } from './components/admin-message-list/admin-message-list.component';

export const routes: Routes = [
  { 
    path: '', 
    children: [
      { path: '', component: MessageFormComponent, outlet: 'form' },
      { path: '', component: MessageListComponent, outlet: 'list' },
    ],
  },
  { path: 'admin/messages', component: AdminMessageListComponent },
];