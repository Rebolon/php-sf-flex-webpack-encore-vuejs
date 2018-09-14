import {Editor} from "./editor";

export class Editors {
    id: number
    editor: Editor | number
    publicationDate: Date = new Date()
    collection?: string = ''
    isbn?: string = ''
}