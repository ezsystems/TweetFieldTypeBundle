# Introduce a template

In order to display data of your Field Type from templates, you need to create and register a template for it. You can find documentation about [FieldType templates](https://doc.ez.no/display/DEVELOPER/Field+Type+template), as well as on [importing settings from a bundle](https://doc.ez.no/display/DEVELOPER/Importing+settings+from+a+bundle).

In short, such a template must:

- extend `EzPublishCoreBundle::content_fields.html.twig`
- define a dedicated Twig block for the type, named by convention `<TypeIdentifier_field>`, in this case, `eztweet_field`
- be registered in parameters

## The template:` Resources/views/fields/eztweet.html.twig`

The first thing to do is create the template. It will basically define the default display of a tweet. Remember that [field type templates can be overridden](https://confluence.ez.no/display/DEVELOPER/ez_render_field#ez_render_field-Overrideafieldtemplateblock) in order to tweak what is displayed and how.

Each Field Type template receives a set of variables that can be used to achieve the desired goal. The variable you care about is `field`, an instance of `eZ\Publish\API\Repository\Values\Content\Field`. In addition to its own metadata (`id`, `fieldDefIdentifier`, etc.), it exposes the Field Value (`Tweet\Value`) through the `value` property.

This would work as a primitive template:  

``` html
{# TweetFieldTypeBundle/Resources/views/fields/eztweet.html.twig #}

{% extends "EzPublishCoreBundle::content_fields.html.twig" %}

{% block eztweet_field %}
{% spaceless %}
    {{ field.value.contents|raw }}
{% endspaceless %}
{% endblock %}
```

`field.value.contents` is piped through the `raw` twig operator, since the variable contains HTML code. Without it, the HTML markup would be visible directly, since twig escapes variables by default. Notice that the code is nested within a `spaceless` tag, so that you can format the template in a readable manner without jeopardizing the display with unwanted spaces.

### Using the content field helpers

Even though the above will work just fine, a few helpers will enable you to get something a bit more flexible. The <a href="https://github.com/ezsystems/ezpublish-kernel/blob/master/eZ/Bundle/EzPublishCoreBundle/Resources/views/content_fields.html.twig">EzPublishCoreBundle::content_fields.html.twig</a> template, where the native Field Type templates are implemented, provides a few helpers: `simple_block_field`, `simple_inline_field` and `field_attributes`. The first two are used to display a field either as a block or inline. `field_attributes` makes it easier to use the `attr` variable that contains additional (HTML) attributes for the field.

Let's try to display the value as a block element.

First, you need to make the template inherit from `content_fields.html.twig`. Then, create a `field_value` variable that will be used by the helper to print out the content inside the markup. And that's it. The helper will use `field_attributes` to add the HTML attributes to the generated `div`.

``` html
{# TweetFieldTypeBundle/Resources/views/fields/eztweet.html.twig #}

{% extends "EzPublishCoreBundle::content_fields.html.twig" %}

{% block eztweet_field %}
{% spaceless %}
    {% set field_value %}
        {{ field.value.contents|raw }}
    {% endset %}
    {{ block( 'simple_block_field' ) }}
{% endspaceless %}
{% endblock %}
```

`fieldValue` is set to the markup you had above, using a `{% set %}` block. You then call the `block` function to process the `simple_block_field` template block.

## Registering the template

As explained in the [FieldType template documentation](https://confluence.ez.no/display/DEVELOPER/Field+Type+template#FieldTypetemplate-Registeringyourtemplate), a Field Type template needs to be registered in the eZ Platform semantic configuration. The most basic way to do this would be to do so in `app/config/ezplatform.yml`:

``` yml
# app/config/ezplatform.yml

ezpublish:
    global:
        field_templates:
            - { template: "EzSystemsTweetFieldTypeBundle:fields:eztweet.html.twig"}
```

However, this is far from ideal. You want this to be part of our bundle, so that no manual configuration is required. For that to happen, you need to make the bundle extend the eZ Platform semantic configuration.

To do so, you are going to make your Bundle's dependency injection extension (`DependencyInjection/EzSystemsTweetFieldTypeExtension.php`) implement `Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface`. This interface will let you prepend bundle configuration:

``` php
// TweetFieldTypeBundle/DependencyInjection/EzSystemsTweetFieldTypeExtension.php

<?php
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Yaml\Yaml;

class EzSystemsTweetFieldTypeExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container)
    {
        $config = Yaml::parse(file_get_contents(__DIR__.'/../Resources/config/ez_field_templates.yml'));
        $container->prependExtensionConfig('ezpublish', $config);
    }
}
```

The last thing to do is move the template mapping from `app/config/ezplatform.yml` to `Resources/config/ezpublish_field_templates.yml`:

``` yml
# Resources/config/ezpublish\_field\_templates.yml

system:
    default:
        field_templates:
            - {template: EzSystemsTweetFieldTypeBundle:fields:eztweet.html.twig, priority: 0}
```

Notice that the `ezpublish` yaml block was deleted. This is because you already import your configuration under the `ezpublish` namespace in the `prepend` method.

You should now be able to display a Content item with this Field Type from the front office, with a fully functional embed.

------------------------------------------------------------------------

⬅ Previous: [Implement the Legacy Storage Engine Converter](implement_the_legacy_storage_engine_converter.md)

Next: [Add content and edit views](add_content_and_edit_views.md) ➡
