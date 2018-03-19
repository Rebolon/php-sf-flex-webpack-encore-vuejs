import { Routes} from "@angular/router";
import {DatagridComponent} from "./datagrid/datagrid.component";
import {HomeComponent} from "./home/home.component";

export const appRoutes: Routes = [
    { path: 'home', component: HomeComponent },
    //{ path: 'login', component: LoginComponent },
    { path: 'datagrid', component: DatagridComponent },
    { path: '**', redirectTo: '/home' }
]
