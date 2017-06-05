# Create the bundle

Once you have [installed eZ Platform](https://doc.ez.no/x/opPfAQ), including the creation of a database for the tutorial, [configured your server](https://doc.ez.no/pages/viewpage.action?pageId=31429536), and [started your web server](https://doc.ez.no/display/DEVELOPER/Web+Server), you need to create a code base for the tutorial.

We will use [the Symfony 2 extension mechanism, bundles,](http://symfony.com/doc/current/cookbook/bundles/index.html) to wrap the Fieldtype. You can get started with a bundle using the built-in Symfony 2 bundle generator, following the instructions on this page.
Then you will configure your Bundle to be able to write the code you need to create a Field Type.

The [tutorial's Github repository](https://github.com/ezsystems/TweetFieldTypeBundle) shows you the Bundle in a finished state.

## Generating the bundle

From the eZ Platform root, run the following:

``` bash
php app/console generate:bundle
```

First, you are asked:

``` bash
Are you planning on sharing this bundle across multiple applications? [no]: yes<enter>
```

Type the answer `yes` and submit it with an Enter.

Next you will be asked about the namespace of your bundle.

#### More about naming bundles

See (http://symfony.com/doc/current/cookbook/bundles/best_practices.html#bundle-name) for more details on bundle naming conventions.

Put **EzSystems/TweetFieldTypeBundle** as Bundle namespace, then the name of the bundle will be hinted from this entry.

``` bash
Bundle namespace: EzSystems/TweetFieldTypeBundle<enter>
```

Next, you must select the bundle name. Choose a preferably unique name for the Field Type: `TweetFieldTypeBundle`. Add the vendor name (in this case, `EzSystems`, but you can of course substitute it with your own) and end the name with `Bundle`:

``` bash
Based on the namespace, we suggest EzSystemsTweetFieldTypeBundle.

Bundle name [EzSystemsTweetFieldTypeBundle]:<enter>
```

You are then asked for the target directory. Begin within the `src` folder, but you could (and should!) version it and have it moved to `vendor` at some point. Again, this is the default, so just hit Enter.

``` bash
Target Directory [src/]:<enter>
```

You must then specify which format the configuration will be generated as. Use yml, since it is what is used in eZ Platform itself. Of course, any other format could be used.

``` bash
Configuration format (annotation, yml, xml, php) [xml]: yml<enter>
```

Our bundle should now be generated. Navigate to `src/EzSystems/TweetFieldTypeBundle` and you should see the following structure:

``` bash
$ ls -l src/EzSystems/TweetFieldTypeBundle
Controller/
DependencyInjection/
EzSystemsTweetFieldTypeBundle.php
Resources/
Tests/
```

Feel free to delete the Controller folder, since you won’t use it in this tutorial. It could have been useful, had our Field Type required an interface of its own.
Also, you can safely delete the `Resources/views/Default` folder and `Resources/config/routing.yml` file, as they won't be needed. You should remove the `ez_systems_tweet_field_type` entry from your app/config/routing.yml file as well.

The tests aren't part of the documentation, but you can find them in the repository. 

------------------------------------------------------------------------

⬅ Previous: [Build the bundle](build_the_bundle.md)

Next: [Structure the bundle](structure_the_bundle.md) ➡
