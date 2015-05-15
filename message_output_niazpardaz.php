<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * niazpardaz message processor to send messages by niazpardaz
 *
 * @package    message_niazpardaz
 * @copyright  2008 Luis Rodrigues
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
require_once($CFG->dirroot . '/message/output/lib.php');

//require_once($CFG->dirroot . '/message/output/lib/nusoap.php');
/**
 * The niazpardaz message processor
 *
 * @package   message_niazpardaz
 * @copyright 2008 Luis Rodrigues
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message_output_niazpardaz extends message_output {

  /**
   * Processes the message and sends a notification via niazpardaz
   *
   * @param stdClass $eventdata the event data submitted by the message sender plus $eventdata->savedmessageid
   * @return true if ok, false if error
   */
  function send_message($eventdata) {
    global $CFG;

    // Skip any messaging of suspended and deleted users.
    if ($eventdata->userto->auth === 'nologin' or $eventdata->userto->suspended or $eventdata->userto->deleted) {
      return true;
    }

    if (!empty($CFG->nosmsever)) {
      // hidden setting for development sites, set in config.php if needed
      debugging('$CFG->nosmsever is active, no niazpardaz message sent.', DEBUG_MINIMAL);
      return true;
    }

    if (PHPUNIT_TEST) {
      // No connection to external servers allowed in phpunit tests.
      return true;
    }

    //hold onto niazpardaz id preference because /admin/cron.php sends a lot of messages at once
    static $numbers = array();

    if (!array_key_exists($eventdata->userto->id, $numbers)) {
      $phone2 = $eventdata->userto->phone2;
      // validate $phone2
      $phone2 = $this->mobileValidation($phone2);
      $numbers[$eventdata->userto->id] = $phone2;
    }

    $number = $numbers[$eventdata->userto->id];

    //calling s() on smallmessage causes niazpardaz to display things like &lt; niazpardaz != a browser
    $message = !empty($eventdata->smallmessage) ? $eventdata->smallmessage : $eventdata->fullmessage;
    $message = strip_tags($message);

    try {
      ini_set("soap.wsdl_cache_enabled", "0");
      $client = new SoapClient('http://5.9.76.186/SendService.svc?wsdl', array('encoding' => 'UTF-8'));
      $parameters['userName'] = $CFG->niazpardazusername;
      $parameters['password'] = $CFG->niazpardazpassword;
      $parameters['fromNumber'] = $CFG->niazpardaznumber;
      $parameters['toNumbers'] = array($number);
      $parameters['messageContent'] = $message;
      $parameters['isFlash'] = false;
      $recId = array();
      $status = array();
      $parameters['recId'] = &$recId;
      $parameters['status'] = &$status;
      $client->SendSMS($parameters)->SendSMSResult;
    } catch (SoapFault $e) {
      debugging($e->getMessage());
      return false;
    }
    return true;
  }

  //define "98" to first of the numbet
  function mobileValidation($number) {
    $number = (int) $number;
    if (strpos($number, "98") === 0) {
      $number = substr($number, 2);
    }
    $final = "0" . $number;
    return $final;
  }

  /**
   * Creates necessary fields in the messaging config form.
   *
   * @param array $preferences An array of user preferences
   */
  function config_form($preferences) {
    global $CFG, $USER;

    if (!$this->is_system_configured()) {
      return get_string('notconfigured', 'message_niazpardaz');
    } else {
      return get_string('niazpardazmobilenumber', 'message_niazpardaz') . ': ' . $USER->phone2;
    }
  }

  /**
   * Parses the submitted form data and saves it into preferences array.
   *
   * @param stdClass $form preferences form class
   * @param array $preferences preferences array
   */
  function process_form($form, &$preferences) {
    
  }

  /**
   * Loads the config data from database to put on the form during initial form display
   *
   * @param array $preferences preferences array
   * @param int $userid the user id
   */
  function load_data(&$preferences, $userid) {
    
  }

  /**
   * Tests whether the niazpardaz settings have been configured
   * @return boolean true if niazpardaz is configured
   */
  function is_system_configured() {
    global $CFG;
    return (!empty($CFG->niazpardaznumber) && !empty($CFG->niazpardazusername) && !empty($CFG->niazpardazpassword));
  }

  /**
   * Tests whether the niazpardaz settings have been configured on user level
   * @param  object $user the user object, defaults to $USER.
   * @return bool has the user made all the necessary settings
   * in their profile to allow this plugin to be used.
   */
  function is_user_configured($user = null) {
    global $USER;

    if (is_null($user)) {
      $user = $USER;
    }
    return (bool) $user->phone2;
  }

}

/*
 *
 *         $f = fopen('/tmp/event_niazpardazx', 'a+');
        fwrite($f, date('l dS \of F Y h:i:s A')."\n");
        fwrite($f, "from: $message->userfromid\n");
        fwrite($f, "userto: $message->usertoid\n");
        fwrite($f, "subject: $message->subject\n");
        fclose($f);


$savemessage = new stdClass();
    $savemessage->useridfrom        = 3;
    $savemessage->useridto          = 2;
    $savemessage->subject           = 'IM';
    $savemessage->fullmessage       = 'full';
    $savemessage->timecreated       = time();


$a = new message_output_niazpardaz();

$a->send_message($savemessage);
* */

