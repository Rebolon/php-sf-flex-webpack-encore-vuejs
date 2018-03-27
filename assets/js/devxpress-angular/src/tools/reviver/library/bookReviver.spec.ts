import { TestBed, async } from '@angular/core/testing'
import { BookReviver } from './bookReviver'
import {AuthorsReviver} from "./authorsReviver"
import {EditorsReviver} from "./editorsReviver"
import {EditorReviver} from "./editorReviver"
import {JobReviver} from "./jobReviver"
import {SerieReviver} from "./serieReviver"
import {AuthorReviver} from "./authorReviver"

/**
 * @var string allow to test a correct HTTP Post with the ability of the ParamConverter to de-duplicate entity like for author in this sample
 */
const bodyOk = JSON.stringify(
{
        "book": {
            "title": "Zombies in western culture",
            "authors": [{
                "author": {
                    "firstname": "Marc",
                    "lastname": "O'Brien"
                }
            },{
                "author": {
                    "firstname": "Marc",
                    "lastname": "O'Brien"
                }
            }, {
                "author": {
                    "firstname": "Paul",
                    "lastname": "Kyprianou"
                }
            }],
            "serie": {
                "name": "Open Reports Series"
            }
        }
    }
)

/**
 * @var string to test that the ParamConverter are abled to reuse entity from database
 */
const bodyOkWithExistingEntities = JSON.stringify(
    {
        "book": {
            "title": "Oh my god, how simple it is !",
            "serie": 4
        }
    }
)

/**
 * @var string to test that the ParamConverter are abled to reuse entity from database
 */
const bodyOkWithExistingEntitiesWithFullProps = JSON.stringify(
    {
        "book": {
            "title": "Oh my god, how simple it is !",
            "serie": {
                "id": 4,
                "name": "whatever, it won't be read"
            }
        }
    }
)

/**
 * @var string allow to test a failed HTTP Post with expected JSON content
 */
const bodyNoAuthor = JSON.stringify(
    {
        "book": {
            "title": "Oh my god, how simple it is !",
            "authors": [{
                "author": { }
            }]
        }
    }
)

describe('BookReviver', () => {
  fit('should create all entities except for authors where there should be 2 instead of 3', () => {
      const content = JSON.parse(bodyOk)

      const bookConverter = getBookConverter().bookReviver

      const book: any = bookConverter.main(content.book)

      expect(book.title).toEqual(content.book.title)
      expect( book.serie.name).toEqual(content.book.serie.name)
      expect(book.authors.length).toEqual(3)

      expect(book.authors[0].author.firstname).toEqual(content.book.authors[0].author.firstname)
      expect(book.authors[0].author.lastname).toEqual(content.book.authors[0].author.lastname)

      expect(book.authors[2].author.firstname).toEqual(content.book.authors[2].author.firstname)
      expect(book.authors[2].author.lastname).toEqual(content.book.authors[2].author.lastname)

      // check that there is only 2 different Authors
      expect(book.authors[1]).toEqual(book.authors[0])
      expect(book.authors[2]).not.toEqual(book.authors[1])
  })
})

/**
 * @return BookConverter|void
 */
function getBookConverter()
{
    const authorReviver = new AuthorReviver()
    const editorReviver = new EditorReviver()
    const jobReviver = new JobReviver()
    const serieReviver = new SerieReviver()
    const editorsReviver = new EditorsReviver(editorReviver)
    const authorsReviver = new AuthorsReviver(jobReviver, authorReviver)

    const bookReviver = new BookReviver(authorsReviver, editorsReviver, serieReviver)

    return {
        bookReviver,
        authorReviver,
        editorReviver,
        jobReviver,
        serieReviver,
        editorsReviver,
        authorsReviver
    }
}
