export class Editors {
    constructor() {
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
