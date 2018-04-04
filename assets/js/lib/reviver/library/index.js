import {EditorReviver} from "./editorReviver";
import {AuthorReviver} from "./authorReviver";
import {JobReviver} from "./jobReviver";
import {SerieReviver} from "./serieReviver";
import {EditorsReviver} from "./editorsReviver";
import {AuthorsReviver} from "./authorsReviver";
import {BookReviver} from "./bookReviver";

const authorReviver = new AuthorReviver()
const editorReviver = new EditorReviver()
const jobReviver = new JobReviver()
const serieReviver = new SerieReviver()
const editorsReviver = new EditorsReviver(editorReviver)
const authorsReviver = new AuthorsReviver(jobReviver, authorReviver)

const bookReviver = new BookReviver(authorsReviver, editorsReviver, serieReviver)

export {
    bookReviver,
    authorReviver,
    editorReviver,
    jobReviver,
    serieReviver,
    editorsReviver,
    authorsReviver
}