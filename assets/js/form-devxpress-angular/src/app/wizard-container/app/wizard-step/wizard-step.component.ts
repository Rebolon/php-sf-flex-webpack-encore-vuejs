import {Component, Input} from '@angular/core'

@Component({
    selector: 'my-wizard-step',
    templateUrl: './wizard-step.component.html',
    styleUrls: ['./wizard-step.component.scss'],
})
export class WizardStepComponent {
    @Input() title: string = ''
    @Input() currentStep: number = 0
    @Input() steps: [{
        title: string
    }]

    getCurrentStep() {
        return this.steps[this.currentStep]
    }

    //@todo use in direct this.router.isActive( "/parent/p1/child/p1-c1" /*, true */);
    isActive(index: number) {
        return this.currentStep === index
    }

    isPassed(index: number) {
        return this.currentStep > index
    }

    canDisplay() {
        return typeof this.currentStep != 'undefined'
    }
}
