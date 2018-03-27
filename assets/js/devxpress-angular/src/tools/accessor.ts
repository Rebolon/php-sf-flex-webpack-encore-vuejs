/**
 *
 * @param prop
 * @param {EntityInterface} entity
 * @param {Array<any>} json
 * @returns {EntityInterface}
 */
import {EntityInterface} from "./reviver/library/entityInterface";

export function accessor(prop, entity: EntityInterface, values: Array<any>) {
    // use setter first, then the prop
    const setter = 'set' + (prop[0].toUpperCase() + prop.slice(1))
    const value = typeof values[prop] != 'undefined' ? values[prop] : values
    if (entity.hasOwnProperty(setter)) {
        entity[setter](value)
    } else {
        entity[prop] = value
    }

    return entity
}
