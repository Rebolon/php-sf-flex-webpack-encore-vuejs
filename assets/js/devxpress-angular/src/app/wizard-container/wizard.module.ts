import {NgModule} from '@angular/core';
import {RouterModule} from "@angular/router";
import {WizardContainerComponent} from "./app/wizard-container.component";
import {WizardStepComponent} from "./app/wizard-step/wizard-step.component";
import {BookFormComponent} from "./app/book-form/book-form.component";
import {EditorsFormComponent} from "./app/editors-form/editors-form.component";
import {AuthorsFormComponent} from "./app/authors-form/authors-form.component";
import {BrowserModule} from "@angular/platform-browser";
import {HttpClientModule} from "@angular/common/http";
import {SharedModule} from "../shared/shared.module";

@NgModule({
    declarations: [
        WizardContainerComponent,
        WizardStepComponent,
        BookFormComponent,
        EditorsFormComponent,
        AuthorsFormComponent
    ],
    imports: [
        RouterModule,
        BrowserModule,
        HttpClientModule,
        SharedModule,
    ],
    providers: [
    ],
    bootstrap: [WizardContainerComponent]
})
export class WizardModule {
}
