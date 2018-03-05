# CHANGELOG

## Unreleased

* Completely [rewritten from scratch](https://github.com/simplepie/simplepie-ng/commit/a26457d3912f4f96aadbc9451d532e58239ed1a5) (compared to the original SimplePie).
* Strict [coding standards](http://cs.sensiolabs.org), [linting](https://github.com/squizlabs/PHP_CodeSniffer), and [static analysis](https://scrutinizer-ci.com/g/simplepie/simplepie-ng/) from the very beginning.
* A high level of unit and integration [test coverage](https://coveralls.io/github/simplepie/simplepie-ng).
* Provided under the liberal [Apache 2.0 license](https://github.com/simplepie/simplepie-ng/blob/master/LICENSE.md).
* Leverages a middleware-based system for feed format support, including custom extensions.
* Leverages [DOMDocument](http://php.net/manual/en/class.domdocument.php) and [XPath 1.0](http://php.net/manual/en/class.domxpath.php) for parsing XML content.
* Designed to support multiple input formats (e.g., XML, JSON, HTML)
* Strict compliance with the [Atom 1.0 specification](https://tools.ietf.org/html/rfc4287).
* Close compliance with [FeedParser.py](https://github.com/kurtmckee/feedparser).
* Support for text, HTML, and XHTML content types.
* Support for `xml:base` and `xml:lang`.
* Opt-in support for [case-insensitive XML element matching](https://medium.com/simplepie-ng/php-domdocument-xpath-1-0-case-insensitivity-and-performance-ad962b98e71c).
