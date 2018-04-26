import {Component, Input, OnDestroy, OnInit} from '@angular/core'
import {Book} from "../../../entities/library/book";
import {Subject} from "rxjs/Subject";
import {Map} from "immutable";
import {WizardBook} from "../services/wizard-book";
import {Subscription} from "rxjs/Subscription";

@Component({
  selector: 'my-wizard-sumup',
  templateUrl: './wizard-sumup.component.html',
  styleUrls: ['./wizard-sumup.component.scss']
})
export class WizardSumupComponent implements OnInit, OnDestroy {
    // true: the book to display will be immutable until the Subject return a new value
    // false: all changes on the book instance (every where in the app) will have impact here
    @Input() snapshot = false
    book: any

    // pattern to unsubscribe everything, if you don't you risk memory leak !
    protected subscriptions: Array<Subscription> = []

    constructor (private bookService: WizardBook) {}

    ngOnInit() {
        this.subscriptions.push(
            this.bookService.book.subscribe(book => {
                if (!this.snapshot) {
                    this.book = book

                    return
                }

                let immutableBook = Map(book)

                const pseudoBook = immutableBook.toJS()

                this.book = pseudoBook
            })
        )
    }

    /**
     * Unsubscribe from everything ye'all
     */
    ngOnDestroy() {
        this.subscriptions.forEach(subs => subs.unsubscribe())
    }
}
