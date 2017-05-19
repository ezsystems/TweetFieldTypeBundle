1.  <span>[Developer](index)</span>
2.  <span>[Creating a Tweet Field Type](Creating-a-Tweet-Field-Type)</span>

<span id="title-text"> Developer : Implement the Tweet\\Type class </span>
==========================================================================



As said in the introduction, the Type class of a Field Type must implement `eZ\Publish\SPI\FieldType\FieldType` (later referred to as "Field Type interface").

All native Field Types also extend the `eZ\Publish\Core\FieldType\FieldType` abstract class that implements this interface and provides implementation facilities through a set of abstract methods of its own. In this case, Type classes implement a mix of methods from the Field Type interface and from the abstract Field Type.

Let’s go over those methods and their implementation.

### Identification method

#### `getFieldTypeIdentifier()`

This method must return the string that **uniquely** identifies this Field Type (DataTypeString in legacy), in this case "`eztweet`":

**eZ/Publish/FieldType/Tweet/Type.php**

``` brush:
public function getFieldTypeIdentifier()
{
   return 'eztweet';
}
```

### Value handling methods

#### `createValueFromInput()` and `checkValueStructure()`

Both methods are used by the abstract Field Type implementation of `acceptValue()`. This Field Type interface method checks and transforms various input values into the type's own Value class: `eZ\FieldType\Tweet\Value`. This method must:

-   either **return the Value object** it was able to create out of the input value,
-   or **return this value untouched**. The API will detect this and inform that the input value was not accepted.

The only acceptable value for your type is the URL of a tweet (you could of course imagine more possibilities). This should do:

``` brush:
protected function createValueFromInput( $inputValue )
{
   if ( is_string( $inputValue ) )
   {
       $inputValue = new Value( array( 'url' => $inputValue ) );
   }
 
   return $inputValue;
}
```

Use this method to provide convenient ways to set an attribute’s value using the API. This can be anything from primitives to complex business objects.

Next, implement `     checkValueStructure()`. It is called by the abstract Field Type to ensure that the Value fed to the Type is acceptable. In this case, you want to be sure that `Tweet` `     \Value::$url` is a string:

``` brush:
protected function checkValueStructure( BaseValue $value )
{
   if ( !is_string( $value->url ) )
   {
       throw new eZ\Publish\Core\Base\Exceptions\InvalidArgumentType(
           '$value->url',
           'string',
           $value->url
       );
   }
}
```

You see that this executes the same check as in `createValueFromInput()`, but both methods aren't responsible for the same thing. The first will, *if given something else than a Value of its type*, try to convert it to one. `checkValueStructure()` will always be used, even if the Field Type is directly fed a `Value` object, and not a string.

### Value initialization

#### `getEmptyValue()`

This method provides what is considered an empty value of this type, depending on your business requirements. No extra initialization is required in this case.

**eZ/Publish/FieldType/Tweet/Type.php**

``` brush:
public function getEmptyValue()
{
   return new Value;
}
```

If you ran the unit tests at this point, you would get about five failures, all of them on the `fromHash()` or `     toHash()` methods. You'll handle them later.

### Validation methods

#### `validateValidatorConfiguration()` and `validate()`

The Type class is also responsible for validating input data (to a `Field`), as well as configuration input data (to a `FieldDefinition`). In this tutorial, we will run two validation operations on input data:

-   validate submitted urls, ensuring they actually reference a Twitter status;

-   limit input to a known list of authors, as an optional validation step.

`     validateValidatorConfiguration()` will be called when an instance of the Field Type is added to a Content Type, to ensure that the validator configuration is valid.

For the validator schema configuration, you can add:

**eZ/Publish/FieldType/Tweet/Type.php**

``` brush:
protected $validatorConfigurationSchema = array(
    'TweetUrlValidator' => array(),
    'TweetAuthorValidator' => array(
            'AuthorList' => array(
                 'type' => 'array',
                 'default' => array()
        )
    )
);
```

For a TextLine (length validation), it means checking that both min length and max length are positive integers, and that min is lower than max.

When an instance of the type is added to a Content Type, `validateValidatorConfiguration()` receives the configuration for the validators used by the Type as an array. It must return an array of error messages if errors are found in the configuration, and an empty array if no errors were found.

For TextLine, the provided array looks like this:

**eZ/Publish/FieldType/Tweet/Type.php**

``` brush:
array(
   'StringLengthValidator' => array(
       'minStringLength' => 0,
       'maxStringLength' => 100
   )
);
```

The structure of this array is totally free, and up to each type implementation. In this tutorial it will mimic what is done in native Field Types:

Each level one key is the name of a validator, as acknowledged by the Type. That key contains a set of parameter name / parameter value rows. You must check that:

-   all the validators in this array are known to the type

-   arguments for those validators are valid and have sane values

You do not need to include mandatory validators if they don’t have options. Here is an example of what your Type expects as validation configuration:

``` brush:
array(
   ‘TweetAuthorValidator’ => array(
       ‘AuthorList’ => array( ‘johndoe’, ‘janedoe’ )
   )
);
```

