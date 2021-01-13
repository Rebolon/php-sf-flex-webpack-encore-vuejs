import {NgModule} from '@angular/core';
import {FormsModule, ReactiveFormsModule} from "@angular/forms";
import {WizardSumupComponent} from './wizard-sumup/wizard-sumup.component';
import {DevExtremeModule} from 'devextreme-angular';
import {WizardListAuthorsComponent} from './wizard-list-authors/wizard-list-authors.component';
import {WizardListEditorsComponent} from './wizard-list-editors/wizard-list-editors.component';
import {LoginComponent} from "./login/login.component";
import {CommonModule} from '@angular/common';
import {TokenService} from "../../services/token.service";
import {environment} from "../../environments/environment";

@NgModule({
    imports: [
        CommonModule,
        DevExtremeModule,
        FormsModule,
        ReactiveFormsModule,
    ],
    declarations: [
        LoginComponent,
        WizardListAuthorsComponent,
        WizardListEditorsComponent,
        WizardSumupComponent,
    ],
    exports: [
        CommonModule,
        DevExtremeModule,
        LoginComponent,
        WizardListAuthorsComponent,
        WizardListEditorsComponent,
        WizardSumupComponent,
    ],
    providers: [
      {
        provide: 'TokenServiceJwt',
        useFactory: () => {
          return new TokenService(environment.tokenKey)
        }
      }
    ]
})
export class SharedModule {
}
