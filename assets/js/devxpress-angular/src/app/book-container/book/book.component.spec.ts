import { TestBed, async } from '@angular/core/testing';
import { BookComponent } from './book.component';
import {WizardSumupComponent} from "../../shared/wizard-sumup/wizard-sumup.component";
import {WizardListEditorsComponent} from "../../shared/wizard-list-editors/wizard-list-editors.component";
import {WizardListAuthorsComponent} from "../../shared/wizard-list-authors/wizard-list-authors.component";
import {DevExtremeModule} from "devextreme-angular";
import {RouterTestingModule} from "@angular/router/testing";
import {WizardBook} from "../../shared/services/wizard-book";
import {ApiService} from "../../../services/api";
import {HttpClientTestingModule} from "@angular/common/http/testing";
import {BookReviver} from "../../shared/services/reviver/library/bookReviver";
import {AuthorsReviver} from "../../shared/services/reviver/library/authorsReviver";
import {EditorsReviver} from "../../shared/services/reviver/library/editorsReviver";
import {SerieReviver} from "../../shared/services/reviver/library/serieReviver";
import {JobReviver} from "../../shared/services/reviver/library/jobReviver";
import {AuthorReviver} from "../../shared/services/reviver/library/authorReviver";
import {EditorReviver} from "../../shared/services/reviver/library/editorReviver";
import {BroadcastChannelApi} from "../../shared/services/broadcast-channel-api";
import {SharedModule} from "../../shared/shared.module";
import {ApiServiceMock} from "../../../services/api-mock";
describe('BookComponent', () => {
  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [
          BookComponent,
      ],
      imports: [
          RouterTestingModule,
          HttpClientTestingModule,
          SharedModule,
          DevExtremeModule,
      ],
      providers: [
          {provide: ApiService, useClass: ApiServiceMock},
      ]
    }).compileComponents();
  }));
  it('should create the component', async(() => {
    const fixture = TestBed.createComponent(BookComponent);
    const app = fixture.debugElement.componentInstance;
    expect(app).toBeTruthy();
  }));
});
