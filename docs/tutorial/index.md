# Creating a Tweet Field Type

### Getting the code

The code created in this tutorial is available on GitHub: (https://github.com/ezsystems/TweetFieldTypeBundle).

This tutorial covers the creation and development of a custom eZ Platform [Field Type](https://doc.ez.no/display/DEVELOPER/Field+Types+reference).
Field Types are the smallest building blocks of content. eZ Platform comes with about [30 native types](https://doc.ez.no/display/DEVELOPER/Field+Types+reference) that cover most common needs (Text line, Rich text, Email, Author list, Content relation, Map location, Float, etc.)

Field Types are responsible for:

- Storing data, either using the native storage engine mechanisms or specific means
- Validating input data
- Making the data searchable (if applicable)
- Displaying Fields of this type

Custom Field Types are a very powerful type of extension, since they allow you to hook deep into the content model.

You can find the in-depth [documentation about Field Types and their best practices here](https://doc.ez.no/display/DEVELOPER/Field+Type+API+and+best+practices). It describes how each component of a Field Type interacts with the various layers of the system, and how to implement those.

## Intended audience

This tutorial is aimed at developers who are familiar with eZ Platform and are comfortable with operating in PHP and Symfony2.

## Content of the tutorial

This tutorial will demonstrate how to create a Field Type on the example of a *Tweet* Field Type. It will:

- Accept as input the URL of a tweet (https://twitter.com/{username}/status/{id})
- Fetch the tweet using the Twitter oEmbed API (https://dev.twitter.com/docs/embedded-tweets)
- Store the tweet’s embed contents and URL
- Display the tweet's embedded version when displaying the field from a template

## Preparation

To start the tutorial, you need to make a clean eZ Platform installation. Follow the guide for your system from [Step 1: Installation](https://doc.ez.no/display/DEVELOPER/Step+1%3A+Installation). Remember to install using the `dev` environment.

## Steps

The tutorial will lead you through the following steps:

#### 1. The bundle

Field Types, like any other eZ Platform plugin, must be provided as Symfony2 bundles. This chapter covers the creation and organization of this bundle.
Read more about [creating](create_the_bundle.md) and [structuring the bundle](structure_the_bundle.md).

#### 2. API

This part covers the implementation of the eZ Platform API elements required to implement a custom Field Type.
Read more about [implementing the Tweet\\Value class](implement_the_tweet_value_class.md) and [the Tweet\\Type class](implement_the_tweet_type_class.md).

#### 3. Converter

Storing data from any Field Type in the Legacy Storage Engine requires that your custom data is mapped to the data model.
Read more about [implementing the Legacy Storage Engine Converter](implement_the_legacy_storage_engine_converter.md).

#### 4. Templating

Displaying a Field Type's data is done through a [Twig template](http://twig.sensiolabs.org/doc/intro.html).
Read more about [implementing the Field Type template](introduce_a_template.md).

#### 5. PlatformUI integration

Viewing and editing values of the Field Type in PlatformUI requires that you extend PlatformUI, using mostly JavaScript.

You should ideally read the general [extensibility documentation for PlatformUI](https://doc.ez.no/display/DEVELOPER/Extending+eZ+Platform+UI). You can find information about view templates [in the next tutorial](https://doc.ez.no/display/DEVELOPER/Define+a+View). Edit templates are not documented at the time of writing, but [Netgen](http://www.netgenlabs.com/) has published a tutorial that covers the topic: (http://www.netgenlabs.com/Blog/Adding-support-for-a-new-field-type-to-eZ-Publish-Platform-UI).

