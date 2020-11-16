export interface Loan {
  '@id'?: string;
  readonly book?: string;
  readonly borrower: string;
  readonly loaner: string;
  readonly startLoan: Date;
  readonly endLoan?: Date;
  id?: string;
}
