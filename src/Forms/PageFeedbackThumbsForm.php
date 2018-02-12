<?php

namespace Vulcan\PageFeedback\Forms;

use SilverStripe\Control\Controller;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\LiteralField;

/**
 * Class PageFeedbackForm
 * @package Vulcan\PageFeedback\Forms
 */
class PageFeedbackThumbsForm extends Form
{
    /**
     * PageFeedbackForm constructor.
     *
     * @param Controller $controller
     * @param string     $name
     */
    public function __construct(Controller $controller, $name)
    {
        $fields = FieldList::create([]);

        $actions = FieldList::create([
            LiteralField::create('thumbsUpWrapper', '<div class="pagefeedback-thumbs-up">'),
            FormAction::create('processPageFeedbackUp', _t('VulcanPageFeedback.UP_BUTTON_TEXT', '+1'))->setUseButtonTag(true)->setButtonContent('+1'),
            LiteralField::create('thumbsUpWrapperEnd', '</div>'),
            LiteralField::create('thumbsDownWrapper', '<div class="pagefeedback-thumbs-down">'),
            FormAction::create('processPageFeedbackDown', _t('VulcanPageFeedback.DOWN_BUTTON_TEXT', '-1'))->setUseButtonTag(true)->setButtonContent('-1'),
            LiteralField::create('thumbsDownWrapperEnd', '</div>'),
        ]);

        $this->extend('updateFormFields', $fields);
        $this->extend('updateFormActions', $actions);

        parent::__construct($controller, $name, $fields, $actions, null);
    }

    /**
     * @return array
     */
    public function getRatingMap()
    {
        return [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5
        ];
    }
}
