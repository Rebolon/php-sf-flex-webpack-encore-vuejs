import {NgModule} from "@angular/core";
import {WizardSumupComponent} from "./wizard-sumup/wizard-sumup.component";
import {WizardBook} from "./services/wizard-book";
import {WizardRouting} from "./services/wizard-routing";
import {DevExtremeModule} from "devextreme-angular";
import {WizardListAuthorsComponent} from "./wizard-list-authors/wizard-list-authors.component";
import {WizardListEditorsComponent} from "./wizard-list-editors/wizard-list-editors.component";
import {CommonModule} from "@angular/common";
import {BroadcastChannelApi} from "./services/broadcast-channel-api";
import {BookReviver} from "./services/reviver/library/bookReviver";
import {AuthorReviver} from "./services/reviver/library/authorReviver";
import {AuthorsReviver} from "./services/reviver/library/authorsReviver";
import {EditorsReviver} from "./services/reviver/library/editorsReviver";
import {EditorReviver} from "./services/reviver/library/editorReviver";
import {JobReviver} from "./services/reviver/library/jobReviver";
import {SerieReviver} from "./services/reviver/library/serieReviver";

@NgModule({
    imports: [
        CommonModule,
        DevExtremeModule
    ],
    declarations: [
        WizardListAuthorsComponent,
        WizardListEditorsComponent,
        WizardSumupComponent,
    ],
    exports: [
        CommonModule,
        DevExtremeModule,
        WizardListAuthorsComponent,
        WizardListEditorsComponent,
        WizardSumupComponent
    ],
    providers: [
        BroadcastChannelApi,
        WizardRouting,
        WizardBook,
        AuthorReviver,
        AuthorsReviver,
        BookReviver,
        EditorReviver,
        EditorsReviver,
        JobReviver,
        SerieReviver,
    ]
})
export class SharedModule {
}
