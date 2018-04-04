import {Author} from "../../entities/library/author"
import {ItemAbstractReviver} from "@rebolon/json-reviver";

export class AuthorReviver extends ItemAbstractReviver
{
    /**
     *
     * @returns {string}
     */
    getNodeName() {
        return 'author'
    }

    /**
     *
     * @returns {Object}
     */
    getNewEntity() {
        return new Author()
    }

    /**
     * {@inheritdoc}
     * for this kind of json:
     * {
     *   "author": {
     *     "firstname": "Paul",
     *     "lastname": "Smith"
     *   }
     * }
     */
    public getEzPropsName()
    {
        return ['id', 'firstname', 'lastname', ]
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
