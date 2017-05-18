# Field Type Tutorial

This repository contains the bundle that is created in the [Field Type Tutorial](https://doc.ez.no/x/hpTfAQ).

## About the tutorial

This tutorial covers the creation and development of a custom eZ Platform Field Type on the example of a Tweet Field Type. The Field will:

- Accept as input the URL of a tweet (`https://twitter.com/<username>/status/<id>`)
- Fetch the tweet using the Twitter oEmbed API (https://dev.twitter.com/docs/embedded-tweets)
- Store the tweet’s embed contents and URL
- Display the tweet's embedded version when displaying the field from a template

## Contributing

Take a look at [CONTRIBUTING.md](CONTRIBUTING.md) to learn how to contribute to this tutorial.

## Tag stability warning
Code in this repository can and will be changed along with changes made to the tutorial, which also means that commit history can be rewritten and tags can be moved. This implies that you shouldn't rely on "git pull" for updating repository and instead should clone it again to avoid problems.

## CLI and REST helpers
The example Content Type with TweetFieldType can be created from CLI using the following command:
```bash
php app/console ezsystems:tweet-fieldtype:create-contenttype
```

The example Content Object can be created from CLI using the following command:
```bash
php app/console ezsystems:tweet-fieldtype:create-content
```

Alternatively, you can use REST API. Content Type creation example using curl (requires enabling basic auth based configuration: https://doc.ez.no/display/DEVELOPER/Getting+started+with+the+REST+API#GettingstartedwiththeRESTAPI-Authentication):
```bash
curl -u "admin:publish" -i -H "Accept: application/vnd.ez.api.ContentType+xml" \
-H "Content-Type: application/vnd.ez.api.ContentTypeCreate+xml" \
-X POST -d @src/EzSystems/TweetFieldTypeBundle/Resources/misc/create-content-type.xml \
http://ezplatform.dev/api/ezp/v2/content/typegroups/1/types?publish=true
```

Content Object creation using REST API requires changing Content Type ID in file src/EzSystems/TweetFieldTypeBundle/Resources/misc/create-content.xml to the one created in your installation. Then you can create Content Object, for example using curl (also requires enabling basic auth):
```bash
curl -u "admin:publish" -i -H "Accept: application/vnd.ez.api.Content+xml" \
-H "Content-Type: application/vnd.ez.api.ContentCreate+xml" \
-X POST -d @src/EzSystems/TweetFieldTypeBundle/Resources/misc/create-content.xml \
http://ezplatform.dev/api/ezp/v2/content/objects
```
