# Example module for Craft CMS 3.x

## Overview

The module either creates a new entry if one with a matching title doesn't exist or it updates an existing entry by matching its entry title. It creates a couple different types of matrix blocks in that entry as well. It matches what block it should map to by handle. 

## Example JSON payload

With a REST client of some sort, it uses a POST request with `X-API-Key: abc123` in the header passed to authorize the call. Valid keys live in the module model, though it could be put into the admin area too and thus be able to have an envvar house that data. 

This is an example of JSON data that can be passed. These all basically map to field handles in the entry, except for pageTitle which just goes to title (I don't know why - I just didn't change it as I was stubbing out the data for some reason). 

```
{
    "pageTitle": "Another Test Entry",
	"bodyText": "sssLorem ipsum dolor sit amet, consectetur adipiscing elit.",
    "status": "1",
    "blocks": [
        {
        "blockType": "anotherBlockType",
        "headline": "Another Headline",
        "showThis": "0"
        },
        {
        "blockType": "exampleBlock",
        "headline": "Some Headline",
        "bodyText": "Proin finibus non magna non tincidunt. "
        }
    ]
}
```