The configuration says that tweets must be either by `johndoe` or by `janedoe`. If you had not provided `TweetAuthorValidator` at all, it would have been ignored.

You will iterate over the items in `$validatorConfiguration` and:

add errors for those you don’t know about;

check that provided arguments are known and valid:

-   `TweetAuthorValidator` accepts a non-empty array of valid Twitter usernames

**eZ/Publish/FieldType/Tweet/Type.php**

``` brush:
public function validateValidatorConfiguration( $validatorConfiguration )
{
    $validationErrors = array();
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
                        $validationErrors[] = new ValidationError("Invalid authorList argument");
                    }

                    foreach ($value as $authorName) {
                        if (!preg_match('/^[a-z0-9_]{1,15}$/i', $authorName)) {
                            $validationErrors[] = new ValidationError("Invalid twitter username");
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

`     validate()` is the method that runs the actual validation on data, when a content item is created with a Field of this type:

**eZ/Publish/FieldType/Tweet/Type.php**

``` brush:
/**
 * Validates a field based on the validators in the field definition.
 *
 * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
 *
 * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $fieldDefinition
 * The field definition of the field
 * @param Value $fieldValue The field value for which an action is performed
 *
 * @return \eZ\Publish\SPI\FieldType\ValidationError[]
 */
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
```

First, you validate the url with a regular expression. If it doesn’t match, you add an instance of `     ValidationError` to the return array. Note that the tested value isn’t directly embedded in the message but passed as an argument. This ensures that the variable is properly encoded in order to prevent attacks, and allows for singular/plural phrases using the second parameter.

Then, if your Field Type instance’s configuration contains a `     TweetValueValidator   ` key, you will check that the username in the status url matches one of the valid authors.

### Metadata handling methods

#### `getName()` and `getSortInfo()`

Field Types require two methods related to Field metadata:

-   ` getName()` is used to generate a name out of a Field value, either to name a Content item (naming pattern in legacy) or to generate a part for a URL alias.

-   ` getSortInfo()` is used by the persistence layer to obtain the value it can use to sort and filter on a Field of this type

Obviously, a tweet’s full URL isn’t really suitable as a name. Let’s use a subset of it: `     <username>-<tweetId>   ` should be reasonable enough, and suitable for both sorting and naming.

You can assume that this method will not be called if the Field is empty, and that the URL is a valid twitter URL:

``` brush:
public function getName( SPIValue $value )
{
   return preg_replace(
       '#^https?://twitter\.com/([^/]+)/status/([0-9]+)$#',
       '$1-$2',
       (string)$value->url );
}

protected function getSortInfo(CoreValue $value)
{
    return (string)$value->url;
}
```

In `     getName()` you run a regular expression replace on the URL to extract the part you’re interested in.

This name is a perfect match for `     getSortInfo()` as it allows you to sort by the tweet’s author and by the tweet’s ID.

### Field Type serialization methods

#### `fromHash()` and `toHash()`

Both methods defined in the Field Type interface are core to the REST API. They are used to export values to serializable hashes.

In this case it is quite easy:

-   ` toHash()` will build a hash with every property from `Tweet\Value`;

-   ` fromHash()` will instantiate a `Tweet\Value` with the hash it receives.  

``` brush:
public function fromHash( $hash )
{
   if ( $hash === null )
   {
       return $this->getEmptyValue();
   }
   return new Value( $hash );
}
 
public function toHash(SPIValue $value)
{
    if ($this->isEmptyValue($value)) {
        return null;
    }
    return [
        'url' => $value->url,
        'authorUrl' => $value->authorUrl,
        'contents' => $value->contents
    ];
}
```

### Persistence methods

#### `fromPersistenceValue()` and `toPersistenceValue()`

Storage of Field Type data is done through the persistence layer (SPI).

Field Types use their own Value objects to expose their contents using their own domain language. However, to store those objects, the Type needs to map this custom object to a structure understood by the persistence layer: `PersistenceValue`. This simple value object has three properties:

-   `data` – standard data, stored using the storage engine's native features
-   `externalData` – external data, stored using a custom storage handler
-   `sortKey` – sort value used for sorting

The role of those mapping methods is to convert a `Value` of the Field Type into a `PersistenceValue` and the other way around.

About external storage

<span class="aui-icon aui-icon-small aui-iconfont-info confluence-information-macro-icon"></span>
Whatever is stored in `externalData` requires an external storage handler to be written. Read more about external storage in [Field Type API and best practices](https://doc.ez.no/display/DEVELOPER/Field+Type+API+and+best+practices).

External storage is beyond the scope of this tutorial, but many examples can be found in existing Field Types.

You will follow a simple implementation here: the `Tweet\Value` object will be serialized as an array to the `code` property using `fromHash()` and `toHash()`:

**Tweet\\Type**

``` brush:
/**
 * @param \EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet\Value $value
 * @return \eZ\Publish\SPI\Persistence\Content\FieldValue
 */
