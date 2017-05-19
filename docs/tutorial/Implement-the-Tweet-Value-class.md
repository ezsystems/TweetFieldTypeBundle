1.  <span>[Developer](index)</span>
2.  <span>[Creating a Tweet Field Type](Creating-a-Tweet-Field-Type)</span>

<span id="title-text"> Developer : Implement the Tweet\\Value class </span>
===========================================================================


The Value class of a Field Type is by design very simple. It is meant to be stateless and as lightweight as possible.<span> </span><span class="hardreadability"><span><span>This class must contain as little logic as possible, because the logic is the responsibility of the Type class</span></span></span><span><span>. You will create this Type class in the next step.</span></span>

<span class="aui-icon aui-icon-small aui-iconfont-info confluence-information-macro-icon"></span>
All the code for the Bundle will be created in: `src/EzSystems/TweetFieldTypeBundle`

The Value class will contain at least:

-   public properties: used to store the actual data 

-   an implementation of the `__toString()` method: required by the Value interface it inherits from

By default, the constructor from `      FieldType\Value` will be used. It allows you to pass a hash of property/value pairs. You can override it as well if you want.

The Tweet Field Type is going to store 3 elements:

-   The tweet’s URL

-   The tweet’s author URL

-   The body, as an HTML string  

At this point, it does not matter where they are stored. All you care about is *what you want your Field Type to expose as an API*.

You will end up with the following properties:

**eZ/Publish/FieldType/Tweet/Value.php**

``` brush:
//Properties of the class Value
/**
* Tweet URL on twitter.com (http://twitter.com/UserName/status/id).
* @var string
*/
public $url;

 
/**
* Author's tweet URL (http://twitter.com/UserName)
* @var string
*/
public $authorUrl;

 
/**
* The tweet's embed HTML
* @var string
*/
public $contents;
```

The only thing left to honor the `      FieldType\Value` interface is to add a `__toString()` method, in addition to the constructor. Let’s say that yours will return the tweet’s URL:

**eZ/Publish/FieldType/Tweet/Value.php**

``` brush:
//Methods of the class Value
public function __toString()
{
   return (string)$this->url;
}
```

 

------------------------------------------------------------------------

 

 <span class="char" title="Leftwards Black Arrow">⬅</span> Previous: [Structure the bundle](Structure-the-bundle)

Next: <span class="confluence-link" title="Black Rightwards Arrow">[Implement the Tweet\\Type class](Implement-the-Tweet-Type-class) ➡</span>



