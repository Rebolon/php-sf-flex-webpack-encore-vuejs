import {Editor} from "./editor";
import {EntityAbstract} from "../abstract";

export class Editors extends EntityAbstract {
    id: number
    editor: Editor | number
    publicationDate: Date = new Date()
    collection?: string = ''
    isbn?: string = ''
}