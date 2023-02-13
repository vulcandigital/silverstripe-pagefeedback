<?php

namespace Vulcan\PageFeedback\Models;

use Page;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLText;

/**
 * Class PageFeedbackEntry
 *
 * @author Reece Alexander <reece@steadlane.com.au>
 *
 * @property int    Rating    1-5 (Terrible-Excellent)
 * @property string Comment   An optional comment the user is able to provided
 * @property string SessionID The session ID that was active when this entry was submitted
 * @property string IPAddress Both this and the session ID is used to determine if the user has provided feedback
 *                            checking both allows us to receive feedback from more than one person on the same network and while
 *                            can be a caveat and taken advantage of, it's much more preferable to receive feedback anywhere you can get it
 * @property string Thumbs    If "thumbs" mode is enabled, this will either specify "Up" or "Down"
 *
 * @property int    PageID
 *
 * @method Page Page()
 */
class PageFeedbackEntry extends DataObject
{
    private static $table_name = 'PageFeedbackEntry';

    /** @var array */
    private static $db = [
        'Rating'    => 'Enum("0,1,2,3,4,5")',
        'Comment'   => 'Text',
        'SessionID' => 'Varchar(255)',
        'IPAddress' => 'Varchar(255)',
        'Thumbs'    => 'Enum("+1, -1")'
    ];

    /** @var array */
    private static $has_one = [
        'Page' => \Page::class
    ];

    /** @var array */
    private static $summary_fields = [
        'DisplayRating'     => 'Rating',
        'DisplayRatingMode' => 'Mode',
        'DisplayComment'    => 'Comment'
    ];

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('SessionID');

        return $fields;
    }

    /**
     * @return string|DBHTMLText
     */
    public function getDisplayComment()
    {
        if ($this->Comment) {
            return $this->Comment;
        }

        $html = DBHTMLText::create();

        return $html->setValue('<em>' . _t('VulcanPageFeedback.CMS_NO_COMMENT', 'No comment was provided..') . '</em>');
    }

    public function getRatingMode()
    {
        if ($this->Rating) {
            return 'form';
        }

        if ($this->Thumbs) {
            return 'thumbs';
        }

        return false;
    }

    public function getDisplayRatingMode()
    {
        if (!$mode = $this->getRatingMode()) {
            return 'Unknown';
        }

        return ucfirst($mode);
    }

    public function getDisplayRating()
    {
        if (!$mode = $this->getRatingMode()) {
            return 'Unknown';
        }

        return ($mode === 'form') ? $this->Rating : $this->Thumbs;
    }

    public function getDisplayThumbs()
    {

    }
}
