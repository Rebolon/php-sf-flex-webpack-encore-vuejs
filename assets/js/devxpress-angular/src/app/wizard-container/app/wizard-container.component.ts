import {Component, OnDestroy, OnInit} from '@angular/core'
import {WizardRouting} from "../../shared/services/wizard-routing";
import {ActivatedRoute, Router, Routes, UrlSegment} from "@angular/router";
import {wizardRoutes} from "../wizard.routes";
import {WizardBook} from "../../shared/services/wizard-book";
import {CacheKey} from "../enums/cache-key";
import {Book} from "../../../entities/library/book";
import {Subscription} from "rxjs/Subscription";
import {BookReviver} from "../../shared/services/reviver/library/bookReviver";

@Component({
    selector: 'my-wizard',
    templateUrl: './wizard-container.component.html',
    styleUrls: ['./wizard-container.component.scss']
})
// @todo may use of ActivatedRoute to prevent test in html to set Active class : routerLinkActive="active" in button
export class WizardContainerComponent implements OnInit, OnDestroy {
    basePath = ''
    currentStep: number

    // @todo find a better way to configure the routing in bot appModule and here in the component
    steps: Routes

    // pattern to unsubscribe everything, if you don't you risk memory leak !
    protected subscriptions: Array<Subscription> = []

    constructor(private bookService: WizardBook, private routingService: WizardRouting, private router: Router, private route: ActivatedRoute, private bookReviver: BookReviver) {
        this.steps = wizardRoutes.filter(route => route.path !== '**')
    }

    ngOnInit(): void {
        // manage the route prefix if we are embeded in another component
        if (this.route.snapshot.routeConfig.path) {
            this.basePath = this.route.snapshot.routeConfig.path + '/'
        }

        this.setCurrentStepFromRoute();

        this.manageStepsActivationAndRouting();

        this.manageCache()
    }

    /**
     * Unsubscribe from everything ye'all
     */
    ngOnDestroy() {
        this.subscriptions.forEach(subs => subs.unsubscribe())
    }

    /**
     * manage the first display
     */
    private setCurrentStepFromRoute() {
        let urlFinder = this.route.snapshot.url
        if (this.basePath) {
            urlFinder = this.route.snapshot.firstChild.url
        }
        urlFinder.forEach((urlSegment: UrlSegment, idx) => {
            let index = 0
            this.steps.forEach((route, idx) => {
                if (route.path === urlSegment.path) {
                    index = idx
                }
            })

            this.currentStep = index
        })
    }

    /**
     * manage all 4 navigations behaviors & currentStep changes
     */
    private manageStepsActivationAndRouting() {
        this.subscriptions.push(
            this.routingService.cancel.subscribe((_) => {
                const route = '/demo/devxpress-angular'
                this.router.navigate([route])
            })
        )

        this.subscriptions.push(
            this.routingService.end.subscribe((_) => {
                // @todo save or display a toast and return to main page

                const route = '/demo/devxpress-angular'
                this.router.navigate([route])
            })
        )

        this.subscriptions.push(
            this.routingService.back.subscribe((_) => {
                if (this.currentStep === 0) {
                    return
                }

                this.currentStep--
                // @todo how to grab wizard parent name ?
                const route = this.basePath + this.steps[this.currentStep].path
                this.router.navigate([route])
            })
        )

        this.subscriptions.push(
            this.routingService.next.subscribe((_) => {
                if (this.currentStep === this.steps.length) {
                    return
                }

                this.currentStep++
                const route = this.basePath + this.steps[this.currentStep].path
                this.router.navigate([route])
            })
        )
    }

    // Manage cache state restore
    private restoreCache() {
        let objToRestore = new Book()
        const cache = localStorage.getItem(CacheKey.BOOK)
        if (cache) {
            objToRestore = this.bookReviver.main(cache)
        }

        this.bookService.createBook(objToRestore)
    }

    private manageCache() {
        this.restoreCache()

        // save in cache when user refresh or come back later
        this.subscriptions.push(
            this.bookService.book.subscribe(book => {
                localStorage.setItem(CacheKey.BOOK, JSON.stringify(book))
            })
        )
    }
}
