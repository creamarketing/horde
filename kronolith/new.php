<?php
/**
 * Copyright 1999-2010 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * @author Chuck Hagenbuch <chuck@horde.org>
 */

require_once dirname(__FILE__) . '/lib/Application.php';
Horde_Registry::appInit('kronolith');

if (Kronolith::showAjaxView()) {
    Horde::applicationUrl('', true)->setAnchor('event')->redirect();
}

/* Check permissions. */
$url = Horde::applicationUrl($prefs->getValue('defaultview') . '.php', true)
      ->add(array('month' => Horde_Util::getFormData('month'),
                  'year' => Horde_Util::getFormData('year')));

$perms = $GLOBALS['injector']->getInstance('Horde_Perms');
if ($perms->hasAppPermission('max_events') !== true &&
    $perms->hasAppPermission('max_events') <= Kronolith::countEvents()) {
    try {
        $message = Horde::callHook('perms_denied', array('kronolith:max_events'));
    } catch (Horde_Exception_HookNotSet $e) {
        $message = @htmlspecialchars(sprintf(_("You are not allowed to create more than %d events."), $perms->hasAppPermission('max_events')), ENT_COMPAT, $GLOBALS['registry']->getCharset());
    }
    $notification->push($message, 'horde.error', array('content.raw'));
    $url->redirect();
}

$calendar_id = Horde_Util::getFormData('calendar', Kronolith::getDefaultCalendar(Horde_Perms::EDIT));
if (!$calendar_id) {
    $url->redirect();
}

$event = Kronolith::getDriver()->getEvent();
$_SESSION['kronolith']['attendees'] = $event->attendees;
$_SESSION['kronolith']['resources'] = $event->getResources();

$date = Horde_Util::getFormData('datetime');
if (!$date) {
    $date = Horde_Util::getFormData('date', date('Ymd')) . '000600';
    if ($prefs->getValue('twentyFour')) {
        $event->start->hour = 12;
    }
}
$event->start = new Horde_Date($date);
$event->end = new Horde_Date($event->start);
if (Horde_Util::getFormData('allday')) {
    $event->end->mday++;
} else {
    // Default to a 1 hour duration.
    $event->end->hour++;
}
$month = $event->start->month;
$year = $event->start->year;

$buttons = array('<input type="submit" class="button" name="save" value="' . _("Save Event") . '" />');
$url = Horde_Util::getFormData('url');
if (isset($url)) {
    $cancelurl = new Horde_Url($url);
} else {
    $cancelurl = Horde::applicationUrl('month.php', true)->add('month', $month);
}

$all_calendars = Kronolith::listCalendars(false, Horde_Perms::EDIT | Kronolith::PERMS_DELEGATE);
$calendars = array();
foreach ($all_calendars as $id => $calendar) {
    if ($calendar->get('owner') != $GLOBALS['registry']->getAuth() &&
        !empty($GLOBALS['conf']['share']['hidden']) &&
        !in_array($calendar->getName(), $GLOBALS['display_calendars'])) {
        continue;
    }
    $calendars[$id] = $calendar;
}

Horde_Core_Ui_JsCalendar::init(array(
    'full_weekdays' => true
));

$title = _("Add a new event");
Horde::addScriptFile('edit.js', 'kronolith');
Horde::addScriptFile('popup.js', 'horde');
require KRONOLITH_TEMPLATES . '/common-header.inc';
require KRONOLITH_TEMPLATES . '/menu.inc';
require KRONOLITH_TEMPLATES . '/edit/edit.inc';

require $registry->get('templates', 'horde') . '/common-footer.inc';
