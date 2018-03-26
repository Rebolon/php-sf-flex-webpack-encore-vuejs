import { Injectable} from '@angular/core'
import {Observable} from "rxjs/Observable";
import 'rxjs/add/operator/filter'
import {Subject} from "rxjs/Subject";
import {BehaviorSubject} from "rxjs/BehaviorSubject";
import {Book} from "../../../entities/library/book";
import {Editors} from "../../../entities/library/editors";
import {Authors} from "../../../entities/library/authors";
import {Map} from "immutable";
import {ApiService} from '../../../services/api';
import {apiConfig} from "../../../../../lib/config";
import {options} from "../tools/form-options";

@Injectable()
export class WizardBook {
    private _book: BehaviorSubject<Book> = new BehaviorSubject<Book>(null)
    book: Observable<any> = this._book.asObservable().filter(value => Boolean(value))
    protected apiConfig
    protected options = options

    constructor(private api: ApiService) {
      this.apiConfig = apiConfig
    }

    createBook(book: Book) {
        this._book.next(book)
    }

    updateBook(book: Book) {
        const currentBook = this._book.getValue()

        book.getOwnProps().forEach(prop => currentBook[prop] = book[prop])
        this._book.next(book)
    }

    setEditors(editors: Array<Editors>) {
        const book = this._book.getValue()
        book.editors = editors
        this._book.next(book)
    }

    addEditors(editors: Editors) {
        const book = this._book.getValue()
        book.editors.push(editors)
        this._book.next(book)
    }

    setAuthors(authors: Array<Authors>) {
        const book = this._book.getValue()
        book.authors = authors
        this._book.next(book)
    }

    addAuthors(authors: Authors) {
        const book = this._book.getValue()
        book.authors.push(authors)
        this._book.next(book)
    }

    /**
     *
     * @returns {Subject<any>}
     */
    save() {
        const res = new Subject()
        const book = this._book.getValue()

        // clean useless props
        // @todo there is an error in the ParamConverter if the item is empty !!!
        // should be useless
        if (book.editors && !book.editors.length) {
            delete book.editors
        }

        if (book.authors && !book.authors.length) {
            delete book.authors
        }
        // end of useless code (normally)

        const body = {
        book: book
        }
        if (book.id) {
        this.api
            .put('/booksiu/special_3', JSON.stringify(body))
            .subscribe(() => {
                res.next(book)
            }, err => {
                res.error(err)
            })
        } else {
        this.api
            .post('/booksiu/special_3', JSON.stringify(body))
            .subscribe(newBook => {
                book.id = newBook.id
                this._book.next(book)
                res.next(book)
            }, err => {
                res.error(err)
            })
        }

        return res
    }

    reset() {
        const book = new Book()

        this._book.next(book)
    }
}
