import { BrowserModule } from '@angular/platform-browser';
import {RouterModule} from '@angular/router';
import { NgModule } from '@angular/core';

import { AppComponent } from './app.component';
import {appRoutes} from './app.routes';
import {HomeComponent} from './home/home.component';
import {BookComponent} from './book-container/book/book.component';
import {SharedModule} from './shared/shared.module';
import {WizardModule} from "./wizard-container/wizard.module";
import {ApiService} from "../services/api";

@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    BookComponent,
  ],
  imports: [
    BrowserModule,
    RouterModule.forRoot(
      appRoutes,
      { enableTracing: false }// debugging purposes only
    ),
    SharedModule,
    WizardModule,
  ],
  providers: [
    ApiService,
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
