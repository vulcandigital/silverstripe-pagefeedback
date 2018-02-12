<?php

namespace Vulcan\PageFeedback\Extensions;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordViewer;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\HasManyList;
use Vulcan\PageFeedback\Models\PageFeedbackEntry;

/**
 * Class PageFeedback
 *
 * @author Reece Alexander <reece@steadlane.com.au>
 *
 * @method HasManyList|PageFeedbackEntry[] PageFeedbackEntries()
 * @property \Page|PageFeedbackExtension $owner
 */
class PageFeedbackExtension extends DataExtension
{
    private static $db = [];

    private static $has_many = [
        'PageFeedbackEntries' => PageFeedbackEntry::class
    ];

    /**
     * Forces spam protection on all forms relating to this module.
     * Requires the spamprotection module
     *
     * @config
     * @var bool
     */
    private static $form_spam_protection = false;

    public function updateCMSFields(FieldList $fields)
    {
        parent::updateCMSFields($fields);

        $fields->addFieldsToTab('Root.PageFeedback', [
            GridField::create('PageFeedback', 'Page Feedback', $this->owner->PageFeedbackEntries(), GridFieldConfig_RecordViewer::create())
        ]);

        $fields->addFieldsToTab('Root.Main', [
            ReadonlyField::create('MyPageRating', 'Page Rating', sprintf('%s (%s votes)', $this->getPageRating(), $this->owner->PageFeedbackEntries()->count()))
        ], 'Title');
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

        $stack = [];

        /** @var PageFeedbackEntry $feedback */
        foreach ($this->owner->PageFeedbackEntries() as $feedback) {
            $stack[] = (int)$feedback->Rating;
        }

        return round(array_sum($stack) / count($stack), 1);
    }
}
