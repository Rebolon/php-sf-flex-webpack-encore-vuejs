import {Component, OnDestroy, OnInit} from '@angular/core'
import {WizardRouting} from "../../../shared/services/wizard-routing";
import {Book} from "../../../../entities/library/book";
import {options} from "../../../shared/tools/form-options";
import {WizardBook} from "../../../shared/services/wizard-book";
import {CacheKey} from "../../enums/cache-key";
import notify from "devextreme/ui/notify";
import {Subscription} from "rxjs/Subscription";

@Component({
    selector: 'my-wizard-book',
    templateUrl: './book-form.component.html',
    styleUrls: ['./book-form.component.scss']
})
export class BookFormComponent implements OnInit, OnDestroy {
    book: Book
    options = options
    nextPageNotifyMsg = "Local save success"

    // pattern to unsubscribe everything
    protected subscriptions: Array<Subscription> = []

    constructor(private bookService: WizardBook, private routing: WizardRouting) {
    }

    ngOnInit() {
        this.subscriptions.push(
            this.bookService.book.subscribe(book => {
                console.info('bookFormcomp, book received', book.name, book)
                this.book = book
            })
        )
    }

    /**
     * Unsubscribe from everything ye'all
     */
    ngOnDestroy() {
        this.subscriptions.forEach(subs => subs.unsubscribe())
    }

    goCancel(ev: Event) {
        ev.preventDefault()

        // reset localStorage and service
        // @todo should be in a Guar route when exit wizard/*
        for (let cacheKey of Object.values(CacheKey)) {
            localStorage.removeItem(cacheKey)
        }

        this.bookService.createBook(new Book())

        // go to cancel route
        this.routing.goCancel(ev)
    }

    goNext(ev: Event) {
        ev.preventDefault()

        notify({
            message: this.nextPageNotifyMsg,
            position: {
                my: "center top",
                at: "center top"
            }
        }, "success", 3000);

        // propagate to other component
        this.bookService.updateBook(this.book)

        // go next
        this.routing.goNext(ev)
    }
}
