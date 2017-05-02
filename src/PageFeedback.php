<?php

/**
 * Class PageFeedback
 *
 * @author Reece Alexander <reece@steadlane.com.au>
 *
 * @method HasManyList PageFeedbackEntries
 */
class PageFeedback extends DataExtension
{
    /** @var array */
    private static $db = array();

    /** @var array */
    private static $has_many = array(
        'PageFeedbackEntries' => 'PageFeedbackEntry'
    );

    /**
     * {@inheritdoc}
     *
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        parent::updateCMSFields($fields);

        $fields->addFieldsToTab(
            'Root.PageFeedback',
            array(
                GridField::create(
                    'PageFeedback',
                    'Page Feedback',
                    $this->owner->PageFeedbackEntries(),
                    GridFieldConfig_RecordViewer::create()
                )
            )
        );

        $fields->addFieldsToTab(
            'Root.Main',
            array(
                ReadonlyField::create('MyPageRating', 'Page Rating', sprintf('%s (%s votes)', $this->getPageRating(), $this->owner->PageFeedbackEntries()->count()))
            ),
            'Title'
        );
    }

    /**
     * Gets the average Page Rating
     *
     * @return float|int
     */
    public function getPageRating()
    {
        $rating = 0;

        if (!$this->owner->PageFeedbackEntries()->count()) {
            return $rating;
        }

        $stack = array();

        /** @var PageFeedbackEntry $feedback */
        foreach ($this->owner->PageFeedbackEntries() as $feedback) {
            $stack[] = (int)$feedback->Rating;
        }

        return round(array_sum($stack) / count($stack), 1);
    }
}