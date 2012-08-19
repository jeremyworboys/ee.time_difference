<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require PATH_THIRD.'cc_time_difference/config.php';

/**
 * Plugin Info
 *
 * @var array
 */
$plugin_info = array(
    'pi_name'           => CC_TIME_DIFFERENCE_NAME,
    'pi_version'        => CC_TIME_DIFFERENCE_VERSION,
    'pi_author'         => 'Jeremy Worboys',
    'pi_author_url'     => 'http://complexcompulsions.com',
    'pi_description'    => 'Compare the difference between two times.',
    'pi_usage'          => Cc_time_difference::usage()
);

/**
 * CC Compare Time
 *
 * @package    cc_time_difference
 * @author     Jeremy Worboys <jeremy@complexcompulsions.com>
 * @link       http://complexcompulsions.com/add-ons/cc-time-difference
 * @copyright  Copyright (c) 2012 Jeremy Worboys
 */
class Cc_time_difference {

    public $return_data = "";

    /**
     * Constructor
     *
     * @param mixed Settings array or empty string if none exist.
     */
    public function __construct()
    {
        $this->EE =& get_instance();

        // Check version number
        if (version_compare(PHP_VERSION, '5.3.0') < 0) {
            show_error('{exp:cc_time_difference} requires at least PHP version 5.3.0');
        }

        // Collect template data
        $time_1  = DateTime::createFromFormat('U', $this->EE->TMPL->fetch_param('time_1'));
        $time_2  = DateTime::createFromFormat('U', $this->EE->TMPL->fetch_param('time_2'));
        $tagdata = $this->EE->TMPL->tagdata;

        // Find difference
        $interval = $time_1->diff($time_2, true);

        // Calculate segments
        $cond['years']   = intval($interval->format('%y'));
        $cond['months']  = intval($interval->format('%m')) + 12*$cond['years'];
        $cond['days']    = intval($interval->format('%a')); // have to calculate days before weeks
        $cond['weeks']   = intval($cond['days'] / 7);
        $cond['hours']   = intval($interval->format('%h')) + 24*$cond['days'];
        $cond['minutes'] = intval($interval->format('%i')) + 60*$cond['hours'];
        $cond['seconds'] = intval($interval->format('%s')) + 60*$cond['minutes'];

        // Run conditionals
        $tagdata = $this->EE->functions->prep_conditionals($tagdata, $cond);

        // Send output
        $this->return_data = $tagdata;
    }


   /**
     * Usage
     *
     * @return string How to use this plugin.
     */
    public function usage()
    {
        ob_start(); ?>

CC Time Difference
===========================

Wrap your conditionals in `{exp:cc_time_difference}` tags.

**Note** This plugin requires PHP version > 5.3.0 and will display an error
otherwise.


Parameters
===========================

The tag has the following parameters:

- `time_1` - The first time to calculate difference with. (Required)
- `time_2` - The second time to calculate difference with. (Required)

**Note** The order the times are passed does not matter.


Conditional Variables
===========================

CC Time Difference creates the following conditional parameters to use:

- `years` - The total number of years between the two dates.
- `months` - The total number of months between the two dates.
- `days` - The total number of days between the two dates.
- `weeks` - The total number of weeks between the two dates.
- `hours` - The total number of hours between the two dates.
- `minutes` - The total number of minutes between the two dates.
- `seconds` - The total number of seconds between the two dates.


Example
===========================

```
{exp:channel:entries channel="compare_time"}
    {exp:cc_time_difference time_1="{entry_date}" time_2="{current_time}"}
        {if weeks < 1}
            Posted less than one week ago.
        {if:else}
            Posted more than one week ago.
        {/if}
    {/exp:cc_time_difference}
{/exp:channel:entries}
```


Changelog
===========================

Version 1.0.0
---------------------------

- Initial release

    <?php
        $buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;
    }
}
// END CLASS