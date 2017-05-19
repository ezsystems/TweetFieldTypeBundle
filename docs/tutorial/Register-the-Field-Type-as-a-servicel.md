1.  <span>[Developer](index)</span>
2.  <span>[Creating a Tweet Field Type](Creating-a-Tweet-Field-Type)</span>

<span id="title-text"> Developer : Register the Field Type as a service </span>
===============================================================================



To complete the implementation, you must register your Field Type with Symfony by creating a service for it.

Services are by default declared by bundles in `Resources/config/services.yml`.

Using a dedicated file for the Field Type services

<span class="aui-icon aui-icon-small aui-iconfont-warning confluence-information-macro-icon"></span>
In order to be closer to the kernel best practices, you could declare your Field Type services in a custom `fieldtypes.yml` file.

All you have to do is instruct the bundle to actually load this file in addition to `services.yml` (or instead of `services.yml`!). This is done in the extension definition file, `         DependencyInjection/EzSystemsTweetFieldTypeExtension.php`, in the `         load()` method.

Inside this file, find this line:

``` brush:
$loader->load('services.yml');
```

This is where your bundle tells Symfony that when parameters are loaded, `services.yml` should be loaded from `Resources/config/` (defined above). Either replace the line, or add a new one with:

``` brush:
$loader->load('fieldtypes.yml');
```

Like most API components, Field Types use the <a href="http://symfony.com/doc/current/components/dependency_injection/tags.html" class="external-link">Symfony 2 service tag mechanism</a>.

The principle is quite simple: a service can be assigned one or several tags, with specific parameters. When the dependency injection container is compiled into a PHP file, tags are read by `CompilerPass` implementations that add extra handling for tagged services. Each service tagged as `       ezpublish.fieldType` is added to a <a href="http://martinfowler.com/eaaCatalog/registry.html" class="external-link">registry</a> using the alias argument as its unique identifier (`ezstring`, `ezxmltext`, etc.). Each Field Type must also inherit from the abstract `ezpublish.fieldType` service. This ensures that the initialization steps shared by all Field Types are executed.

Here is the service definition for your Tweet type:

**Resources/config/services.yml**

``` brush:
services:
    ezsystems.tweetbundle.twitter.client:
    class: EzSystems\TweetFieldTypeBundle\Twitter\TwitterClient
```

You take care of namespacing your Field Type with your vendor and bundle name to limit the risk of naming conflicts.

And you can create a YAML file dedicated to the Bundle

**Resources/config/fieldtypes.yml**

``` brush:
services:
    ezsystems.tweetbundle.fieldtype.eztweet:
        parent: ezpublish.fieldType
        class: EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet\Type
        tags:
            - {name: ezpublish.fieldType, alias: eztweet}
        arguments: ['@ezsystems.tweetbundle.twitter.client']
```

 

 

------------------------------------------------------------------------

 

 <span class="char" title="Leftwards Black Arrow">⬅</span> Previous: [Implement the Tweet\\Type class](Implement-the-Tweet-Type-class)

Next: <span class="confluence-link" style="text-align: right;" title="Black Rightwards Arrow">[Implement the Legacy Storage Engine Converter](Implement-the-Legacy-Storage-Engine-Converter) ➡</span>




