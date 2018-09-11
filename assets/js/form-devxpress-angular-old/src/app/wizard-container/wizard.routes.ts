import {Routes} from "@angular/router";
import {BookFormComponent} from "./app/book-form/book-form.component";
import {EditorsFormComponent} from "./app/editors-form/editors-form.component";
import {AuthorsFormComponent} from "./app/authors-form/authors-form.component";

export const wizardRoutes: Routes = [
    {path: `book`, component: BookFormComponent},
    {path: `editors`, component: EditorsFormComponent},
    {path: `authors`, component: AuthorsFormComponent},
    {path: `**`, redirectTo: 'book'}
]
