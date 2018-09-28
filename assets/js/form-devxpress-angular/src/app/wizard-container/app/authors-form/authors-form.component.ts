import {Component, OnDestroy, OnInit, ViewChild} from '@angular/core'
import {WizardRouting} from "../../../shared/services/wizard-routing";
import {options} from "../../../shared/tools/form-options";
import {Authors} from "../../../../entities/library/authors";
import {apiConfig} from "../../../../../../lib/config";
import {ApiService} from "../../../../services/api";
import CustomStore from "devextreme/data/custom_store";
import DataSource from "devextreme/data/data_source";
import {WizardBook} from "../../../shared/services/wizard-book";
import {CacheKey} from "../../enums/cache-key";
import {Subscription} from "rxjs/Subscription";
import {DxAutocompleteComponent, DxFormComponent, DxLookupComponent, DxSelectBoxComponent} from "devextreme-angular";
import notify from 'devextreme/ui/notify';
import {Author} from '../../../../entities/library/author';
import {Job} from '../../../../entities/library/job';
import {Book} from '../../../../entities/library/book';

@Component({
    selector: 'my-wizard-authors',
    templateUrl: './authors-form.component.html',
    styleUrls: ['./authors-form.component.scss']
})
export class AuthorsFormComponent implements OnInit, OnDestroy {
    @ViewChild(DxAutocompleteComponent) dxAutocomplete: DxAutocompleteComponent
    @ViewChild(DxSelectBoxComponent) dxSelectBox: DxSelectBoxComponent
    authors: Array<Authors>
    options = options

    protected apiConfig
    public authorsDatasource: any = {}
    public jobDatasource: any = {}

    // pattern to unsubscribe everything
    protected subscriptions: Array<Subscription> = []

    constructor(private bookService: WizardBook, private routing: WizardRouting, private api: ApiService) {// teak: to allow the validation rule custom callack (validateAddOrSelectEditor) to access to this component instance
        // if you don't do that, the method validateAddOrSelectEditor' will be binded to the validator instance, not this component,
        // so you can't access to the lookup or any other element of the component
        this.validateAddOrSelectAuthor = this.validateAddOrSelectAuthor.bind(this)

        this.apiConfig = apiConfig

        this.api.get('/jobs', options)
            .toPromise()
            .then(json => {
                this.jobDatasource = new DataSource({
                    store: json['hydra:member']
                })
            })
            .catch(error => {
                console.log(error)
                throw 'Data Loading Error'
            })

        this.authorsDatasource.store = new CustomStore({
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
                if (loadOptions.skip) {
                    options.params['page'] = loadOptions.skip > 0 ? Math.ceil(loadOptions.skip / itemPerPage) + 1 : 1
                }

                // search on Author name
                if (loadOptions.searchValue) {
                    const searchKey = loadOptions.searchExpr ? loadOptions.searchExpr : 'firstname'
                    options.params[searchKey] = loadOptions.searchValue
                }

                return this.api.get('/authors', options)
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
        const cache = localStorage.getItem(CacheKey.AUTHORS);
        this.authors = []
        if (cache) {
            this.authors = JSON.parse(cache)
        }
    }

    /**
     * Unsubscribe from everything ye'all
     */
    ngOnDestroy() {
        this.subscriptions.forEach(subs => subs.unsubscribe())
    }

    goBack(ev: Event) {
        ev.preventDefault()
        // @todo validate

        localStorage.setItem(CacheKey.AUTHORS, JSON.stringify(this.authors))
        this.routing.goBack(ev)
    }

    goEnd(ev: Event) {
        ev.preventDefault()

        this.bookService.setAuthors(this.authors)

        this.bookService.save()
            .subscribe((book: Book) => {
                notify({
                    message: 'New book created with ID: ' + book.id,
                    position: {
                        my: "center top",
                        at: "center top"
                    }
                }, "success", 3000)


                const cacheKeys = Object.values(CacheKey)
                // reset localStorage and service
                // @todo should be in a Guar route when exit wizard/*
                for (let cacheKey of cacheKeys) {
                    localStorage.removeItem(cacheKey)
                }
                this.bookService.reset()
                this.routing.goEnd(ev)
            }, err => {
                let msg = 'An error happened'
                if (err.error
                    && err.error.detail) {
                    msg += `: ${err.error.detail}`
                    if (err.error.violations) {
                        for (let i = 0; i < err.error.violations.length; i++) {
                            console.warn(err.error.violations[i].propertyPath + ': ' + err.error.violations[i].message)
                        }
                    }
                } else {
                    if (typeof err == 'string') {
                        msg += ': ' + err
                    } else {
                        if (err.statusText) {
                            msg += ': ' + err.statusText
                        }
                    }
                }

                notify({
                    message: msg,
                    position: {
                        my: "center top",
                        at: "center top"
                    }
                }, "error", 3000)
            })
    }

    // @todo how to reset only some fields ???
    resetNameAuthor(ev, fieldFirstname, fieldLastname) {
        if (ev.selectedItem) {
            // @todo don't undertand why but it also resets fieldLastname
            fieldFirstname.instance.resetValues()
        }
    }

    validateAddOrSelectAuthor(ev) {
        let isValid = false
        if (ev.value) {
            // @todo do the same in editors validation
            this.dxAutocomplete.instance.reset()
            isValid = true
        } else if (this.dxAutocomplete.value) {
            isValid = true
        }

        return isValid
    }

    addAuthor(ev, authorsForm) {
        ev.preventDefault()

        // we should never enter this coz the button is disabled until everything is ok
        // but some user may force the form submit (yes they can) so we have to prevent that kind of hack
        if (!authorsForm.instance.validate().isValid) {
            notify({
                message: `Your form still has errors, fix them before trying to add the authors`,
                position: {
                    my: "center top",
                    at: "center top"
                }
            }, "warning", 3000);

            return false
        }

        const author = new Author()
        if (this.dxAutocomplete.selectedItem) {
            // @todo improve this with maybe decomposition or anything else
            author.id = this.dxAutocomplete.selectedItem.id
            author.firstname = this.dxAutocomplete.selectedItem.firstname
            author.lastname = this.dxAutocomplete.selectedItem.lastname
        } else {
            author.firstname = authorsForm.formData.firstname
            author.lastname = authorsForm.formData.lastname
        }

        const job = new Job()
        job.id = this.dxSelectBox.value.id
        job.translationKey = this.dxSelectBox.value.translationKey

        const authors = new Authors()
        authors.author = author
        authors.role = job

        this.authors.push(authors)

        // reset the forms
        this.dxAutocomplete.instance.reset()
        this.dxSelectBox.instance.reset()
        authorsForm.instance.resetValues()

        // save in cache when user refresh or come back later
        localStorage.setItem(CacheKey.AUTHORS, JSON.stringify(this.authors))

        // notify the user
        notify({
            message: `The author has been saved locally`,
            position: {
                my: "center top",
                at: "center top"
            }
        }, "success", 3000);
    }

    /**
     * @param author
     */
    removeAuthor(authorToRemove) {
        this.authors = this.authors.filter(author => author !== authorToRemove)

        // save in cache when user refresh or come back later
        localStorage.setItem(CacheKey.AUTHORS, JSON.stringify(this.authors))
    }
}
