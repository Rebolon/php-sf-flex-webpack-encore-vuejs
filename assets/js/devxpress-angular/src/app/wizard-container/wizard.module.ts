import {NgModule} from '@angular/core';
import {RouterModule} from "@angular/router";
import {WizardContainerComponent} from "./app/wizard-container.component";
import {WizardStepComponent} from "./app/wizard-step/wizard-step.component";
import {BookFormComponent} from "./app/book-form/book-form.component";
import {EditorsFormComponent} from "./app/editors-form/editors-form.component";
import {AuthorsFormComponent} from "./app/authors-form/authors-form.component";
import {BrowserModule} from "@angular/platform-browser";
import {HttpClientModule} from "@angular/common/http";
import {WizardRouting} from "./services/wizard-routing";
import {DevExtremeModule} from "devextreme-angular";
import {WizardSumupComponent} from "./app/wizard-sumup/wizard-sumup.component";
import {WizardBook} from "./services/wizard-book";
import {WizardListEditorsComponent} from "./app/wizard-list-editors/wizard-list-editors.component";
import {WizardListAuthorsComponent} from "./app/wizard-list-authors/wizard-list-authors.component";

@NgModule({
    declarations: [
        WizardContainerComponent,
        WizardStepComponent,
        WizardSumupComponent,
        WizardListEditorsComponent,
        WizardListAuthorsComponent,
        BookFormComponent,
        EditorsFormComponent,
        AuthorsFormComponent
    ],
    imports: [
        RouterModule,
        BrowserModule,
        HttpClientModule,
        DevExtremeModule,
    ],
    providers: [
        WizardRouting,
        WizardBook
    ],
    bootstrap: [WizardContainerComponent]
})
export class WizardModule {
}
