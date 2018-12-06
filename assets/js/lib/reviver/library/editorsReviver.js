import {Editors} from "../../entities/library/editors"
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
    getEzPropsName()
    {
        return ['id', 'publicationDate', 'collection', 'isbn', ]
    }

    /**
     * {@inheritdoc}
     */
    getManyRelPropsName()
    {
        return {}
    }

    /**
     * {@inheritdoc}
     */
    getOneRelPropsName()
    {
        return {
            'editor': {
                'reviver': this.editorReviver,
                'registryKey': 'editor'
            }
        }
    }
}
