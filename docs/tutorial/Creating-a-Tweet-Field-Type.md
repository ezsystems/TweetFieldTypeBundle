1.  <span>[Developer](index)</span>

<span id="title-text"> Developer : Creating a Tweet Field Type </span>
======================================================================



Getting the code

<span class="aui-icon aui-icon-small aui-iconfont-approve confluence-information-macro-icon"></span>
The code created in this tutorial is available on GitHub: <a href="https://github.com/ezsystems/TweetFieldTypeBundle" class="uri" class="external-link">https://github.com/ezsystems/TweetFieldTypeBundle</a>.

This tutorial covers the creation and development of a custom eZ Platform [Field Type](https://doc.ez.no/display/DEVELOPER/Field+Types+reference).
Field Types are the smallest building blocks of content. eZ Platform comes with about [30 native types](https://doc.ez.no/display/DEVELOPER/Field+Types+reference) that cover most common needs (Text line, Rich text, Email, Author list, Content relation, Map location, Float, etc.)

Field Types are responsible for:

-   Storing data, either using the native storage engine mechanisms or specific means

-   Validating input data

-   Making the data searchable (if applicable)

-   Displaying Fields of this type

Custom Field Types are a very powerful type of extension, since they allow you to hook deep into the content model.

You can find the in-depth [documentation about Field Types and their best practices here](https://doc.ez.no/display/DEVELOPER/Field+Type+API+and+best+practices). It describes how each component of a Field Type interacts with the various layers of the system, and how to implement those.

Intended audience
-----------------

This tutorial is aimed at developers who are familiar with eZ Platform and are comfortable with operating in PHP and Symfony2.

Content of the tutorial
-----------------------

This tutorial will demonstrate how to create a Field Type on the example of a *Tweet* Field Type. It will:

-   Accept as input the URL of a tweet (https://twitter.com/&lt;username&gt;/status/&lt;id&gt;)

-   Fetch the tweet using the Twitter oEmbed API (<a href="https://dev.twitter.com/docs/embedded-tweets" class="uri" class="external-link">https://dev.twitter.com/docs/embedded-tweets</a>)

-   Store the tweet’s embed contents and URL

-   Display the tweet's embedded version when displaying the field from a template

 

<span class="confluence-embedded-file-wrapper image-center-wrapper"><img src="attachments/31429766/31429765.png" class="confluence-embedded-image image-center" /></span>

Preparation
-----------

To start the tutorial, you need to make a clean eZ Platform installation. Follow the guide for your system from [Step 1: Installation](https://doc.ez.no/display/DEVELOPER/Step+1%3A+Installation). Remember to install using the `dev` environment.

 

[Start the tutorial](Build-the-bundle)
----------------------------------------------------

Steps
-----

The tutorial will lead you through the following steps:

#### 1. The bundle

Field Types, like any other eZ Platform plugin, must be provided as Symfony2 bundles. This chapter covers the creation and organization of this bundle.
Read more about [creating](Create-the-bundle) and [structuring the bundle](Structure-the-bundle).

#### 2. API

This part covers the implementation of the eZ Platform API elements required to implement a custom Field Type.
Read more about [implementing the Tweet\\Value class](Implement-the-Tweet-Value-class) and [the Tweet\\Type class](Implement-the-Tweet-Type-class).

#### 3. Converter

Storing data from any Field Type in the Legacy Storage Engine requires that your custom data is mapped to the data model.
Read more about [implementing the Legacy Storage Engine Converter](Implement-the-Legacy-Storage-Engine-Converter).

#### 4. Templating

Displaying a Field Type's data is done through a <a href="http://twig.sensiolabs.org/doc/intro.html" class="external-link">Twig template</a>.
Read more about [implementing the Field Type template](Introduce-a-template).

#### 5. PlatformUI integration

Viewing and editing values of the Field Type in PlatformUI requires that you extend PlatformUI, using mostly JavaScript.

You should ideally read the general [extensibility documentation for PlatformUI](https://doc.ez.no/display/DEVELOPER/Extending+eZ+Platform+UI). You can find information about view templates [in the next tutorial](https://doc.ez.no/display/DEVELOPER/Define+a+View). Edit templates are not documented at the time of writing, but <a href="http://www.netgenlabs.com/" class="external-link">Netgen</a> <span> has published a tutorial that </span>covers the topic: <a href="http://www.netgenlabs.com/Blog/Adding-support-for-a-new-field-type-to-eZ-Publish-Platform-UI" class="uri" class="external-link">http://www.netgenlabs.com/Blog/Adding-support-for-a-new-field-type-to-eZ-Publish-Platform-UI</a>.

 

[Start the tutorial](Build-the-bundle)
----------------------------------------------------

------------------------------------------------------------------------

 

 

[Start the tutorial](Build-the-bundle) <span class="confluence-link" title="Black Rightwards Arrow">➡</span>


 

 

Attachments:
------------

<img src="images/icons/bullet_blue.gif" width="8" height="8" /> [fieldtype tutorial, final result.PNG](attachments/31429766/31429765.png) (image/png)




