import {Editors} from "./editors";
import {Authors} from "./authors";
import {Serie} from "./serie";
import {EntityInterface} from "@rebolon/json-reviver";

export class Book implements EntityInterface {
    id: number
    title: string = ''
    description?: string = ''
    indexInSerie?: number

    editors: Array<Editors>
    authors: Array<Authors>
    serie?: Serie

    addEdition(edition: Editors) {
        if (typeof this.editors == 'undefined') {
            this.editors = []
        }

        this.editors.push(edition)
    }

    setEdition(edition: Editors) {
        this.editors = []

        this.editors.push(edition)
    }

    addAuthor(author: Authors) {
        if (typeof this.authors == 'undefined') {
            this.authors = []
        }

        this.authors.push(author)
    }

    setAuthors(author: Authors) {
        this.authors = []

        this.authors.push(author)
    }
}