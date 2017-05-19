1.  <span>[Developer](index)</span>
2.  <span>[Creating a Tweet Field Type](Creating-a-Tweet-Field-Type)</span>
3.  <span>[Build the bundle](Build-the-bundle)</span>

<span id="title-text"> Developer : Structure the bundle </span>
===============================================================



At this point, you have a basic application-specific Symfony 2 bundle. **** Let’s start by creating the structure for your Field Type.

To make it easier to move around the code, you will to some extent mimic the structure that is used in the kernel of eZ Platform. Native Field Types are located inside `ezpublish-kernel` (in `vendor/ezsystems`), in the `      eZ/Publish/Core/FieldType` folder.
Each Field Type has its own subfolder: `      TextLine`, `      Email`, `      Url`, etc.

<span class="aui-icon aui-icon-small aui-iconfont-info confluence-information-macro-icon"></span>
Clone the Github repository to follow this tutorial, it will be useful: <a href="https://github.com/ezsystems/TweetFieldTypeBundle" class="uri" class="external-link">https://github.com/ezsystems/TweetFieldTypeBundle</a>

You will use a structure quite close to this.

<span class="confluence-embedded-file-wrapper image-center-wrapper confluence-embedded-manual-size"><img src="attachments/31429784/34080072.png?effects=border-simple,blur-border" class="confluence-embedded-image image-center" height="250" /></span>

 

From the tutorial git repository, list the contents of the `      eZ/Publish/FieldType` folder:

     eZ
     └── Publish
        └── FieldType
            └── Tweet
                ├── Type.php
                └── Value.php

A Field Type requires two base classes: `      Type    ` and `      Value`.

### The Type class

The Type contains the logic of the Field Type: validating data, transforming from various formats, describing the validators, etc.
A Type class must implement `      eZ\Publish\SPI\FieldType\FieldType`. It may also extend the `      eZ\Publish\Core\FieldType\FieldType` abstract class.

### The Value class

The Value is used to represent an instance of our type within a Content item. Each Field will present its data using an instance of the Type’s Value class.
A value class must implement the `      eZ\Publish\SPI\FieldType\Value` ` ` interface. It may also extend the `      eZ\Publish\Core\FieldType\Value` abstract class.

 

------------------------------------------------------------------------

 

 <span class="char" title="Leftwards Black Arrow">⬅</span> Previous: [Create the bundle](Create-the-bundle)

Next: <span class="confluence-link" title="Black Rightwards Arrow"> [Implement the Tweet\\Value class](Implement-the-Tweet-Value-class) ➡</span>

**Tutorial path**

Attachments:
------------

<img src="images/icons/bullet_blue.gif" width="8" height="8" /> [TweetFieldTypeBundle\_eZ\_Publish\_FieldType\_Tweet\_at\_master\_·\_ezsystems\_TweetFieldTypeBundle\_-\_2017-04-12\_14.47.11.png](attachments/31429784/34080072.png) (image/png)


