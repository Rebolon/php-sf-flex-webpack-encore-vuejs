import {Editors} from "../../entities/library/editors"
import {EditorReviver} from './editorReviver'
import {ListAbstractReviver} from "@rebolon/json-reviver/src"

export class EditorsReviver extends ListAbstractReviver
{
    /**
     * @var EditorReviver
     */
    protected editorReviver: EditorReviver

    /**
     *
     * @param {EditorReviver} editorReviver
     */
    constructor (
        editorReviver: EditorReviver
    ) {
        super()

        this.editorReviver = editorReviver
    }

    /**
     *
     * @returns {string}
     */
    getNodeName(): string {
        return 'editors'
    }

    /**
     *
     * @returns {Object}
     */
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
    public getEzPropsName()
    {
        return ['id', 'publicationDate', 'collection', 'isbn', ]
    }

    /**
     * {@inheritdoc}
     */
    public getManyRelPropsName(): Object
    {
        return {}
    }

    /**
     * {@inheritdoc}
     */
    public getOneRelPropsName(): Object
    {
        return {
            'editor': {
                'reviver': this.editorReviver,
                'registryKey': 'editor'
            }
        }
    }
}
