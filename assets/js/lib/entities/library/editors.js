import {EntityAbstract} from "../abstract";

export class Editors extends EntityAbstract {
    constructor() {
        super()

        this.id
        this.editor
        this.publicationDate
        this.collection
        this.isbn
    }

    setDate(date) {
        this.date = typeof date === 'object' ? date : new Date(date)
    }
}