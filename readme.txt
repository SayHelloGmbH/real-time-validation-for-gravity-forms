=== Real Time Validation for Gravity Forms ===
Contributors: wisetr, djeet
Tags: gravity forms,gravity forms addon,gravity forms fields validation,live validation,jquery validation,client side validation,javascript validation
Requires at least: 4.2.1
Tested up to: 4.9.6
Stable tag: 1.7.0
License: GPLv2 or later

Real Time Validation for Gravity Forms increases conversion rates of your Gravity Form using inline validation messages as user types in field.


== Description ==

This plugin ensures that users get the feedback in each field as he types even before the form is submitted.Specific validation messages are shown to user help him quickly rectify the mistakes. 

Major features in Real Time Validation for Gravity Forms include:

* Ability to turn on/off Real Time Validation for Gravity Forms on each form.
* Add custom error messages at each field level.
* Works with multi-step forms.
* Works with both environment AJAX / NON-AJAX.
* Works with the Conditional logic of fields.
* Works with Multiple Forms on one page.
 
> [Click Here to Experience Real-Time Validation in Action.](http://demo.formsoptimizer.com/)

= Works with following fields =
 * Text 
 * Textarea
 * Select 
 * Multiselect
 * Number
 * Radio buttons
 * Checkboxes 
 * Name 
 * Email 
 * Phone 
 * Date 
 * Address 
 * Website 
 * Time 
 * All Post fields (Except image upload)  
 * All Pricing fields

** [Live Demo Here ](http://demo.formsoptimizer.com/)**


= Learn How To Use =

[youtube http://www.youtube.com/watch?v=m_bv3eCmvgs]

Here is the example of how to use hook for success state-

> jQuery(document).on("lv_after_field_valid",function(event,element){
   jQuery(element).css("background-color","green");
});

Here is the example of how to use hook for invalid state-

> jQuery(document).on("lv_after_field_invalid",function(event,element){
   jQuery(element).css("background-color","red");
});

= Latest Updates =
* June , 9th 2018: Added a New Validation for matching Email input confirmation fields
* April , 29th 2018: Compatibility With PHP 7.2
* April , 29th 2018: Compatibility With Gravity Forms 2.3
* December, 21st 2017: Compatibility With Partial Entry addon
* December, 21st 2017: Compatibility upto Gravity Forms 2.2.5
* December, 21st 2016: Compatibility Fix: Multiple forms on one page with real-time validation ON.
* December, 21st 2016: Fix: Checkbox with the conditional logic issue.
* December, 21st 2016: Fix: Multi-step form values were not getting sustained on step change.
* December, 21st 2016: Fix: how to use video and on activation behavior fixed for GF > 2.0.0.
* November, 25th 2016: Fix: Filter Regex for modifiers (flags) to prevent validation failure.
* August, 12th 2016: Fix: Hidden state conflict with conditional logic causing fields to be disabled when the form of modal popups. 
* August, 12th 2016: Added: JS Hooks for validation and success. 
* August, 12th 2016: Fix: resolved issue of form object turning into boolean TRUE, when we disable notification forever. 
* July, 18th 2016:  Fix: Get Param conflict with feeds.
* July, 18th 2016:  Fix: Field setting showing even LV switched off.
* June, 13th 2016:  Fix: Address Line number 2 was marked as required.
* June, 13th 2016:  Fix: Conditional fields getting validated while hidden.
* April, 30th 2016: New `How to Use` Video
* April, 30th 2016: Compatible with the changes happening over form while Validation is in Off state.
* April, 28th 2016: Compatibility with Mozilla Firefox & Safari
* April, 28th 2016: Compatibility with mobile browsers
* April, 28th 2016: Better UI for Notifications
* April, 22th 2016: Added Better User Experience
* April, 22th 2016: Added Documentation 
* April, 22th 2016: Added Support section 
* April, 22th 2016: Added Pro plugin Documentation
* April, 22th 2016: Added Ajax & multistep support
* April, 19th 2016: Added new admin notices
* April, 18th 2016: Added new Installation Instructions.
* April, 18th 2016: Added a new set of Screenshots.
* April, 18th 2016: Modified Labels and Tooltip's texts for better understanding.
* April, 16th 2016: Set of popular patterns to choose from.
* April, 16th 2016: Support for Post & Pricing fields.


= Upcoming Releases = 
 
* Dedicated Support Site.


  
PS: You'll need a working Gravity Forms to use it. 

== Installation ==





1. Install “Real Time Validation For Gravity Form” Plugin.
2. Activate the Plugin.
3. Go to Forms  ->  Individual Form Settings  -> Real Time Validation.
4. Enable Real-Time Validation by toggling switch to "Yes".
5. Go to Form Editor and Expand Any Field options. In General Tab under Rules section Check ‘Required” option to make the field required.Skip this step if the field is not required.
6. Go to Appearance Tab -> Custom Validation Message Section & if need be Override Default Validation Message.
7. Go to Advanced Tab -> Check the option “Validate Real-Time Input”.
8. Under field "Enter RegEx Pattern", Enter Your Own RegEx Pattern or Copy N Paste Popular RegEx Patterns from our available patterns list.
9. Customize the Real-Time Error Message to display above/ below the form field.
10.Update form to have Real Time Validation feature activated in front end Gravity form.




== Screenshots ==
1. Activate Real-Time Validation for Gravity Forms.
2. Enable Real-Time Validation On each form.
3. Switch it On and Save.
4. Check the field Required by field editor.
5. Modify Error message If you want to.
6. Enable Pattern matching under "Advanced" Tab.
7. Type RegEx OR Choose from given patterns.
8. List of Popular patterns.
9. The pattern should look like this. 
10. Input custom error message is thrown when pattern validation fails.
11. Update the Form.



== Changelog ==
= 1.7.0 = 
* Fixed: Changes as per needed for making plugin text prepare for better translations

= 1.6.0 = 
* Fixed: Date Field showing JS error after selection in the field.
* Added: New validation for email confirmation field that checks if same email is entered in confirmation field.

= 1.5.0 = 
* Fixed: Critical Update as having fixes that makes the plugin compatible with php > 7.2
* Fixed: Issue in name field validation, forced required validation for the middle name field is getting applied.

= 1.4.0 = 
* Fixed: Critical Issue when User hits submit button more than once, form getting submitted multiple times. 
* Fixed: Resolved conflict with Yoast SEO plugin and moved rendering for the script over wp_footer, to prevent form process over admin calls.

= 1.3.0 = 

* Fixed: Some functionality issues with the Save & Continue feature of gravity forms.
* Fixed: Text-domain modified in last update, but not implemented, now fixed. 
* Added: A callback function to modify the scroll offset of the field position, raised by a support ticket. 

= 1.2.0 = 
* Fixed: Fatal Error coming when partial entry saving AJAX gets fired.
* Added: Compatibility with Gforms 2.2.5 & WordPress 4.9

= 1.1.0 =
* Fixed: Checkbox conditional logic was not working when RTV is on that input.
* Fixed: Critical bug: When inputs are getting disabled by our non visible fields logic, field values were not submitting after form submission. In multi-step
            Forms one can visualize it by losing the selection user made at prev step. Attribute added to detect if we need to validate that field or not.
* Fixed multiform handling: on submit window property reset
* Fixed multiform handling: all_validation structure modified
* Fixed multiform handling: form id handled
* Fixed after activation effect on version >= 2.0.0
* Fixed Real-time validation help button not showing on version >= 2.0.0


= 1.0.17 =
* Removed: Code responsible for plugin deactivation when Gforms doesn't exist.
* Fixed: Filtered Regex to not allow any such modifier(s) to the front end that cause validation failure or any abrupt behavior, live validation format validation now runs without any modifiers.

= 1.0.16 =
* Fix: Undefined "all_validations" issue caused by last update. 

= 1.0.15 =
* Fix: Hidden state conflict with conditional logic causing fields to be disabled when the form in modal popups. 
* Added: JS Hooks for validation and success. 
* Fixed: Resolved issue of form object turning into boolean TRUE, when we disable notification forever. 



= 1.0.14 =
* Fixed: `fid` get param conflict with gform feeds system, preventing feed page to open in backend
* Fixed: FIelds settings were showing even Live validation is off for form. 


= 1.0.13 =
* Added: Native UT for notifications.
* Added: reload after quick toggle on form edit page.
* Fixed: Address Line number 2 was marked as required.
* Fixed: Conditional fields getting validated while hidden.  

= 1.0.12 =
* Added: Easy Turn ON/OFF Real Time Validation from Gform Listing page.
* Added: Support for GForm Logging AddOn

= 1.0.11 =
* Fixed: Window smooth scroll effect on perfect position.
* Fixed: Auto validation on email field
* Fixed: Phone Number masking handling on required validation
* Added: New `How to Use` Video
* Fixed: Keeping track of the changes happening over the form while Setting for the form is turned off. So we never face issues of mis configurations.


= 1.0.10 =
* Added: Focus Out Compatibility with Broswers (Mozilla and Safari).
* Fix: Sub fields and error conflict resolved.
* Feature: Window Scrolling to the field in error.
* Added: Better Notification UI.
* Bug: WP pointer callled in specific pages.

= 1.0.9 =
* Feature: Support for multistep forms with AJAX.
* Added: Settings page under Gravity forms global settings 
* Added: Support form.
* Added: Tab for user documentaion.
* Added: Pro feature documentaion and subscription for pro.
* Added: Video tutorial using WP Pointers on form edit page. 
* Modification: Validation trigger applies on Focus out instead of keyUp/Change.
* Fix: Fields for RegEx validatations left open. 

= 1.0.8 = 
* Fix: Resolved conflict with masked inputs during validation.
* Added: Admin Notice to Enable validation for the form.

= 1.0.7 = 
* Fix: corrected the pattern given for limit
* Added: Better installation process, User experience changes
* Added: Tooltips and friendly naming of labels.


= 1.0.6 = 
* Fix: Issue in some fields stopped validating after v1.0.5 push

= 1.0.5 =
* Field support: Post & pricing fields (except image upload)
* Added: Regex pattern help thick box 
* Fix: Validation error was showing on some cases in sub fields
* Fix: Radio & checkboxes validation not working when submit triggered


= 1.0.4 =
* Field support: Address, Website & Time.
* Fix: Saving field level configuration for sub fields
* Fix: Resolved an issue, inconsistant behaviour for error prompt for sub fields


= 1.0.3 =
* Fix: Form object was printing above the form
* Fix: Conditional logic not working properly when fields are toggled.

= 1.0.2 = 
* Field support: Email, Phone, Name & Date.
* Fix:  Conditional logic suppoort.
* Fix: Ajax submission issues causing jQuery errors.

= 1.0.1 = 
* fix: Problem in initialization for dropdown fields. 
* fix: Ajax loader was showing while validation is on.

= 1.0.0 =
* Real Time Validation for Gravity Forms.



