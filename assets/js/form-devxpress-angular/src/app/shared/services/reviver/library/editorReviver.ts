import {Editor} from '../../../../../entities/library/editor';
import {ItemAbstractReviver} from '@rebolon/json-reviver';
import {Injectable} from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class EditorReviver extends ItemAbstractReviver
{
    getNodeName(): string {
        return 'editor'
    }

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
    public getEzPropsName() {
        return ['id', 'name', ]
    }

    public getManyRelPropsName(): Object {
        return {}
    }

    public getOneRelPropsName(): Object {
        return {}
    }
}
