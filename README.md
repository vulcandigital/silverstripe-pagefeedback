# silverstripe-pagefeedback

This module allows you to add a form to any page type for the purpose of accruing feedback about how a user perceives that page.

Commonly found on help desk pages under a label similar to "How helpful did you find this page?".

It allows the user to rate the page out of five (5) and allows them to optionally provide a comment

The users IP address is recorded on a per-page basis so that a user can only submit feedback once for that specific page. This comes with its fair share of caveats (ie LAN, Internet Cafes etc) that will be eliminated in future versions.
 
## Installation

Installation is supported via composer only:

```
composer require zanderwar/silverstripe-pagefeedback
```

After the module has been successfully installed, run a `dev/build` (and `?flush=1` for good measure)

## Configuration

`PageFeedback` is an `Extension` meaning you must activate it on the page types you desire.

If you wanted to enable it's functionality on all pages you would:

```yml
Page:
  extensions:
    - PageFeedback

Page_Controller:
  extensions:
    - PageFeedbackController
```

or for a specific page type

```yml
MyCustomPage:
  extensions:
    - PageFeedback

MyCustomPage_Controller:
  extensions:
    - PageFeedbackController
```

#### Adding the form

In order for the form to show, you should add `$PageFeedbackForm` in the location you wish for it to display.

e.g.

```html
<div id='myfeedbackform'>
    $PageFeedbackForm
</div>
```

If you wish to hide the form:

```html
<% if not $GivenFeedback %>
<div id='myfeedbackform'>
    $PageFeedbackForm
</div>
<% end_if %>
```

If you wish to display information about the feedback the user has provided:

```html
<% if $GivenFeedback %>
    <% control $GivenFeedback %>
    <div id='myprovidedfeedback'>
        Rating: $Rating<br/>
        Comment: $Comment
    </div>
    <% end_control %>
<% end_if %>
```

## Inspiration

The form is provided to you unstyled, but each of the five radio buttons on the form have a class of `pagefeedback-option-n` where `n` is `1` through to `5` for easier customisation (ie swap the radio buttons with smiley faces that have different expressions; sad through to happy)

![Shopify Inspiration](http://i.imgur.com/FxtzPFJ.png)
![Shopify Inspiration](http://i.imgur.com/YklTmRc.png)

## Contributing

If you wish to contribute to this module please do not hesitate to do so by forking this respository and submitting a Pull Request with your changes/improvements

## License

```
MIT License

Copyright (c) 2017 Zanderwar (Reece Alexander)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
```
