<?php
namespace App\Tests\Common;

class JsonBook
{
    /**
     * @var string allow to test a correct HTTP Post with the ability of the ParamConverter to de-duplicate entity like for editor in this sample
     */
    static public $bodyOk = <<<JSON
{
    "book": {
        "title": "Zombies in western culture",
        "editors": [{
            "publicationDate": "1519664915",
            "collection": "printed version",
            "isbn": "9781783743230",
            "editor": {
                "name": "Open Book Publishers"
            }
        }, {
            "publicationDate": "1519747464",
            "collection": "ebooks",
            "isbn": "9791036500824",
            "editor": {
                "name": "Open Book Publishers"
            }
        }],
        "authors": [{
            "role": {
                "translationKey": "WRITER"
            },
            "author": {
                "firstname": "Marc",
                "lastname": "O'Brien"
            }
        }, {
            "role": {
                "translationKey": "WRITER"
            },
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
JSON;

    /**
     * Denormalizer doesn't work like my ParamConverter, so the json root is a little bit different: here we don't need the root book node !
     * And so we have to clean it
     *
     * @var string allow to test a correct HTTP Post with the ability of the ParamConverter to de-duplicate entity like for editor in this sample
     */
    static public $bodyOkForDenormalizer = <<<JSON
{
    "title": "Zombies in western culture",
    "editors": [{
        "publicationDate": "02/26/18 18:08:35",
        "collection": "printed version",
        "isbn": "9781783743230",
        "editor": {
            "name": "Open Book Publishers"
        }
    }, {
        "publicationDate": "02/27/18 17:04:24",
        "collection": "ebooks",
        "isbn": "9791036500824",
        "editor": {
            "name": "Open Book Publishers"
        }
    }],
    "authors": [{
        "role": {
            "translationKey": "WRITER"
        },
        "author": {
            "firstname": "Marc",
            "lastname": "O'Brien"
        }
    }, {
        "role": {
            "translationKey": "WRITER"
        },
        "author": {
            "firstname": "Paul",
            "lastname": "Kyprianou"
        }
    }],
    "serie": {
        "name": "Open Reports Series"
    }
}
JSON;

    /**
     * @var string to test that the ParamConverter are abled to reuse entity from database
     */
    static public $bodyOkWithExistingEntities = <<<JSON
{
    "book": {
        "title": "Oh my god, how simple it is !",
        "editors": [{
            "publicationDate": "1519664915",
            "collection": "from my head",
            "isbn": "9781783742530",
            "editor": 1
        }, {
            "publicationDate": "1519747464",
            "collection": "ebooks",
            "isbn": "9782821883963",
            "editor": {
                "name": "Open Book Publishers"
            }
        }],
        "authors": [{
            "role": 2,
            "author": 3
        }, {
            "role": {
                "translationKey": "WRITER"
            },
            "author": {
                "firstname": "Paul",
                "lastname": "Kyprianou"
            }
        }],
        "serie": 4
    }
}
JSON;

    /**
     * @var string allow to test a failed HTTP Post with expected JSON content
     */
    static public $bodyNoEditor = <<<JSON
{
    "book": {
        "title": "Oh my god, how simple it is !",
        "editors": [{
            "publicationDate": "1519664915",
            "collection": "from my head",
            "isbn": "9781783742530",
            "editor": { }
        }],
        "serie": 4
    }
}
JSON;

}