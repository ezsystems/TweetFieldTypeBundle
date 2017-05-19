1.  <span>[Developer](index)</span>
2.  <span>[Creating a Tweet Field Type](Creating-a-Tweet-Field-Type)</span>
3.  <span>[Build the bundle](Build-the-bundle)</span>

<span id="title-text"> Developer : Create the bundle </span>
============================================================



<span class="tL8wMe EMoHub">Once you have [installed eZ Platform](https://doc.ez.no/x/opPfAQ), including the creation of a database for the tutorial, [configured your server](https://doc.ez.no/pages/viewpage.action?pageId=31429536), and [started your web server](https://doc.ez.no/display/DEVELOPER/Web+Server), you need to create a code base for the tutorial. </span>

<span class="tL8wMe EMoHub">We will use <a href="http://symfony.com/doc/current/cookbook/bundles/index.html" class="external-link">the Symfony 2 extension mechanism, bundles,</a> to wrap the Fieldtype. </span>You can get started with a bundle using the built-in Symfony 2 bundle generator, following the instructions on this page.
Then you will configure your Bundle to be able to write the code you need to create a Field Type.

<span class="aui-icon aui-icon-small aui-iconfont-info confluence-information-macro-icon"></span>
The<a href="https://github.com/ezsystems/TweetFieldTypeBundle" class="external-link">tutorial Github repository</a> shows you the Bundle finished.

Generating the bundle
---------------------

From the eZ Platform root, run the following:

``` brush:
php app/console generate:bundle
```

First, you are asked:

``` brush:
Are you planning on sharing this bundle across multiple applications? [no]: yes <enter>
```

Type the answer `yes` and submit it with an Enter.

Next you will be asked about the namespace of your bundle.

More about naming bundles

<span class="aui-icon aui-icon-small aui-iconfont-info confluence-information-macro-icon"></span>
See <a href="http://symfony.com/doc/current/cookbook/bundles/best_practices.html#bundle-name" class="uri" class="external-link">http://symfony.com/doc/current/cookbook/bundles/best_practices.html#bundle-name</a> for more details on bundle naming conventions.

Put **EzSystems\\TweetFieldTypeBundle** as Bundle namespace, then the name of the bundle will be hinted from this entry.

**Namespace bundle**

``` brush:
Bundle namespace: EzSystems\TweetFieldTypeBundle<enter>
```

Next, you must select the bundle name. Choose a preferably unique name for the Field Type: `TweetFieldTypeBundle`. Add the vendor name (in this case, `EzSystems`, but you can of course substitute it with your own) and end the name with `Bundle`:

``` brush:
Based on the namespace, we suggest EzSystemsTweetFieldTypeBundle.

Bundle name [EzSystemsTweetFieldTypeBundle]:<enter>
```

You are then asked for the target directory. Begin within the `src` folder, but you could (and should!) version it and have it moved to `vendor` at some point. Again, this is the default, so just hit Enter.

``` brush:
Target Directory [src/]:<enter>
```

You must then specify which format the configuration will be generated as. Use yml, since it is what is used in eZ Platform itself. Of course, any other format could be used.

``` brush:
Configuration format (annotation, yml, xml, php, annotation): yml<enter>
```

Our bundle should now be generated. Navigate to `src/EzSystemsTweetFieldTypeBundle` and you should see the following structure:

``` brush:
$ ls -l src/EzSystemsTweetFieldTypeBundle
Controller
EzSystemsTweetFieldTypeBundle.php
Resources
```

Feel free to delete the Controller folder, since you won’t use it in this tutorial. It could have been useful, had our Field Type required an interface of its own.

 

------------------------------------------------------------------------

 

 <span class="char" title="Leftwards Black Arrow">⬅</span> Previous: [Build the bundle](Build-the-bundle)

Next: <span class="confluence-link" style="text-align: right;" title="Black Rightwards Arrow">[Structure the bundle](Structure-the-bundle) ➡</span>






