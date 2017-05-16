# Field Type Tutorial

This repository contains the bundle that is created in the [Field Type Tutorial](https://doc.ezplatform.com/en/latest/tutorials/field_type/creating_a_tweet_field_type/).

## About the tutorial

This tutorial covers the creation and development of a custom eZ Platform Field Type on the example of a Tweet Field Type. The Field will:

- Accept as input the URL of a tweet (`https://twitter.com/<username>/status/<id>`)
- Fetch the tweet using the Twitter oEmbed API (https://developer.twitter.com/en/docs/tweets/post-and-engage/api-reference/get-statuses-oembed)
- Store the tweet’s embed contents and URL
- Display the tweet's embedded version when displaying the field from a template

## Contributing

Take a look at [CONTRIBUTING.md](CONTRIBUTING.md) to learn how to contribute to this tutorial.

## Tag stability warning
Code in this repository can and will be changed along with changes made to the tutorial, which also means that commit history can be rewritten and tags can be moved. This implies that you shouldn't rely on "git pull" for updating repository and instead should clone it again to avoid problems.
