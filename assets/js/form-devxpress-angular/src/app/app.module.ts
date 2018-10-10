import { BrowserModule } from '@angular/platform-browser';
import {RouterModule} from '@angular/router';
import { NgModule } from '@angular/core';
import { HTTP_INTERCEPTORS, HttpClientModule } from '@angular/common/http';

import { AppComponent } from './app.component';
import {appRoutes} from './app.routes';
import {HomeComponent} from './home/home.component';
import {BookComponent} from './book-container/book/book.component';
import {DatagridComponent} from "./datagrid/datagrid.component";
import {SharedModule} from './shared/shared.module';
import {WizardModule} from './wizard-container/wizard.module';
import {ApiService} from '../services/api';
import { JwtInterceptorService } from '../services/jwt-interceptor';
import {environment} from '../environments/environment'
import {DatagridComponent} from './datagrid/datagrid.component';

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
      { enableTracing: environment.production ? false: true }// debugging purposes only
    ),

    SharedModule,
    WizardModule,
  ],
  providers: [
    { provide: HTTP_INTERCEPTORS, useExisting: JwtInterceptorService, multi: true },
    ApiService,
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
