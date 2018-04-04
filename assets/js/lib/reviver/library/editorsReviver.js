import {Editors} from "../../entities/library/editors"
import {EditorReviver} from './editorReviver'
import {ListAbstractReviver} from "@rebolon/json-reviver";

export class EditorsReviver extends ListAbstractReviver
{
    /**
     *
     * @param {EditorReviver} editorReviver
     */
    constructor (
        editorReviver
    ) {
        super()

        this.editorReviver = editorReviver
    }

    /**
     *
     * @returns {string}
     */
    getNodeName() {
        return 'editors'
    }

    /**
     *
     * @returns {Object}
     */
    getNewEntity() {
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
    public getEzPropsName()
    {
        return ['id', 'publicationDate', 'collection', 'isbn', ]
    }

    /**
     * {@inheritdoc}
     */
    public getManyRelPropsName()
    {
        return {}
    }

    /**
     * {@inheritdoc}
     */
    public getOneRelPropsName()
    {
        return {
            'editor': {
                'reviver': this.editorReviver,
                'registryKey': 'editor'
            }
        }
    }
}
