# Add a validation

We want to add the option to configure list of authors from which the tweets are allowed. To achieve this, we have to:
- implement `validateValidatorConfiguration()` and `validate()` methods in the Type class
- implement the FormMapper
- add field definition edit view
- implement `toStorageFieldDefinition()` and `toFieldDefinition()` methods in LegacyConverter

## Implement `validateValidatorConfiguration()` and `validate()` methods in the Type class

`validateValidatorConfiguration()` will be called when an instance of the Field Type is added to a Content Type, to ensure that the validator configuration is valid.

For the validator schema configuration, you can add:

``` php
// eZ/Publish/FieldType/Tweet/Type.php

protected $validatorConfigurationSchema = [
    'TweetValueValidator' => [
        'authorList' => [
            'type' => 'array',
            'default' => []
        ]
    ]
];
```

For a TextLine (length validation), it means checking that both min length and max length are positive integers, and that min is lower than max.

When an instance of the type is added to a Content Type, `validateValidatorConfiguration()` receives the configuration for the validators used by the Type as an array. It must return an array of error messages if errors are found in the configuration, and an empty array if no errors were found.

For TextLine, the provided array looks like this:

``` php
// eZ/Publish/FieldType/Tweet/Type.php

array(
   'StringLengthValidator' => array(
       'minStringLength' => 0,
       'maxStringLength' => 100
   )
);
```

The structure of this array is totally free, and up to each type implementation. In this tutorial it will mimic what is done in native Field Types:

Each level one key is the name of a validator, as acknowledged by the Type. That key contains a set of parameter name / parameter value rows. You must check that:

- all the validators in this array are known to the type
- arguments for those validators are valid and have sane values

You do not need to include mandatory validators if they don’t have options. Here is an example of what your Type expects as validation configuration:

``` php
[
    'TweetValueValidator' => [
        'authorList' => ['johndoe', 'janedoe']
    ]
];
```

The configuration says that tweets must be either by `johndoe` or by `janedoe`. If you had not provided `TweetValueValidator` at all, it would have been ignored.

You will iterate over the items in `$validatorConfiguration` and:

- add errors for those you don’t know about;
- check that provided arguments are known and valid:
  - `TweetValueValidator` accepts a non-empty array of valid Twitter usernames

``` php
// eZ/Publish/FieldType/Tweet/Type.php

public function validateValidatorConfiguration($validatorConfiguration)
{
    $validationErrors = [];

    foreach ($validatorConfiguration as $validatorIdentifier => $constraints) {
        // Report unknown validators
        if ($validatorIdentifier !== 'TweetValueValidator') {
            $validationErrors[] = new ValidationError("Validator '$validatorIdentifier' is unknown");
            continue;
        }

        // Validate arguments from TweetValueValidator
        foreach ($constraints as $name => $value) {
            switch ($name) {
                case 'authorList':
                    if (!is_array($value)) {
                        $validationErrors[] = new ValidationError('Invalid authorList argument');
                    }
                    foreach ($value as $authorName) {
                        if (!preg_match('/^[a-z0-9_]{1,15}$/i', $authorName)) {
                            $validationErrors[] = new ValidationError('Invalid twitter username');
                        }
                    }
                    break;
                default:
                    $validationErrors[] = new ValidationError("Validator parameter '$name' is unknown");
            }
        }
    }

    return $validationErrors;
}
```

`validate()` is the method that runs the actual validation on data:

``` php
// eZ/Publish/FieldType/Tweet/Type.php

public function validate(FieldDefinition $fieldDefinition, SPIValue $fieldValue)
{
    $errors = [];

    if ($this->isEmptyValue($fieldValue)) {
        return $errors;
    }

    // Tweet Url validation
    if (!preg_match('#^https?://twitter.com/([^/]+)/status/[0-9]+$#', $fieldValue->url, $m)) {
        $errors[] = new ValidationError(
            'Invalid twitter status url %url%',
            null,
            ['%url%' => $fieldValue->url]
        );

        return $errors;
    }

    $author = $m[1];
    $validatorConfiguration = $fieldDefinition->getValidatorConfiguration();
    if (!$this->isAuthorApproved($author, $validatorConfiguration)) {
        $errors[] = new ValidationError(
            'Twitter user %user% is not in the approved author list',
            null,
            ['%user%' => $m[1]]
        );
    }

    return $errors;
}

private function isAuthorApproved($author, $validatorConfiguration)
{
    return !isset($validatorConfiguration['TweetValueValidator'])
        || empty($validatorConfiguration['TweetValueValidator']['authorList'])
        || in_array($author, $validatorConfiguration['TweetValueValidator']['authorList']);
}
```

In addition to validating the URL with a regular expression, which we implemented before, if your Field Type instance’s configuration contains a `TweetValueValidator` key, you will check that the username in the status URL matches one of the valid authors.

