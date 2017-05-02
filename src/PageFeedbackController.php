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
        return PageFeedbackForm::create($this->owner, __FUNCTION__);
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
                'IPAddress' => $_SERVER['REMOTE_ADDR']
            )
        )->first();

        if ($current) {
            $form->sessionMessage('You have already provided feedback for this page', 'bad');
            return Controller::curr()->redirectBack();
        }

        /** @var PageFeedbackEntry $record */
        $record = PageFeedbackEntry::create();
        $form->saveInto($record);
        $record->IPAddress = $_SERVER['REMOTE_ADDR'];
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
                'IPAddress' => $_SERVER['REMOTE_ADDR']
            )
        )->first();

        return $record;
    }
}