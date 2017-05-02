<?php

class PageFeedbackController extends Extension
{
    /** @var array */
    private static $allowed_actions = array(
        'getPageFeedbackForm',
        'processPageFeedback'
    );

    /**
     * Generate the Page Feedback Form
     *
     * @return PageFeedbackForm
     */
    public function getPageFeedbackForm()
    {
        $config = Config::inst()->forClass('PageFeedback');

        $form = PageFeedbackForm::create($this->owner, __FUNCTION__);

        if($form->hasExtension('FormSpamProtectionExtension') && $config->spam_protection) {
            /** @noinspection PhpUndefinedMethodInspection */
            $form->enableSpamProtection();
        }

        return $form;
    }

    /**
     * Process the form data
     *
     * @param $data
     * @param Form|null $form
     *
     * @return bool|SS_HTTPResponse
     */
    public function processPageFeedback($data, Form $form = null)
    {
        /** @var PageFeedbackEntry $current */
        $current = PageFeedbackEntry::get()->filter(
            array(
                'PageID' => $this->owner->ID,
                'SessionID' => session_id()
            )
        )->first();

        if ($current) {
            $form->sessionMessage('You have already provided feedback for this page', 'bad');
            return Controller::curr()->redirectBack();
        }

        /** @var PageFeedbackEntry $record */
        $record = PageFeedbackEntry::create();
        $form->saveInto($record);
        $record->SessionID = session_id();
        $record->PageID = $this->owner->ID;
        $record->write();

        $form->sessionMessage('Thanks for your feedback!', 'good');
        return Controller::curr()->redirectBack();
    }

    /**
     * If the current user has supplied feedback for this page, return it. Can be used to toggle display
     * of the feedback form
     *
     * @return PageFeedbackEntry
     */
    public function getGivenFeedback()
    {
        /** @var PageFeedbackEntry $record */
        $record = PageFeedbackEntry::get()->filter(
            array(
                'PageID' => $this->owner->ID,
                'SessionID' => session_id()
            )
        )->first();

        return $record;
    }
}