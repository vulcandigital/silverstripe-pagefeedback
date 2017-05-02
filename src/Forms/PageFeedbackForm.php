<?php

/**
 * Class PageFeedbackForm
 *
 * @author Reece Alexander <reece@steadlane.com.au>
 */
class PageFeedbackForm extends Form
{
    /**
     * PageFeedbackForm constructor.
     *
     * @param Controller $controller
     * @param string $name
     */
    public function __construct(Controller $controller, $name)
    {
        $fields = FieldList::create(
            OptionsetField::create('Rating', 'Rating', $this->getRatingMap())->setTemplate('PageFeedbackOptionsetField'),
            TextField::create('Comment', 'Comment')
        );

        $actions = FieldList::create(
            FormAction::create('processPageFeedback', 'Submit Feedback')
        );

        $validator = RequiredFields::create(
            array(
                'Rating'
            )
        );

        parent::__construct($controller, $name, $fields, $actions, $validator);
    }

    /**
     * @return array
     */
    public function getRatingMap()
    {
        return array(
            0 => 1,
            1 => 2,
            2 => 3,
            3 => 4,
            4 => 5
        );
    }
}