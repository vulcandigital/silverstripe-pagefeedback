<?php

namespace Vulcan\PageFeedback\Forms;

use SilverStripe\Control\Controller;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;

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
            FormAction::create('processPageFeedbackUp', _t('VulcanPageFeedback.UP_BUTTON_TEXT', '+1')),
            FormAction::create('processPageFeedbackDown', _t('VulcanPageFeedback.DOWN_BUTTON_TEXT', '-1')),
        ]);

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
