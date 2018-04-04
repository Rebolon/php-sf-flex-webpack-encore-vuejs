import {EntityAbstract} from "../abstract";

export class Book extends EntityAbstract {
    constructor() {
        super()

        this.id
        this.title
        this.description
        this.indexInSerie
        this.editors = []
        this.authors = []
        this.serie
    }

    addEdition(edition) {
        if (typeof this.editors == 'undefined') {
            this.editors = []
        }

        this.editors.push(edition)
    }

    setEdition(edition) {
        this.editors = []

        this.editors.push(edition)
    }

    addAuthor(author) {
        if (typeof this.authors == 'undefined') {
            this.authors = []
        }

        this.authors.push(author)
    }

    setAuthors(author) {
        this.authors = []

        this.authors.push(author)
    }
}