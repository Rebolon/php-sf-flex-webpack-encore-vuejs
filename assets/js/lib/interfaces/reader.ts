export interface Reader {
  '@id'?: string;
  readonly lastname: string;
  readonly firstname?: string;
  readonly books?: string[];
  readonly loans?: string[];
  readonly borrows?: string[];
  id?: string;
}
