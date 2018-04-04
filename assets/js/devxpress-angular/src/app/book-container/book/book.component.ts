import {Component, OnDestroy, OnInit} from '@angular/core'
import {BookModel} from "../../../models/book.model";
import { Observable } from 'rxjs/Observable'
import {Subject} from "rxjs/Subject";
import 'rxjs/add/operator/toPromise';
import 'rxjs/add/operator/takeUntil'
import {ActivatedRoute} from "@angular/router";
import {WizardBook} from "../../shared/services/wizard-book";
import notify from "devextreme/ui/notify";
import {BroadcastChannelApi} from "../../shared/services/broadcast-channel-api";

@Component({
  selector: 'my-book',
  templateUrl: './book.component.html',
  styleUrls: ['./book.component.scss']
})
export class BookComponent implements OnInit, OnDestroy {
    protected ngUnsubscribe: Subject<void> = new Subject()
    protected book: BookModel

  constructor(private route: ActivatedRoute, private bookService: WizardBook, private broadcastChannel: BroadcastChannelApi) {}

  ngOnInit(): void {
      // Observe the params from activatedRoute AND then load the story
      this.route.paramMap
          .takeUntil(this.ngUnsubscribe)
          .subscribe(params => {
              const bookId = params.get('id')

              if (bookId === null) {
                  throw new Error('Cannot access Book without any selected one')
              }

              this.bookService
                  .get(parseInt(bookId, 10))
                  .takeUntil(this.ngUnsubscribe)
                  .subscribe((res: BookModel) => this.book = res)
          })
  }

  ngOnDestroy() {
      this.ngUnsubscribe.next()
      this.ngUnsubscribe.complete()
  }

  sendMessage(){
        this.broadcastChannel.message.subscribe(msg => {
            console.info('message received', msg)
        })

      this.broadcastChannel.ping()
  }
}
