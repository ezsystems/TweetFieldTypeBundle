YUI.add('ez-tweet-view', function (Y) {
    "use strict";
    /**
     * Provides the Tweet field view
     *
     * @module ez-tweet-view
     */
    Y.namespace('eZ');

    /**
     * The Tweet field view
     *
     * @namespace eZ
     * @class TweetView
     * @constructor
     * @extends eZ.FieldView
     */
    Y.eZ.TweetView = Y.Base.create('tweetView', Y.eZ.FieldView, [], {
        /**
         * Returns the value to be used in the template. If the value is not
         * filled, it returns undefined otherwise an object with a `url` entry.
         *
         * @method _getFieldValue
         * @protected
         * @return Object
         */
        _getFieldValue: function () {
            var value = this.get('field').fieldValue, res;

            if ( !value || !value.url ) {
                return res;
            }
            res = {url: value.url};

            return res;
        }
    });

    Y.eZ.FieldView.registerFieldView('eztweet', Y.eZ.TweetView);
});
