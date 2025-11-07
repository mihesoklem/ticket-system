# Webhook Integration Guide

## Setup Complete ✓

The webhook system has been integrated into your application. Here's what was done:

### 1. Files Created/Modified:
- ✅ `/application/libraries/ZWebhook.php` - Webhook library
- ✅ `/application/config/config.php` - Added webhook configuration
- ✅ `/application/controllers/actions/admin/Support.php` - Added webhook to ticket note creation
- ✅ Backup created: `/application/controllers/actions/admin/Support.php.backup`

### 2. Current Integration:
Webhooks are currently triggered for:
- **ticket_note_added** - When a note is added to a ticket

## Configuration Steps:

### Step 1: Get your n8n Webhook URL
1. Open your n8n instance
2. Create a new workflow
3. Add a "Webhook" node (trigger)
4. Set method to POST
5. Copy the webhook URL (e.g., `https://your-n8n.com/webhook/tickets`)

### Step 2: Enable Webhooks in Config
Edit `/var/www/html/application/config/config.php`:

```php
$config['n8n_webhook_enabled'] = TRUE;  // Change FALSE to TRUE
$config['n8n_webhook_url'] = 'YOUR_N8N_WEBHOOK_URL';  // Paste your webhook URL
$config['n8n_webhook_debug'] = TRUE;  // Enable for testing (check logs)
```

### Step 3: Test the Integration
1. Add a note to any ticket in your admin panel
2. Check n8n workflow executions
3. You should receive a payload like:
```json
{
  "event": "ticket_note_added",
  "timestamp": "2025-10-29 14:50:00",
  "data": {
    "ticket_id": 123,
    "ticket_subject": "Support Request",
    "ticket_status": "open",
    "user_id": 5,
    "priority": "high",
    "note_id": 456,
    "user_id": 1
  }
}
```

## Adding More Webhook Triggers:

To add webhooks to other ticket actions, add this code after successful operations:

### Example: Close Ticket Webhook
Find the `close_ticket()` function and add after `log_ticket_activity()`:

```php
// Send webhook notification
$this->zwebhook->ticket_event('closed', $ticket, [
    'closed_by' => $user_id
]);
```

### Example: Create Ticket Webhook
Find the `create_ticket()` function and add after ticket creation:

```php
// Send webhook notification
$ticket = $this->Support_model->ticket($id);
$this->zwebhook->ticket_event('created', $ticket, [
    'created_by' => $user_id,
    'department' => $department_id
]);
```

### Example: Reopen Ticket Webhook
Find the `reopen_ticket()` function:

```php
// Send webhook notification
$this->zwebhook->ticket_event('reopened', $ticket, [
    'reopened_by' => $user_id
]);
```

### Example: Custom Event Webhook
For any custom event:

```php
$this->zwebhook->send('custom_event_name', [
    'key1' => 'value1',
    'key2' => 'value2'
]);
```

## n8n Workflow Example:

1. **Webhook Trigger** - Receives ticket events
2. **Switch Node** - Route based on event type:
   - `ticket_note_added` → Send SMS
   - `ticket_created` → Send email + SMS
   - `ticket_closed` → Send notification
3. **HTTP Request Node** - Call SMS API (Twilio, etc.)
4. **Set Variables** - Store phone numbers, messages

## Troubleshooting:

### Check Logs:
```bash
tail -f /var/www/html/application/logs/*.php
```

### Test Webhook Manually:
```bash
curl -X POST https://your-n8n.com/webhook/tickets \
  -H "Content-Type: application/json" \
  -d '{
    "event": "test",
    "timestamp": "2025-10-29 14:50:00",
    "data": {"test": true}
  }'
```

### Common Issues:
- **No webhook sent**: Check `n8n_webhook_enabled = TRUE` in config
- **Timeout errors**: Increase timeout in ZWebhook.php (currently 5 seconds)
- **n8n not receiving**: Verify webhook URL is correct and accessible

## Security Notes:
- Keep your n8n webhook URL secret
- Consider adding authentication headers in ZWebhook.php
- Use HTTPS for webhook URLs
- Monitor webhook logs for suspicious activity

## Next Steps:
1. Configure your n8n webhook URL in config.php
2. Create n8n workflow for SMS integration
3. Test by adding a ticket note
4. Add webhooks to other ticket actions as needed
