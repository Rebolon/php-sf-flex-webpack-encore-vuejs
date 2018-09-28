import {Editors} from '../../../../../entities/library/editors';
import {EditorReviver} from './editorReviver';
import {ListAbstractReviver} from '@rebolon/json-reviver';
import {Injectable} from '@angular/core';

@Injectable()
export class EditorsReviver extends ListAbstractReviver
{
    protected editorReviver: EditorReviver

    constructor (
        editorReviver: EditorReviver
    ) {
        super()

        this.editorReviver = editorReviver
    }

    getNodeName(): string {
        return 'editors'
    }

    getNewEntity(): Object {
        return new Editors()
    }

    /**
     * {@inheritdoc}
     * for this kind of json:
     * {
     *   "editors": {
     *     "publicationsDate": "1519664915",
     *     "collection": "A collection or edition name of the publication",
     *     "isbn": '2-87764-257-7',
     *     "editor": {
     *       ...
     *     }
     *   }
     * }
     */
    public getEzPropsName() {
        return ['id', 'publicationDate', 'collection', 'isbn', ]
    }

    public getManyRelPropsName(): Object {
        return {}
    }

    public getOneRelPropsName(): Object {
        return {
            'editor': {
                'reviver': this.editorReviver,
                'registryKey': 'editor'
            }
        }
    }
}
