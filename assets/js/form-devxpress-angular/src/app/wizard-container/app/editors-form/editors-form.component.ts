import {Component, OnDestroy, OnInit, ViewChild} from '@angular/core'
import {WizardRouting} from "../../../shared/services/wizard-routing";
import {options} from "../../../shared/tools/form-options";
import {Editors} from "../../../../entities/library/editors";
import {Editor} from "../../../../entities/library/editor";
import CustomStore from "devextreme/data/custom_store";
import {ApiService} from "../../../../services/api";
import {apiConfig} from "../../../../../../lib/config";
import {DxFormComponent, DxLookupComponent} from "devextreme-angular";
import {WizardBook} from "../../../shared/services/wizard-book";
import {CacheKey} from "../../enums/cache-key";
import notify from "devextreme/ui/notify";
import {Subscription} from "rxjs/Subscription";

@Component({
  selector: 'my-wizard-editors',
  templateUrl: './editors-form.component.html',
  styleUrls: ['./editors-form.component.scss']
})
export class EditorsFormComponent implements OnInit, OnDestroy {
    // @todo should use dxAutocomplete (not described in the demo but exists in documentation > UI widgets)
    @ViewChild(DxLookupComponent) dxLookUp:DxLookupComponent
    editors: Array<Editors>
    options = options

    protected apiConfig
    public dataSource: any = {}

    // pattern to unsubscribe everything
    protected subscriptions: Array<Subscription> = []

    constructor(private bookService: WizardBook, private routing: WizardRouting, private api: ApiService) {
        // teak: to allow the validation rule custom callack (validateAddOrSelectEditor) to access to this component instance
        // if you don't do that, the method validateAddOrSelectEditor' will be binded to the validator instance, not this component,
        // so you can't access to the lookup or any other element of the component
        this.validateAddOrSelectEditor = this.validateAddOrSelectEditor.bind(this)

        this.apiConfig = apiConfig

        this.dataSource.store = new CustomStore({
            load: (loadOptions: any) => {
                let itemPerPage = this.apiConfig.itemsPerPage

                let options = {
                    params: []
                }

                // manage number of items wished by the datagrid and that the API must return (take care to configure the config/packages/api_platform.yaml:client_items_per_page key)
                if (loadOptions.take) {
                    options.params[this.apiConfig.itemsPerPageParameterName] = loadOptions.take
                    itemPerPage = loadOptions.take
                }

                // manage the pagination: ApiPlatform works with hydra system and so a page number whereas DevXpress datagrid uses a skip/take parameter, so it requires a small Math calc
                if(loadOptions.skip) {
                    options.params['page'] = loadOptions.skip > 0 ? Math.ceil(loadOptions.skip / itemPerPage) +1 : 1
                }

                // search on Editor name
                if (loadOptions.searchValue) {
                    const searchKey = loadOptions.searchExpr ? loadOptions.searchExpr : 'name'
                    options.params[searchKey] = loadOptions.searchValue
                }

                return this.api.get('/editors', options)
                    .toPromise()
                    .then(json => {
                        return {
                            data: json['hydra:member'],
                            totalCount: json['hydra:totalItems']
                        }
                    })
                    .catch(error => {
                        console.log(error)
                        throw 'Data Loading Error'
                    })
            }
        })
    }

    ngOnInit() {
        // book cache to restore wizard
        const cache = localStorage.getItem(CacheKey.EDITORS);
        this.editors = []
        if(cache) {
            this.editors = JSON.parse(cache)
        }
    }

    /**
     * Unsubscribe from everything ye'all
     */
    ngOnDestroy() {
        this.subscriptions.forEach(subs => subs.unsubscribe())
    }

    /**
     * @todo when it's an existing
     *
     *
     * @param ev
     * @param dxFormComponent
     */
    addEditor(ev, editorsForm: DxFormComponent, editorForm: DxFormComponent) {
        ev.preventDefault()

        // we should never enter this coz the button is disabled until everything is ok
        // but some user may force the form submit (yes they can) so we have to prevent that kind of hack
        if (!editorsForm.instance.validate().isValid || !editorForm.instance.validate().isValid) {
            notify({
                message: `Your form still has errors, fix them before trying to add the edition`,
                position: {
                    my: "center top",
                    at: "center top"
                }
            }, "warning", 3000);

            return false
        }

        const editor = new Editor()
        if (this.dxLookUp.value) {
            // @todo improve this with maybe decomposition or anything else
            editor.id = this.dxLookUp.value.id
            editor.name = this.dxLookUp.value.name
        } else {
            editor.name = editorForm.formData.name
        }

        const editors = new Editors()
        editors.editor = editor
        // @todo improve this with maybe decomposition or anything else
        editors.collection = editorsForm.formData.collection
        editors.isbn = editorsForm.formData.isbn
        editors.publicationDate = editorsForm.formData.publicationDate

        this.editors.push(editors)

        // reset the forms
        this.dxLookUp.value = undefined
        editorsForm.instance.resetValues()
        editorForm.instance.resetValues()

        // save in cache when user refresh or come back later
        localStorage.setItem(CacheKey.EDITORS, JSON.stringify(this.editors))

        // notify the user
        notify({
            message: `The edition has been saved locally`,
            position: {
                my: "center top",
                at: "center top"
            }
        }, "success", 3000);
    }

    /**
     * @param editor
     */
    removeEditor(editorToRemove) {
        this.editors = this.editors.filter(editor => editor !== editorToRemove)

        // save in cache when user refresh or come back later
        localStorage.setItem(CacheKey.EDITORS, JSON.stringify(this.editors))
    }

    // reset editorForm if user has selected an editor in the lookup
    resetNameEditor(ev, formEditor: DxFormComponent) {
        if (ev.value) {
            formEditor.instance.resetValues()
        }
    }

    validateAddOrSelectEditor(ev) {
        let isValid = false
        if (ev.value) {
            this.dxLookUp.value = undefined
            isValid = true
        } else if (this.dxLookUp.value) {
            isValid = true
        }

        return isValid
    }

    goBack(ev: Event) {
        ev.preventDefault()

        this.routing.goBack(ev)
    }

    goNext(ev: Event) {
        ev.preventDefault()

        this.bookService.setEditors(this.editors)

        this.routing.goNext(ev)
    }
}
