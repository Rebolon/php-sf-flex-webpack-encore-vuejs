import {Component, EventEmitter, Input, Output} from '@angular/core'
import {Editors} from "../../../entities/library/editors";

@Component({
  selector: 'my-wizard-list-editors',
  templateUrl: './wizard-list-editors.component.html',
  styleUrls: ['./wizard-list-editors.component.scss']
})
export class WizardListEditorsComponent {
    @Input() editors: Array<Editors>
    @Input() removeEnabled = false
    @Output() removeEditors: EventEmitter<Editors> = new EventEmitter()
}
