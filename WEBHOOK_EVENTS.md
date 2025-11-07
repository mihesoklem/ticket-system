# Webhook Events - Complete List

All webhook events are now integrated and active! 🎉

## Webhook Configuration:
- **URL**: https://n8n.kmkentertainment.com/webhook-test/56e660da-7333-4d2c-a755-7673bf52077a
- **Status**: ✅ ENABLED
- **Debug**: ✅ ENABLED

---

## 📋 Active Webhook Events

### 1. **ticket_created**
**Triggered when**: New ticket is created
```json
{
  "event": "ticket_created",
  "timestamp": "2025-10-29 14:58:00",
  "data": {
    "ticket_id": 123,
    "ticket_subject": "Support Request",
    "ticket_status": "open",
    "user_id": 5,
    "priority": "high",
    "created_by": "admin",
    "department_id": 2,
    "type": "registered_users"
  }
}
```

### 2. **ticket_note_added**
**Triggered when**: Note is added to ticket
```json
{
  "event": "ticket_note_added",
  "data": {
    "ticket_id": 123,
    "note_id": 456,
    "user_id": 1
  }
}
```

### 3. **ticket_note_deleted**
**Triggered when**: Note is deleted from ticket
```json
{
  "event": "ticket_note_deleted",
  "data": {
    "ticket_id": 123,
    "note_id": 456
  }
}
```

### 4. **ticket_reply_added**
**Triggered when**: Admin replies to ticket
```json
{
  "event": "ticket_reply_added",
  "data": {
    "ticket_id": 123,
    "reply_id": 789,
    "replied_by": "admin"
  }
}
```

### 5. **ticket_reply_deleted**
**Triggered when**: Reply is deleted
```json
{
  "event": "ticket_reply_deleted",
  "data": {
    "ticket_id": 123,
    "reply_id": 789
  }
}
```

### 6. **ticket_assigned**
**Triggered when**: Ticket is assigned to a user
```json
{
  "event": "ticket_assigned",
  "data": {
    "ticket_id": 123,
    "assigned_to": 5,
    "assigned_by": "admin"
  }
}
```

### 7. **ticket_closed**
**Triggered when**: Ticket is closed
```json
{
  "event": "ticket_closed",
  "data": {
    "ticket_id": 123,
    "closed_by": "admin"
  }
}
```

### 8. **ticket_reopened**
**Triggered when**: Closed ticket is reopened
```json
{
  "event": "ticket_reopened",
  "data": {
    "ticket_id": 123,
    "reopened_by": "admin"
  }
}
```

### 9. **ticket_solved**
**Triggered when**: Ticket is marked as solved
```json
{
  "event": "ticket_solved",
  "data": {
    "ticket_id": 123,
    "solved_by": "admin"
  }
}
```

---

## 🔄 n8n Workflow Setup

### Recommended n8n Nodes:

1. **Webhook Trigger** (Test URL configured)
2. **Switch Node** - Route by event type:
   ```
   - ticket_created → Send welcome SMS
   - ticket_reply_added → Notify customer via SMS
   - ticket_closed → Send satisfaction survey
   - ticket_assigned → Notify assigned agent
   - ticket_solved → Send thank you message
   ```
3. **HTTP Request Node** - Call SMS API (Twilio/etc)
4. **Function Node** - Format messages

### Example SMS Templates:

**New Ticket:**
> "Hi! Your ticket #{{ticket_id}} has been created. Subject: {{ticket_subject}}. We'll respond soon!"

**Reply Added:**
> "New response on ticket #{{ticket_id}}. Check your ticket portal for details."

**Ticket Closed:**
> "Ticket #{{ticket_id}} has been closed. Reply to reopen. Thanks for contacting support!"

**Ticket Assigned:**
> "Your ticket #{{ticket_id}} has been assigned to {{agent_name}}. You'll hear from them soon."

---

## 🧪 Testing

Test each webhook by performing these actions in your admin panel:

1. ✅ Create a new ticket
2. ✅ Add a note to ticket
3. ✅ Reply to ticket
4. ✅ Assign ticket to user
5. ✅ Close ticket
6. ✅ Reopen ticket
7. ✅ Mark ticket as solved
8. ✅ Delete note
9. ✅ Delete reply

Check n8n executions after each action!

---

## 📊 Monitoring

View webhook logs:
```bash
tail -f /var/www/html/application/logs/*.php
```

Check n8n executions:
https://n8n.kmkentertainment.com/executions

---

## 🔒 Security Tips

- Keep webhook URL secret
- Add authentication in n8n (API key header)
- Monitor for suspicious activity
- Rate limit SMS sending in n8n
- Log all webhook calls

---

## 📞 SMS Integration Example

In n8n, after receiving webhook:

1. **Extract data** from webhook payload
2. **Get customer phone** from database/payload
3. **Format message** based on event type
4. **Send SMS** via Twilio/provider
5. **Log result** back to your app (optional)

Your PHP app is now ready to trigger SMS/notifications for all ticket events! 🚀
