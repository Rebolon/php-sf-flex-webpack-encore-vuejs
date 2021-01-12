export interface Review {
  '@id'?: string;
  readonly rating: number;
  readonly body?: string;
  readonly username?: string;
  readonly publicationDate: Date;
  readonly book?: string;
  id?: string;
}
