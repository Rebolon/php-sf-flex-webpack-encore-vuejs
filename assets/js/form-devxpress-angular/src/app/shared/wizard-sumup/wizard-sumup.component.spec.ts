import { TestBed, async } from '@angular/core/testing';
import { WizardSumupComponent } from './wizard-sumup.component';
import {SharedModule} from "../shared.module";
import {WizardListAuthorsComponent} from "../wizard-list-authors/wizard-list-authors.component";
import {WizardListEditorsComponent} from "../wizard-list-editors/wizard-list-editors.component";
import {DevExtremeModule} from "devextreme-angular";
import {JobReviver} from "../services/reviver/library/jobReviver";
import {WizardBook} from "../services/wizard-book";
import {EditorsReviver} from "../services/reviver/library/editorsReviver";
import {EditorReviver} from "../services/reviver/library/editorReviver";
import {BookReviver} from "../services/reviver/library/bookReviver";
import {WizardRouting} from "../services/wizard-routing";
import {SerieReviver} from "../services/reviver/library/serieReviver";
import {AuthorReviver} from "../services/reviver/library/authorReviver";
import {BroadcastChannelApi} from "../services/broadcast-channel-api";
import {AuthorsReviver} from "../services/reviver/library/authorsReviver";
import {ApiService} from "../../../services/api";
import {ApiServiceMock} from "../../../services/api-mock";
import {HttpClientTestingModule} from "@angular/common/http/testing";
describe('WizardSumupComponent', () => {
  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [
          WizardSumupComponent,
          WizardListAuthorsComponent,
          WizardListEditorsComponent
      ],
      imports: [
          HttpClientTestingModule,
          DevExtremeModule
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
          {provide: ApiService, useClass: ApiServiceMock}
      ]
    }).compileComponents();
  }));
  it('should create the component', async(() => {
    const fixture = TestBed.createComponent(WizardSumupComponent);
    const app = fixture.debugElement.componentInstance;
    expect(app).toBeTruthy();
  }));
});
