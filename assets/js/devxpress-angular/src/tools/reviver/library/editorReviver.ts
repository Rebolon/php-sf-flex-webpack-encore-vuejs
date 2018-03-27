import {Editor} from "../../../entities/library/editor"
import {ItemAbstractReviver} from "../itemAbstractReviver";

export class EditorReviver extends ItemAbstractReviver
{
    /**
     *
     * @returns {string}
     */
    getNodeName(): string {
        return 'editor'
    }

    /**
     *
     * @returns {Object}
     */
    getNewEntity(): Object {
        return new Editor()
    }

    /**
     * {@inheritdoc}
     * for this kind of json:
     * {
     *   "editor": {
     *     "name": "Hachette"
     *   }
     * }
     */
    public getEzPropsName()
    {
        return ['id', 'name', ]
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
        return {}
    }
}
