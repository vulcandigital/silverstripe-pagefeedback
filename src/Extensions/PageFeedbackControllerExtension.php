<?php

namespace Vulcan\PageFeedback\Extensions;

use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Convert;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\Form;
use Vulcan\PageFeedback\Forms\PageFeedbackForm;
use Vulcan\PageFeedback\Forms\PageFeedbackThumbsForm;
use Vulcan\PageFeedback\Models\PageFeedbackEntry;
use SilverStripe\SpamProtection\Extension\FormSpamProtectionExtension;

/**
 * Class PageFeedback
 *
 * @author Reece Alexander <reece@steadlane.com.au>
 */
class PageFeedbackControllerExtension extends Extension
{
    /** @var array */
    private static $allowed_actions = [
        'getPageFeedbackForm',
        'processPageFeedback',
        'processPageFeedbackUp',
        'processPageFeedbackDown',
    ];


    /**
     * Generate the Page Feedback Form
     *
     * @return PageFeedbackForm
     */
    public function getPageFeedbackForm()
    {
        $enableSpamProtection = $this->owner->config()->get('form_spam_protection');

        $mode = $this->owner->config()->get('pagefeedback_mode');
        /** @var PageFeedbackForm|PageFeedbackThumbsForm $formByMode */
        $formByMode = ($mode == 'form' || !$mode) ? PageFeedbackForm::class : PageFeedbackThumbsForm::class;

        $form = $formByMode::create($this->owner, __FUNCTION__);

        // Optionally enable skiplnks to "drop" users to the correct area in the page
        if ($skipLink = $this->owner->config()->get('pagefeedback_skiplink')) {
            $form->setFormAction(sprintf('%s#%s', $form->FormAction(), $skipLink));
        }

        if ($form->hasExtension(FormSpamProtectionExtension::class) && $enableSpamProtection) {
            $form->enableSpamProtection();
        }

        $form->addExtraClass("pagefeedback-$mode");

        return $form;
    }

    /**
     * Processes submitted data for "form" mode
     *
     * @param           $data
     * @param Form|null $form
     *
     * @return bool|HTTPResponse
     */
    public function processPageFeedback($data, Form $form)
    {
        if ($this->owner->config()->get('pagefeedback_mode') !== 'form') {
            $this->owner->httpError(404);
        }

        if (Director::is_ajax()) {
            return $this->processPageFeedbackAjax($data, $form);
        }

        if ($this->getGivenFeedback()) {
            $form->sessionMessage(_t('VulcanPageFeedback.SUBMIT_ALREADY_RATED', 'You have already provided feedback for this page'), 'bad');
            return $this->owner->redirectBack();
        }

        $this->savePageFeedbackEntry($form);

        $form->sessionMessage(_t('VulcanPageFeedback.SUBMIT_THANKS', 'Thanks for your feedback!'), 'good');
        return $this->owner->redirectBack();
    }

    /**
     * AJAX support for "form" mode
     *
     * @param      $data
     * @param Form $form
     *
     * @return $this|bool|HTTPResponse
     */
    public function processPageFeedbackAjax($data, Form $form)
    {
        if (!Director::is_ajax()) {
            return $this->processPageFeedback($data, $form);
        }

        $response = new HTTPResponse();
        $response->addHeader('Content-Type', 'application/json');

        if ($this->getGivenFeedback()) {
            return $response->setBody(Convert::array2json([
                'success' => false,
                'error'   => true,
                'message' => _t('VulcanPageFeedback.SUBMIT_ALREADY_RATED', 'You have already provided feedback for this page')
            ]));
        }

        $this->savePageFeedbackEntry($form);

        return $response->setBody(Convert::array2json([
            'success' => true,
            'error'   => false,
            'message' => _t('VulcanPageFeedback.SUBMIT_THANKS', 'Thanks for your feedback!')
        ]));
    }

    /**
     * @param      $data
     * @param Form $form
     *
     * @return HTTPResponse
     */
    public function processPageFeedbackUp($data, Form $form)
    {
        return $this->processPageFeedbackThumbs($data, $form, '+1');
    }

    /**
     * @param      $data
     * @param Form $form
     *
     * @return HTTPResponse
     */
    public function processPageFeedbackDown($data, Form $form)
    {
        return $this->processPageFeedbackThumbs($data, $form, '-1');
    }

    /**
     * Processes submitted data for "thumbs" mode
     *
     * @param $data
     * @param $form
     * @param $upOrDown "up" or "down"
     *
     * @throws \Exception
     *
     * @return HTTPResponse
     */
    private function processPageFeedbackThumbs($data, Form $form, $upOrDown)
    {
        if ($this->owner->config()->get('pagefeedback_mode') !== 'thumbs') {
            $this->owner->httpError(404);
        }

        if (!in_array($upOrDown, ['+1', '-1'])) {
            throw new \Exception('upOrDown should be "+1" or "-1');
        }

        if (Director::is_ajax()) {
            return $this->processPageFeedbackThumbsAjax($data, $form, $upOrDown);
        }

        if ($this->getGivenFeedback()) {
            $form->sessionMessage(_t('VulcanPageFeedback.SUBMIT_ALREADY_RATED', 'You have already provided feedback for this page'), 'bad');
            return $this->owner->redirectBack();
        }

        $this->savePageFeedbackEntry($form, $upOrDown);

        $form->sessionMessage(_t('VulcanPageFeedback.SUBMIT_THANKS', 'Thanks for your feedback!'), 'good');
        return $this->owner->redirectBack();
    }

    /**
     * AJAX support for "thumbs" mode
     *
     * @param      $data
     * @param Form $form
     * @param      $upOrDown
     *
     * @return $this|HTTPResponse
     */
    private function processPageFeedbackThumbsAjax($data, Form $form, $upOrDown)
    {
        if (!Director::is_ajax()) {
            return $this->processPageFeedbackThumbs($data, $form, $upOrDown);
        }

        $response = new HTTPResponse();
        $response->addHeader('Content-Type', 'application/json');

        if ($this->getGivenFeedback()) {
            return $response->setBody(Convert::array2json([
                'success' => false,
                'error'   => true,
                'message' => _t('VulcanPageFeedback.SUBMIT_ALREADY_RATED', 'You have already provided feedback for this page')
            ]));
        }

        $this->savePageFeedbackEntry($form, $upOrDown);

        return $response->setBody(Convert::array2json([
            'success' => true,
            'error'   => false,
            'message' => _t('VulcanPageFeedback.SUBMIT_THANKS', 'Thanks for your feedback!')
        ]));
    }

    /**
     * Save a new entry
     *
     * @param Form $form
     * @param null $upOrDown If provided, the entry will be assume its "thumbs" mode. Possible values are +1 and -1
     *
     * @return PageFeedbackEntry
     */
    private function savePageFeedbackEntry(Form $form, $upOrDown = null)
    {
        /** @var PageFeedbackEntry $record */
        $record = PageFeedbackEntry::create();
        $form->saveInto($record);
        $record->Thumbs = $upOrDown;
        $record->SessionID = session_id();
        $record->IPAddress = $this->owner->getRequest()->getIP();
        $record->PageID = $this->owner->ID;
        $record->write();

        return $record;
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
        $record = PageFeedbackEntry::get()->filter(['PageID' => $this->owner->ID, 'SessionID' => session_id(), 'IPAddress' => $this->owner->getRequest()->getIP()])->first();

        return $record;
    }
}
