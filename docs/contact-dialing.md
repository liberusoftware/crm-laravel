# Contact and lead calling

Calling uses the existing team Twilio configuration: account SID, auth token and outbound number. Team owners and members with dialing permission can call; users whose contact fields are masked cannot start calls.

On a contact or lead, choose **Call contact**. Enter your agent phone or VoIP number in international format and a connection greeting. The most recently used agent number becomes the default for your next call. Twilio calls the contact, plays the greeting and bridges to your agent number.

In Contacts, use **Last called** to select **Never called**, **Not called today**, **Not called in 7 days** or **Not called in 14 days**. The filters include legacy call logs, telephony calls and dialer attempts. “Today” uses the application timezone; the other windows are rolling periods.

Select up to 500 contacts and choose **Auto dial selected**. Choose the agent number, greeting and exclusion period. The result reports how many contacts were queued or skipped. Contacts with invalid international numbers, recent calls, an existing pending call or a do-not-call outcome are skipped. Eligibility is checked again immediately before dialing. An attempted call counts toward the exclusion period even if it fails or is unanswered.

**Manage auto dialing** pauses or resumes a list. Pausing stops new calls and lets any current call finish. Separate lists sharing an agent number wait for that agent's current call to finish. Provider timeouts remain unresolved until their call status is checked; they are not retried automatically.

## Operations

Deploy the new migration with `php artisan migrate`. Keep the Laravel scheduler running (`php artisan schedule:work` locally, or the existing production scheduler). Active progressive lists dispatch on the existing one-minute schedule. Configure the public application URL so Twilio can reach the signed call-status callback; a terminal callback releases the next call.

Tests fake provider responses and do not place live calls.
