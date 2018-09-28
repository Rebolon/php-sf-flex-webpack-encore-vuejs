import {Component, EventEmitter, Input, Output} from '@angular/core'
import {Authors} from "../../../entities/library/authors";

@Component({
    selector: 'my-wizard-list-authors',
    templateUrl: './wizard-list-authors.component.html',
    styleUrls: ['./wizard-list-authors.component.scss']
})
export class WizardListAuthorsComponent {
    @Input() authors: Array<Authors>
    @Input() removeEnabled = false
    @Output() removeAuthors: EventEmitter<Authors> = new EventEmitter()
}
