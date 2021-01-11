import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import {HomeComponent} from "./home/home.component";
import {BookComponent} from "./book-container/book/book.component";
import {DatagridComponent} from "./datagrid/datagrid.component";
import {SharedModule} from "./shared/shared.module";
import {WizardModule} from "./wizard-container/wizard.module";
import {JwtInterceptorService} from "../services/jwt-interceptor";
import {HTTP_INTERCEPTORS} from "@angular/common/http";

@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    BookComponent,
    DatagridComponent,
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    SharedModule,
    WizardModule,
  ],
  providers: [
    { provide: HTTP_INTERCEPTORS, useExisting: JwtInterceptorService, multi: true },
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
