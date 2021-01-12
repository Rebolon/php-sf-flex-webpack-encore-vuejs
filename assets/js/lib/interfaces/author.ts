export interface Author {
  '@id'?: string;
  readonly firstname: string;
  readonly lastname?: string;
  readonly books?: string[];
  readonly name?: string;
  id?: string;
}
