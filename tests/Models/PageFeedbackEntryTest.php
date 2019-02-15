<?php

namespace Vulcan\PageFeedback\Tests;

use SilverStripe\Dev\FunctionalTest;
use Vulcan\PageFeedback\Models\PageFeedbackEntry;

class PageFeedbackEntryTest extends FunctionalTest
{
    protected static $fixture_file = 'PageFeedbackEntryTest.yml';

    public function testDisplayComment()
    {
        /** @var PageFeedbackEntry $withComment */
        /** @var PageFeedbackEntry $withoutComment */
        $withComment = $this->objFromFixture(PageFeedbackEntry::class, 'form_mode_with_comment');
        $withoutComment = $this->objFromFixture(PageFeedbackEntry::class, 'form_mode_without_comment');

        $this->assertEquals('Hello World!', $withComment->getDisplayComment());
        $this->assertEquals('<em>' . _t('VulcanPageFeedback.CMS_NO_COMMENT', 'No comment was provided..') . '</em>', $withoutComment->getDisplayComment());
    }
}
