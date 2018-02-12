<?php

namespace Vulcan\PageFeedback\Forms;

use SilverStripe\Control\Controller;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextField;

/**
 * Class PageFeedbackForm
 * @package Vulcan\PageFeedback\Forms
 */
class PageFeedbackForm extends Form
{
    /**
     * PageFeedbackForm constructor.
     *
     * @param Controller $controller
     * @param string     $name
     */
    public function __construct(Controller $controller, $name)
    {
        $fields = FieldList::create([
            OptionsetField::create('Rating', 'Rating', $this->getRatingMap())->setTemplate('Vulcan\PageFeedback\Forms\PageFeedbackOptionsetField'),
            TextField::create('Comment', 'Comment')
        ]);

        $actions = FieldList::create([
            FormAction::create('processPageFeedback', 'Submit Feedback')->setUseButtonTag(true)->setButtonContent('Submit Feedback')
        ]);

        $validator = RequiredFields::create([
            'Rating'
        ]);

        $this->extend('updateFormFields', $fields);
        $this->extend('updateFormActions', $actions);
        $this->extend('updateFormValidator', $validator);

        parent::__construct($controller, $name, $fields, $actions, $validator);
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
