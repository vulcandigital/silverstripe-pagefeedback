[![Build Status](https://travis-ci.org/zanderwar/silverstripe-pagefeedback.svg?branch=master)](https://travis-ci.org/vulcandigital/silverstripe-pagefeedback)
[![Latest Stable Version](https://poser.pugx.org/zanderwar/silverstripe-pagefeedback/v/stable)](https://packagist.org/packages/vulcandigital/silverstripe-pagefeedback)
[![Latest Unstable Version](https://poser.pugx.org/vulcandigital/silverstripe-pagefeedback/v/unstable)](https://packagist.org/packages/vulcandigital/silverstripe-pagefeedback)
[![Total Downloads](https://poser.pugx.org/zanderwar/silverstripe-pagefeedback/downloads)](https://packagist.org/packages/vulcandigital/silverstripe-pagefeedback)
[![License](https://poser.pugx.org/zanderwar/silverstripe-pagefeedback/license)](https://packagist.org/packages/vulcandigital/silverstripe-pagefeedback)
[![Monthly Downloads](https://poser.pugx.org/zanderwar/silverstripe-pagefeedback/d/monthly)](https://packagist.org/packages/vulcandigital/silverstripe-pagefeedback)

# silverstripe-pagefeedback

This module allows you to add a form to any page type for the purpose of accruing feedback about how a user perceives that page.

Commonly found on help desk pages under a label similar to "How helpful did you find this page?".

It allows the user to rate the page out of five (5) and allows them to optionally provide a comment

The users IP address and their PHP Session ID is recorded on a per-page basis so that a user can only submit feedback once for that specific page.

## Requirements
* silverstripe/cms: "^4.0"

## Installation

Installation is supported via composer only:

```
composer require zanderwar/silverstripe-pagefeedback "^2"
```

## Configuration

This entire module is an both a `DataExtension` and a `Extension`, meaning you must enable it on the page types you desire.

If you wanted to enable it's functionality on all pages you would:

```yml
Page:
  extensions:
    - Vulcan\PageFeedback\Extensions\PageFeedbackExtensions

PageController:
  extensions:
    - Vulcan\PageFeedback\Extensions\PageFeedbackControllerExtensions
```

or for a specific page type

```yml
Vulcan\UserDocs\UserDocsPage:
  extensions:
    - Vulcan\PageFeedback\Extensions\PageFeedbackExtension

Vulcan\UserDocs\UserDocsPageController:
  extensions:
    - Vulcan\PageFeedback\Extensions\PageFeedbackControllerExtensions
```

## Modes
By default, the mode is set to "form" which will generate a form allowing a user to rate between 1-5 and optionally provide a comment
An alternate mode, "thumbs" is available which will generate a form containing two buttons `+1` and `-1`, which can be beautifully styled:

![Thumbs Preview](https://i.imgur.com/RxHQQ2t.png)

You can change the mode via YML on the controllers of the pages you desire

```
Vulcan\UserDocs\UserDocsPageController:
    pagefeedback_mode: "thumbs"
```

#### Adding the form

In order for the form to show you will need to add `$PageFeedbackForm` into your template, in the location you wish for it to display.

e.g.

```html
<div id='myfeedbackform'>
    $PageFeedbackForm
</div>
```

If you wish to hide the form if feedback has already been supplied by the user:

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
    <% with $GivenFeedback %>
    <div id='myprovidedfeedback'>
        <% if not $Rating %>
            Rating: $Rating<br/>
            Comment: $Comment
        <% else %>
            You gave this page a thumbs <strong>$Thumbs</strong>
        <% end_if %>
    </div>
    <% end_with %>
<% end_if %>
```
## Features
- Adds a "Page Rating" section to the CMS Page Editor
- Adds a "Page Feedback" tab containing a `GridField` of all feedback for that specific page

## Inspiration

The form is provided to you unstyled, but each of the five radio buttons on the form have a class of `pagefeedback-option-n` where `n` is `1` through to `5` for easier customisation (ie swap the radio buttons with smiley faces that have different expressions; sad through to happy)

![Shopify Inspiration](http://i.imgur.com/FxtzPFJ.png)
![Shopify Inspiration](http://i.imgur.com/YklTmRc.png)  

(inspiration courtesy of shopify docs)

## License

[BSD-3-Clause](LICENSE.md) - [Vulcan Digital Ltd](https://vulcandigital.co.nz)
