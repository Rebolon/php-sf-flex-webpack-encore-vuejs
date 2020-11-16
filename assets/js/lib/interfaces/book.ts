export interface Book {
  '@id'?: string;
  readonly title: string;
  readonly description?: string;
  readonly indexInSerie?: number;
  readonly authors?: string[];
  readonly editors?: string[];
  readonly serie?: string;
  readonly tags?: string[];
  id?: string;
}
