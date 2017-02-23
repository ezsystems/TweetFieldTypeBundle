# Adding Symfony forms support to the FieldType

The [ezsystems/repository-forms][repoforms] package, shipped by default, has an API to connect
FieldTypes to Symfony forms, making the Field Definition and Value editable
using PHP and Twig. Field Definition forms are used when editing a Content Type in PlatformUI. Value forms
ar used when [editing Content][ugc_bdd].

The API allows you to define FieldType Mappers, as classes implementing the
`FieldDefinitionFormMapperInterface` and `FieldValueFormMapperInterface`. The Core FieldTypes mappers can be found in
the [`\EzSystems\RepositoryForms\FieldType\Mapper\TextLineFormMapper`][core_mappers] namespace.
Mappers services are tagged with the `ez.fieldFormMapper.definition` tag, that requires a `fieldType`
attribute with the FieldType's identifier.

[repoforms]: https://github.com/ezsystems/repository-forms
[ugc_bdd]: https://github.com/ezsystems/repository-forms/blob/v1.6.0/features/ContentEdit/create_without_draft.feature
[core_mappers]: https://github.com/ezsystems/repository-forms/tree/v1.6.0/lib/FieldType/Mapper

## FieldValue form
Before we add any code to it, edit the Type's service definition, and add the `ez.fieldFormMapper.definition`
to it:

```yaml
    ezsystems.tweetbundle.fieldType.eztweet:
        parent: ezpublish.fieldType
        class: %ezsystems.tweetbundle.fieldType.eztweet.class%
        tags:
            - {name: ezpublish.fieldType, alias: eztweet}
            - {name: ez.fieldFormMapper.definition, alias: eztweet}
        arguments: [@ezsystems.tweetbundle.twitter.client]
```

The Tweet FieldType's field definition exposes a "valid authors" setting. It stores a whitelist
of twitter users that can be stored.

First, let's define the FieldValueMapper. We could use a distinct file,
but we can also re-use our Type class. Edit `eZ/Publish/FieldType/Tweet/Type.php`, and change the
class definition so that it extends `\EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface`.

Then add the method from the interface to the class:

```php
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use Symfony\Component\Form\FormInterface;

class Type extends FieldType implements FieldValueFormMapperInterface
{
    //...
    
    public function mapFieldValueForm(FormInterface $form, FieldData $data)
    {
    }
}
```

The next step is to add the form fields required to edit the FieldType's content.
The only input value is the tweet's URL, as a string. Let's add a Text form widget
using the FormInterface's `add()` method:

```php
public function mapFieldValueForm(FormInterface $form, FieldData $data)
{
    $form->add(
        'value',
        TextType::class,
        [
            'required' => $data->fieldDefinition->isRequired,
            'label' => 'URL',
            'property_path' => 'value.url',
        ]
    );
}
```

`value` is the name required by the Content Forms API, and must therefore be used for FieldTypes
that only have one edit field. `TextType` is the forms package default text input field.

We also set a couple options on the form widget:
- `required` is mapped on the FieldDefinition's required property, read from the `$data` object.
- `label` is hardcoded, but should be mapped to the FieldDefinition's name in the current language.
- `property_path` is used to map the widget to the Field's Value property.
  It tells that the widget's text is to be read from/written to the `url` property of the `value`
  index of `$data` (`$data->value->url`).

> Mapping using `property_path` is required because `$data`, the object handled by the FormType,
> is not the Field's Value, but an aggregate of:
> - `value`, the field's Value object (`Tweet\Value`).
> - `fieldDefinition`, the `FieldDefinition` the Field is an instance of.
> - `field`, the API Field object.

### Handling complex forms
For cases when the Value has several properties we want to read from/to, a custom FormType is required.
MapLocation can be used as an example:

```php
// Mapper
public function mapFieldValueForm(FormInterface $form, FieldData $data)
{
    $form->add(
        'value',
        MapLocationType::class,
        ['required' => $data->fieldDefinition->isRequired]
    );
}
```

In the mapper, we add a _custom_ FormType, `MapLocationType`. The custom type will define
its own fields, and how they are mapped to the value's properties:

```php
// MapLocationType
class MapLocationType extends AbstractType
{
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezrepoforms_fieldtype_maplocation';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $commonOptions = ['required' => $options['required'] ?: false];
        $builder
            ->add(
                'latitude',
                NumberType::class,
                $commonOptions + ['label' => 'Latitude', 'scale' => 5, 'property_path' => 'latitude')
            ->add(
                'longitude',
                NumberType::class,
                $commonOptions + ['label' => 'Longitude', 'scale' => 5, 'property_path' => 'longitude'])
            ->add(
                'address',
                TextType::class,
                $commonOptions + ['label' => 'Address', 'property_path' => 'address']);
    }
}
```

The `required` option, passed by the Mapper from the FieldDefinition, is used to set each individual's
field's required status.
## FieldDefinition Form
When a Content Type Edit form is rendered, the mapper will be called, and will be given:
- a `FormInterace`, used to add fields to the form
- a `FieldDefinitionData`, used to read the Field Definition's settings and properties
