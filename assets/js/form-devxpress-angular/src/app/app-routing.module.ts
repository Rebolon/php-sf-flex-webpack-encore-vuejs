import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import {HomeComponent} from "./home/home.component";
import {LoginComponent} from "./shared/login/login.component";
import {DatagridComponent} from "./datagrid/datagrid.component";
import {BookComponent} from "./book-container/book/book.component";
import {WizardContainerComponent} from "./wizard-container/app/wizard-container.component";
import {wizardRoutes} from "./wizard-container/wizard.routes";

const routes: Routes = [
  {path: 'home', component: HomeComponent},
  {path: 'login', component: LoginComponent},
  {path: 'datagrid', component: DatagridComponent},
  {path: 'book/:id', component: BookComponent},
  {path: 'wizard', component: WizardContainerComponent, children: wizardRoutes},
  {path: '**', redirectTo: '/home'}
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
