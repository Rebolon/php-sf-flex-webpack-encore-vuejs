import {Serie} from '../../../../../entities/library/serie';
import {ItemAbstractReviver} from '@rebolon/json-reviver';
import {Injectable} from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class SerieReviver extends ItemAbstractReviver
{
    getNodeName(): string {
        return 'serie'
    }

   getNewEntity(): Object {
        return new Serie()
    }

    /**
     * {@inheritdoc}
     * for this kind of json:
     * {
     *   "serie": {
     *     "name": "The serie name"
     *   }
     * }
     */
    public getEzPropsName() {
        return ['id', 'name', ]
    }

    public getManyRelPropsName(): Object {
        // for instance i don't want to allow the creation of a serie with all embeded books, this is not a usual way of working
        // that's why i don't add books here
        return {}
    }

    public getOneRelPropsName(): Object {
        return {}
    }
}
