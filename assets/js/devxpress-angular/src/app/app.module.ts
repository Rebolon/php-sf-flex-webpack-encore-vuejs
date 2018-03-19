import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { DevExtremeModule  } from 'devextreme-angular';

import { AppComponent } from './app.component';
import {DatagridComponent} from "./datagrid/datagrid.component";
import {ApiService} from "../services/api";
import {HttpClientModule} from "@angular/common/http";
import {RouterModule} from "@angular/router";
import {appRoutes} from "./app.routes";
import {HomeComponent} from "./home/home.component";

@NgModule({
  declarations: [
    AppComponent,
    DatagridComponent,
    HomeComponent,
  ],
  imports: [
    RouterModule.forRoot(
        appRoutes,
        { enableTracing: true }// debugging purposes only
    ),
    BrowserModule,
    HttpClientModule,
    // bad coz it require all the component from the module so the file will be bigger than really required
    DevExtremeModule
  ],
  providers: [
      ApiService
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