## Implement FormMapper

Next, we need to create a way for user to input a list of authors for our validation. To achieve this, we need to implement a FormMapper that allows us to add an input field to form with content field definition.
The most basic one looks like the following:

```php
// eZ/Publish/FieldType/Tweet/FormMapper.php

class FormMapper implements FieldDefinitionFormMapperInterface
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $fieldDefinitionForm
            ->add('authorList', TextType::class, [
                'required' => false,
                'property_path' => 'validatorConfiguration[TweetValueValidator][authorList]',
                'label' => 'field_definition.eztweet.authorList'
            ]);
    }
}
```

Unfortunately, because in our case authorList validator has array type, we cannot leave it like that. We will need a way to transform data from array to comma-separated string and vice versa.
To achieve this, first we need to implement a DataTransformer:

```php
// Form/StringToArrayTransformer.php

class StringToArrayTransformer implements DataTransformerInterface
{
    public function transform($array)
    {
        if ($array === null) {
            return '';
        }

        return implode(',', $array);
    }

    public function reverseTransform($string)
    {
        if (empty($string)) {
            return [];
        }

        $array = explode(',', $string);

        return array_map('trim', $array);
    }
}
```

Then, we can use this DataTransformer in our FormMapper like this:
```php
// eZ/Publish/FieldType/Tweet/FormMapper.php

class FormMapper implements FieldDefinitionFormMapperInterface
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $fieldDefinitionForm
            ->add(
                // Creating from FormBuilder as we need to add a DataTransformer.
                $fieldDefinitionForm->getConfig()->getFormFactory()->createBuilder()
                    ->create('authorList', TextType::class, [
                        'required' => false,
                        'property_path' => 'validatorConfiguration[TweetValueValidator][authorList]',
                        'label' => 'field_definition.eztweet.authorList'
                    ])
                    ->addModelTransformer(new StringToArrayTransformer())
                    // Deactivate auto-initialize as we're not on the root form.
                    ->setAutoInitialize(false)->getForm()
            );
    }
}
```

Next thing is to register the FormMapper as a service, so the system would know to use it. To achieve this, let's add the following lines to `fieldtypes.yml`:
```yml
// Resources/config/fieldtypes.yml

    ezsystems.tweetbundle.fieldtype.eztweet.form_mapper:
        class: EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet\FormMapper
        tags:
            - {name: ez.fieldFormMapper.definition, fieldType: eztweet}
```

## Add field definition edit view

We have the new part of the form defined, but we still need to show it to the user. To do that, we need to create file containing the view:
```html
// Resources/views/platformui/content_type/edit/eztweet.html.twig

{% block eztweet_field_definition_edit %}
    <div class="eztweet-validator author_list{% if group_class is not empty %} {{ group_class }}{% endif %}">
        {{- form_label(form.authorList) -}}
        {{- form_errors(form.authorList) -}}
        {{- form_widget(form.authorList) -}}
    </div>
{% endblock %}
```

Also, we need to register it by editing the file `ez_field_templates.yml`:
```yml
// Resources/config/ez_field_templates.yml

        fielddefinition_edit_templates:
            - {template: EzSystemsTweetFieldTypeBundle:platformui/content_type/edit:eztweet.html.twig, priority: 0}
```

## Implement `toStorageFieldDefinition()` and `toFieldDefinition()` methods in LegacyConverter

The last thing to do is to make sure that validation data is properly saved into and retrieved from the database. To achieve this, we need to implement these two functions in LegacyConverter file:
```php
// eZ/Publish/FieldType/Tweet/LegacyConverter.php

public function toStorageFieldDefinition(FieldDefinition $fieldDef, StorageFieldDefinition $storageDef)
{
    $storageDef->dataText1 = json_encode(
        $fieldDef->fieldTypeConstraints->validators['TweetValueValidator']['authorList']
    );
}

public function toFieldDefinition(StorageFieldDefinition $storageDef, FieldDefinition $fieldDef)
{
    $authorList = json_decode($storageDef->dataText1);
    if (!empty($authorList)) {
        $fieldDef->fieldTypeConstraints->validators = [
            'TweetValueValidator' => [
                'authorList' => $authorList
            ],
        ];
    }
}
```

`toStorageFieldDefinition()` converts a field definition to a legacy one using the `dataText1` field (which in this example means converting it to json), and `toFieldDefinition()` converts a stored legacy field definition to an API field definition (which means converting it back to an array according to validation schema).

You should now be able to configure list of authors when editing Content Type with Tweet Field Type.
You should also have your tweets validated in accordance to this list when you create or edit Content Item with this Field Type. 

⬅ Previous: [Introduce a template](introduce_a_template.md)
