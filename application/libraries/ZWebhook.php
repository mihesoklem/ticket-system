<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Webhook Library
 * Handles webhook notifications to external services like n8n
 */
class ZWebhook {

    protected $CI;
    
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->config('config');
    }

    /**
     * Send webhook notification
     * 
     * @param string $event Event type (e.g., 'ticket_created', 'ticket_updated')
     * @param array $data Data to send with webhook
     * @return bool Success status
     */
    public function send($event, $data = [])
    {
        // Get webhook URL from config
        $webhook_url = $this->CI->config->item('n8n_webhook_url');
        
        // Check if webhooks are enabled
        if (empty($webhook_url) || $this->CI->config->item('n8n_webhook_enabled') !== TRUE) {
            return false;
        }

        // Prepare payload
        $payload = [
            'event' => $event,
            'timestamp' => date('Y-m-d H:i:s'),
            'data' => $data
        ];

        // Send async request (non-blocking)
        return $this->send_async($webhook_url, $payload);
    }

    /**
     * Send HTTP POST request to webhook URL
     * 
     * @param string $url Webhook URL
     * @param array $payload Data to send
     * @return bool Success status
     */
    private function send_async($url, $payload)
    {
        try {
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 5 second timeout
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'User-Agent: RetailCashManager/1.0'
            ]);
            
            // Execute request
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            curl_close($ch);
            
            // Log if debug mode is enabled
            if ($this->CI->config->item('n8n_webhook_debug') === TRUE) {
                log_message('info', "Webhook sent to {$url} - HTTP {$http_code}");
            }
            
            return ($http_code >= 200 && $http_code < 300);
            
        } catch (Exception $e) {
            log_message('error', 'Webhook error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send ticket event webhook
     * 
     * @param string $action Action type (e.g., 'created', 'updated', 'note_added')
     * @param object $ticket Ticket object
     * @param array $additional_data Additional data to include
     */
    public function ticket_event($action, $ticket, $additional_data = [])
    {
        $data = array_merge([
            'ticket_id' => $ticket->id ?? null,
            'ticket_subject' => $ticket->subject ?? null,
            'ticket_message' => $ticket->message ?? null,
            'ticket_status' => $ticket->status ?? null,
            'user_id' => $ticket->user_id ?? null,
            'priority' => $ticket->priority ?? null,
            'department_id' => $ticket->department_id ?? null,
            'assigned_to' => $ticket->assigned_to ?? null,
            'email_address' => $ticket->email_address ?? null,
            'created_at' => $ticket->created_at ?? null,
        ], $additional_data);

        
        // Add ticket URL
        $base_url = $this->CI->config->item('base_url');
        if (!empty($ticket->id)) {
            if (!empty($ticket->user_id)) {
                // Registered user ticket
                $data['ticket_url'] = rtrim($base_url, '/') . "/user/support/ticket/{$ticket->id}";
            } else {
                // Guest ticket - need security key
                if (!empty($ticket->security_key)) {
                    $data['ticket_url'] = rtrim($base_url, '/') . "/ticket/guest/{$ticket->security_key}/{$ticket->id}";
                }
            }
        }
        
        // Add requester information
        $requester_info = $this->get_requester_info($ticket);
        if (!empty($requester_info)) {
            $data['requester'] = $requester_info;
        }
        
        // Add custom fields to the data
        $custom_fields = $this->get_ticket_custom_fields($ticket->id ?? null);
        if (!empty($custom_fields)) {
            $data = array_merge($data, $custom_fields);
        }
        
        return $this->send("ticket_{$action}", $data);
    }

    /**
     * Get requester information
     * 
     * @param object $ticket Ticket object
     * @return array Requester information
     */
    private function get_requester_info($ticket)
    {
        $requester = [];
        
        // For guest tickets
        if (empty($ticket->user_id) && !empty($ticket->email_address)) {
            $requester['email'] = $ticket->email_address;
            $requester['type'] = 'guest';
            return $requester;
        }
        
        // For registered users - fetch full user details
        if (!empty($ticket->user_id)) {
            $this->CI->load->model('User_model');
            $user = $this->CI->User_model->get_by_id($ticket->user_id);
            
            if (!empty($user)) {
                $requester['user_id'] = $user->id;
                $requester['username'] = $user->username ?? null;
                $requester['first_name'] = $user->first_name ?? null;
                $requester['last_name'] = $user->last_name ?? null;
                $requester['full_name'] = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                $requester['email'] = $user->email_address ?? null;
                $requester['phone'] = $user->phone ?? null;
                $requester['picture'] = $user->picture ?? null;
                $requester['type'] = 'registered';
                
                return $requester;
            }
        }
        
        return null;
    }

    /**
     * Get custom field values for a ticket
     * 
     * @param int $ticket_id Ticket ID
     * @return array Custom field values
     */
    private function get_ticket_custom_fields($ticket_id)
    {
        if (empty($ticket_id)) {
            return [];
        }
        
        $this->CI->load->model('Custom_field_model');
        $custom_fields = $this->CI->Custom_field_model->custom_fields_data($ticket_id);
        
        $result = [];
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $field) {
                // Use field name as key (like "incident_type" or "shop_name")
                $key = strtolower(str_replace(' ', '_', $field->name));
                $result[$key] = $field->value ?? null;
            }
        }
        
        return $result;
    }

}
