import {Component, OnDestroy, OnInit} from '@angular/core';
import { Observable, Subject } from 'rxjs';
import { takeUntil } from 'rxjs/operators';
import {ActivatedRoute} from '@angular/router';
import {WizardBook} from '../../shared/services/wizard-book';
import {BroadcastChannelApi} from '../../shared/services/broadcast-channel-api';
import {Book} from '../../../entities/library/book';

@Component({
  selector: 'my-book',
  templateUrl: './book.component.html',
  styleUrls: ['./book.component.scss'],
})
export class BookComponent implements OnInit, OnDestroy {
    protected ngUnsubscribe: Subject<void> = new Subject()
    protected book: Book
    protected isLoading = true

    constructor(private route: ActivatedRoute, private bookService: WizardBook, private broadcastChannel: BroadcastChannelApi) {}

    ngOnInit(): void {
        // Observe the params from activatedRoute AND then load the story
        this.route.paramMap
            .pipe(takeUntil(this.ngUnsubscribe))
            .subscribe(params => {
              const bookId = params.get('id')

              if (bookId === null) {
                throw new Error('Cannot access Book without any selected one')
              }

              // subscribe to the book Observable
              this.bookService
                .book
                .subscribe((res: Book) => this.book = res)

              // then do the main call
              this.bookService
                .get(parseInt(bookId, 10))
                .pipe(
                  takeUntil(this.ngUnsubscribe)
                )
                .subscribe((res: Book) => {
                  this.isLoading = false
                })
            })
    }

    ngOnDestroy() {
        this.ngUnsubscribe.next()
        this.ngUnsubscribe.complete()
    }

    sendMessage() {
        this.broadcastChannel.ping()
    }

    sendBookToParent(newTitle) {
        this.book.title = newTitle

        this.broadcastChannel.sendBook(this.book)
    }

    isSecondWindow() {
        return window['name'] === 'second-screen'
    }
}
