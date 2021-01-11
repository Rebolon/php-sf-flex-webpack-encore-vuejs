import {Component, OnDestroy, OnInit} from '@angular/core';
import {Subject} from 'rxjs';
import {takeUntil} from 'rxjs/operators';
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
    protected book: undefined|Book
    public isLoading = true

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
                .subscribe((res) => {
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

    sendBookToParent() {
        this.broadcastChannel.sendBook(this.book)
    }

    saveBookTitle(newTitle: string) {
      if (this.book === undefined) {
        return
      }

      this.book.title = newTitle
      this.bookService.updateBook(this.book)
      this.bookService.save()

      // might be useless if parent already has a listener on Book Observable, it will depends on the pattern used by the datagrid to refresh
      if (this.isSecondWindow()) {
        this.sendBookToParent()
      }
    }

    isSecondWindow() {
        return window['name'] === 'second-screen'
    }
}
