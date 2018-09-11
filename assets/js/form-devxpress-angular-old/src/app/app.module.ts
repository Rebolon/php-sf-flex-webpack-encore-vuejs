import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppComponent } from './app.component';
import {DatagridComponent} from "./datagrid/datagrid.component";
import {ApiService} from "../services/api";
import {HttpClientModule} from "@angular/common/http";
import {RouterModule} from "@angular/router";
import {appRoutes} from "./app.routes";
import {HomeComponent} from "./home/home.component";
import {WizardModule} from "./wizard-container/wizard.module";
import {BookComponent} from "./book-container/book/book.component";
import {SharedModule} from "./shared/shared.module";

@NgModule({
  declarations: [
    AppComponent,
    DatagridComponent,
    HomeComponent,
    BookComponent,
  ],
  imports: [
    RouterModule.forRoot(
        appRoutes,
        { enableTracing: false }// debugging purposes only
    ),
    BrowserModule,
    HttpClientModule,
    // bad coz it require all the component from the module so the file will be bigger than really required
    SharedModule,
    WizardModule
  ],
  providers: [
      ApiService
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
