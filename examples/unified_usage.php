<?php

/**
 * KiriMel Unified SDK Usage Example
 *
 * Demonstrates using both Email API and Loyalty API in a single workflow
 */

require_once __DIR__ . '/../vendor/autoload.php';

use KiriMel\Client;

// Initialize the SDK with credentials
$client = new Client([
    // Email API credentials (required)
    'api_key' => getenv('KIRIMEL_API_KEY') ?: 'your_email_api_key_here',

    // Loyalty API credentials (optional - only if using loyalty features)
    'client_key' => getenv('KIRIMEL_LOYALTY_CLIENT_KEY') ?: 'your_client_key_here',
    'client_secret' => getenv('KIRIMEL_LOYALTY_CLIENT_SECRET') ?: 'your_client_secret_here',
]);

// ============================================================
// EMAIL API EXAMPLES
// ============================================================

echo "=== Email API Examples ===\n\n";

// List all mailing lists
try {
    $lists = $client->lists()->list();
    echo "Mailing Lists: " . json_encode($lists, JSON_PRETTY_PRINT) . "\n\n";
} catch (Exception $e) {
    echo "Error fetching lists: " . $e->getMessage() . "\n\n";
}

// List campaigns
try {
    $campaigns = $client->campaigns()->list();
    echo "Campaigns: " . json_encode($campaigns, JSON_PRETTY_PRINT) . "\n\n";
} catch (Exception $e) {
    echo "Error fetching campaigns: " . $e->getMessage() . "\n\n";
}

// Add a subscriber
try {
    $subscriber = $client->subscribers()->create([
        'email' => 'customer@example.com',
        'list_id' => 1, // Replace with actual list ID
        'fields' => [
            'name' => 'John Doe',
            'phone' => '+60123456789'
        ]
    ]);
    echo "Subscriber added: " . json_encode($subscriber, JSON_PRETTY_PRINT) . "\n\n";
} catch (Exception $e) {
    echo "Error adding subscriber: " . $e->getMessage() . "\n\n";
}

// ============================================================
// LOYALTY API EXAMPLES
// ============================================================

echo "=== Loyalty API Examples ===\n\n";

// Register a new loyalty customer
try {
    $customer = $client->loyaltyCustomers()->register([
        'phone' => '+60123456789',
        'name' => 'John Doe',
        'email' => 'customer@example.com'
    ]);
    echo "Customer registered: " . json_encode($customer, JSON_PRETTY_PRINT) . "\n\n";

    $customerId = $customer['data']['id'] ?? null;

    if ($customerId) {
        // Award points to customer
        $pointsResult = $client->loyaltyPoints()->earn([
            'customer_id' => $customerId,
            'points' => 100,
            'reference' => 'WELCOME_BONUS_' . time(),
            'description' => 'Welcome bonus for new customer'
        ]);
        echo "Points awarded: " . json_encode($pointsResult, JSON_PRETTY_PRINT) . "\n\n";

        // Check wallet balance
        $balance = $client->loyaltyWallet()->balance([
            'customer_id' => $customerId
        ]);
        echo "Wallet balance: " . json_encode($balance, JSON_PRETTY_PRINT) . "\n\n";

        // Preview redemption
        $preview = $client->loyaltyPoints()->previewRedeem([
            'customer_id' => $customerId,
            'points_to_redeem' => 50
        ]);
        echo "Redemption preview: " . json_encode($preview, JSON_PRETTY_PRINT) . "\n\n";
    }
} catch (Exception $e) {
    echo "Loyalty API error: " . $e->getMessage() . "\n\n";
}

// ============================================================
// COMBINED WORKFLOW: Onboard New Customer
// ============================================================

echo "=== Combined Workflow: Onboard New Customer ===\n\n";

function onboardNewCustomer(Client $client, array $data): array
{
    $result = [
        'email_list' => null,
        'loyalty_customer' => null
    ];

    // 1. Add to email list
    if (isset($data['list_id'])) {
        try {
            $result['email_list'] = $client->subscribers()->create([
                'email' => $data['email'],
                'list_id' => $data['list_id'],
                'fields' => [
                    'name' => $data['name'],
                    'phone' => $data['phone']
                ]
            ]);
            echo "✓ Added to email list\n";
        } catch (Exception $e) {
            echo "✗ Email list error: " . $e->getMessage() . "\n";
        }
    }

    // 2. Register as loyalty customer
    try {
        $result['loyalty_customer'] = $client->loyaltyCustomers()->register([
            'phone' => $data['phone'],
            'name' => $data['name'],
            'email' => $data['email']
        ]);
        echo "✓ Registered as loyalty customer\n";

        // 3. Award welcome points
        if (isset($result['loyalty_customer']['data']['id'])) {
            $customerId = $result['loyalty_customer']['data']['id'];
            $client->loyaltyPoints()->earn([
                'customer_id' => $customerId,
                'points' => 100,
                'reference' => 'WELCOME_' . time(),
                'description' => 'Welcome bonus'
            ]);
            echo "✓ Awarded 100 welcome points\n";
        }
    } catch (Exception $e) {
        echo "✗ Loyalty error: " . $e->getMessage() . "\n";
    }

    return $result;
}

// Example: Onboard a new customer
$onboardResult = onboardNewCustomer($client, [
    'email' => 'newcustomer@example.com',
    'phone' => '+60123456789',
    'name' => 'New Customer',
    'list_id' => 1 // Replace with actual list ID
]);

echo "\nOnboarding result: " . json_encode($onboardResult, JSON_PRETTY_PRINT) . "\n";
