import {Editor} from "../../entities/library/editor"
import {ItemAbstractReviver} from "@rebolon/json-reviver";

export class EditorReviver extends ItemAbstractReviver
{
    /**
     *
     * @returns {string}
     */
    getNodeName() {
        return 'editor'
    }

    /**
     *
     * @returns {Object}
     */
    getNewEntity() {
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
    public getManyRelPropsName()
    {
        return {}
    }

    /**
     * {@inheritdoc}
     */
    public getOneRelPropsName()
    {
        return {}
    }
}