public function toPersistenceValue(SPIValue $value)
{
    if ($value === null) {
        return new PersistenceValue(
            [
                'data' => null,
                'externalData' => null,
                'sortKey' => null,
            ]
        );
    }
    return new PersistenceValue(
        [
            'data' => $this->toHash($value),
            'sortKey' => $this->getSortInfo($value),
        ]
    );
}
/**
 * @param \eZ\Publish\SPI\Persistence\Content\FieldValue $fieldValue
 * @return \EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet\Value
 */
public function fromPersistenceValue( PersistenceValue $fieldValue )
{
    if ( $fieldValue->data === null )
    {
        return $this->getEmptyValue();
    }
    return new Value( $fieldValue->data );
}
```

Fetching data from the Twitter API
==================================

As explained in the tutorial's introduction, you will enrich our tweet's URL with the embed version, fetched using the Twitter API. To do so, you will, when `toPersistenceValue()` is called, fill in the value's contents property from this method, before creating the `PersistenceValue` object.

First, we need a Twitter client in `Tweet\Type`. For convenience, one is provided in this tutorial's bundle:

-   the `Twitter\TwitterClient` class
-   the `Twitter\TwitterClientInterface` interface
-   an `ezsystems.tweetbundle.twitter.client` service that uses the class above.

The interface has one method: `getEmbed( $statusUrl )` that, given a tweet's URL, returns the embed code as a string. The implementation is very simple, for the sake of simplicity, but gets the job done. Ideally, it should at the very least handle errors, but it is not necessary here.

Injecting the Twitter client into `Tweet\Type`
----------------------------------------------

Your Field Type doesn't have a constructor yet. You will create one, with an instance of `Twitter\TwitterClientInterface` as the argument, and store it in a new protected property:

**eZ/Publish/FieldType/Tweet/Type.php:**

``` brush:
use EzSystems\TweetFieldTypeBundle\Twitter\TwitterClientInterface;
 
class Type extends FieldType
{
    /** @var TwitterClientInterface */
    protected $twitterClient;

    public function __construct( TwitterClientInterface $twitterClient )
    {
        $this->twitterClient = $twitterClient;
    }
}
```

Completing the value using the Twitter client
---------------------------------------------

As described above, before creating the `PersistenceValue` object in `toPersistenceValue`, you will fetch the tweet's embed contents using the client, and assign it to `Tweet\Value::$data`:

**eZ/Publish/FieldType/Tweet/Type.php**

``` brush:
    public function toPersistenceValue(SPIValue $value)
    {
        if ($value === null) {
            return new PersistenceValue(
                [
                    'data' => null,
                    'externalData' => null,
                    'sortKey' => null,
                ]
            );
        }
        if ($value->contents === null) {
            $value->contents = $this->twitterClient->getEmbed($value->url);
        }
        return new PersistenceValue(
            [
                'data' => $this->toHash($value),
                'sortKey' => $this->getSortInfo($value),
            ]
        );
    }
```

And that's it! When the persistence layer stores content from our type, the value will be completed with what the twitter API returns.

 

------------------------------------------------------------------------

 

 <span class="char" title="Leftwards Black Arrow">⬅</span> Previous: [Implement the Tweet\\Value class](Implement-the-Tweet-Value-class)

Next: <span class="confluence-link" title="Black Rightwards Arrow">[Register the Field Type as a service](Register-the-Field-Type-as-a-service) ➡</span>

**Tutorial path**

**Tweet\\Type class methods**

-   [Identification method](#ImplementtheTweet\Typeclass-Identificationmethod)
    -   [getFieldTypeIdentifier()](#ImplementtheTweet\Typeclass-getFieldTypeIdentifier())
-   [Value handling methods](#ImplementtheTweet\Typeclass-Valuehandlingmethods)
    -   [createValueFromInput() and checkValueStructure()](#ImplementtheTweet\Typeclass-createValueFromInput()andcheckValueStructure())
-   [Value initialization](#ImplementtheTweet\Typeclass-Valueinitialization)
    -   [getEmptyValue()](#ImplementtheTweet\Typeclass-getEmptyValue())
-   [Validation methods](#ImplementtheTweet\Typeclass-Validationmethods)
    -   [validateValidatorConfiguration() and validate()](#ImplementtheTweet\Typeclass-validateValidatorConfiguration()andvalidate())
-   [Metadata handling methods](#ImplementtheTweet\Typeclass-Metadatahandlingmethods)
    -   [getName() and getSortInfo()](#ImplementtheTweet\Typeclass-getName()andgetSortInfo())
-   [Field Type serialization methods](#ImplementtheTweet\Typeclass-FieldTypeserializationmethods)
    -   [fromHash() and toHash()](#ImplementtheTweet\Typeclass-fromHash()andtoHash())
-   [Persistence methods](#ImplementtheTweet\Typeclass-Persistencemethods)
    -   [fromPersistenceValue() and toPersistenceValue()](#ImplementtheTweet\Typeclass-fromPersistenceValue()andtoPersistenceValue())




