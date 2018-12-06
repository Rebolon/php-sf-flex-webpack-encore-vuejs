import { BrowserModule } from '@angular/platform-browser';
import {RouterModule} from '@angular/router';
import { NgModule } from '@angular/core';
import { HTTP_INTERCEPTORS } from '@angular/common/http';

import { AppComponent } from './app.component';
import {appRoutes} from './app.routes';
import {HomeComponent} from './home/home.component';
import {BookComponent} from './book-container/book/book.component';
import {DatagridComponent} from './datagrid/datagrid.component';
import {SharedModule} from './shared/shared.module';
import {WizardModule} from './wizard-container/wizard.module';
import {ApiService} from '../services/api';
import { JwtInterceptorService } from '../services/jwt-interceptor';
import {environment} from '../environments/environment';

@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    BookComponent,
    DatagridComponent,
  ],
  imports: [
    BrowserModule,
    RouterModule.forRoot(
      appRoutes,
      // debugging purposes only
      { enableTracing: environment.production ? false : true }
    ),

    SharedModule,
    WizardModule,
  ],
  providers: [
    { provide: HTTP_INTERCEPTORS, useExisting: JwtInterceptorService, multi: true },
  ],
  bootstrap: [AppComponent],
})
export class AppModule { }
