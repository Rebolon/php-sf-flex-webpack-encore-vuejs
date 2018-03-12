import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { DevExtremeModule  } from 'devextreme-angular';

import { AppComponent } from './app.component';
import {DatagridComponent} from "./datagrid/datagrid.component";
import {ApiService} from "../services/api";
import {HttpClient, HttpHandler, HttpClientModule} from "@angular/common/http";
import {HttpModule} from "@angular/http";

@NgModule({
  declarations: [
    AppComponent,
    DatagridComponent
  ],
  imports: [
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
