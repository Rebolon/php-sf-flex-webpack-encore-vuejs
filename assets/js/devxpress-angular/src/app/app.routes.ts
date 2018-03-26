import {Routes} from "@angular/router";
import {DatagridComponent} from "./datagrid/datagrid.component";
import {HomeComponent} from "./home/home.component";
import {WizardContainerComponent} from "./wizard-container/app/wizard-container.component";
import {wizardRoutes} from "./wizard-container/wizard.routes";

export const appRoutes: Routes = [
    {path: 'home', component: HomeComponent},
    //{ path: 'login', component: LoginComponent },
    {path: 'datagrid', component: DatagridComponent},
    {path: 'wizard', component: WizardContainerComponent, children: wizardRoutes},
    {path: '**', redirectTo: '/home'}
]
