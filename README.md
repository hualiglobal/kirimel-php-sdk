# KiriMel Node.js SDK

Official Node.js SDK for KiriMel Email Marketing API.

## Installation

```bash
npm install @hualiglobal/kirimel-node-sdk
```

## Quick Start

```typescript
import { Client } from '@hualiglobal/kirimel-node-sdk';

// Initialize the client
const client = new Client({
  apiKey: 'sk_test_xxx', // Or set KIRIMEL_API_KEY env variable
  baseUrl: 'https://kirimel.com',
  timeout: 30000,
  retries: 3
});

// List campaigns
const campaigns = await client.campaigns.list({ status: 'sent', limit: 20 });

// Create a campaign
const campaign = await client.campaigns.create({
  name: 'Welcome Email',
  subject: 'Welcome to KiriMel!',
  list_id: 123,
  template_id: 456
});

// Get campaign statistics
const stats = await client.campaigns.stats(campaign.id);
```

## Authentication

The SDK supports two authentication methods:

```typescript
// Method 1: API Key (recommended)
const client = new Client({ apiKey: 'sk_test_xxx' });

// Method 2: Environment variable
// Set KIRIMEL_API_KEY=sk_test_xxx in your environment
const client = new Client();
```

## Resources

### Campaigns

```typescript
// List campaigns
const campaigns = await client.campaigns.list({ limit: 20, status: 'sent' });

// Get recent campaigns
const recent = await client.campaigns.recent();

// Get single campaign
const campaign = await client.campaigns.get(id);

// Create campaign
const campaign = await client.campaigns.create({
  name: 'Summer Sale',
  subject: '50% Off Everything!',
  list_id: 123,
  template_id: 456
});

// Update campaign
await client.campaigns.update(id, { subject: 'New Subject' });

// Delete campaign
await client.campaigns.delete(id);

// Duplicate campaign
const duplicate = await client.campaigns.duplicate(id);

// Schedule campaign
await client.campaigns.schedule(id, '2024-06-01 10:00:00');

// Pause campaign
await client.campaigns.pause(id);

// Resume campaign
await client.campaigns.resume(id);

// Get campaign statistics
const stats = await client.campaigns.stats(id);
```

### Subscribers

```typescript
// List subscribers for a list
const subscribers = await client.subscribers.list(listId, { limit: 50 });

// Get single subscriber
const subscriber = await client.subscribers.get(id);

// Add subscriber to a list
const subscriber = await client.subscribers.create(listId, {
  email: 'user@example.com',
  first_name: 'John',
  last_name: 'Doe'
});

// Update subscriber
await client.subscribers.update(id, { first_name: 'Jane' });

// Delete subscriber
await client.subscribers.delete(id);

// Unsubscribe subscriber
await client.subscribers.unsubscribe(id);

// Bulk unsubscribe
await client.subscribers.bulkUnsubscribe([id1, id2, id3]);

// Bulk delete
await client.subscribers.bulkDelete([id1, id2, id3]);

// Get subscriber activity
const activity = await client.subscribers.activity(id);

// Get subscriber statistics
const stats = await client.subscribers.stats(id);

// Toggle VIP status
await client.subscribers.toggleVip(id);

// Search subscribers
const results = await client.subscribers.search('john@example.com');

// Add tag
await client.subscribers.addTag(id, 'premium-customer');

// Remove tag
await client.subscribers.removeTag(id, 'premium-customer');

// Import subscribers
const result = await client.subscribers.import(listId, {
  subscribers: [
    { email: 'user1@example.com', first_name: 'User 1' },
    { email: 'user2@example.com', first_name: 'User 2' }
  ]
});
```

### Lists

```typescript
// List all lists
const lists = await client.lists.list();

// Get single list
const list = await client.lists.get(id);

// Create list
const list = await client.lists.create({
  name: 'Newsletter Subscribers',
  description: 'Monthly newsletter'
});

// Update list
await client.lists.update(id, { name: 'Updated Name' });

// Delete list
await client.lists.delete(id);

// Get list statistics
const stats = await client.lists.stats(id);
```

### Segments

```typescript
// List segments for a list
const segments = await client.segments.list(listId);

// Get single segment
const segment = await client.segments.get(id);

// Create segment
const segment = await client.segments.create(listId, {
  name: 'Active Subscribers',
  conditions: [
    { field: 'status', operator: 'equals', value: 'active' }
  ]
});

// Update segment
await client.segments.update(id, { name: 'Updated Name' });

// Delete segment
await client.segments.delete(id);

// Preview segment (without saving)
const preview = await client.segments.preview(listId, [
  { field: 'status', operator: 'equals', value: 'active' }
]);

// Get segment subscribers
const subscribers = await client.segments.subscribers(id);

// Refresh segment count
await client.segments.refresh(id);

// Get segment build logs
const logs = await client.segments.logs(id);

// Get segment templates
const templates = await client.segments.templates();

// Get available fields
const fields = await client.segments.fields();
```

### Templates

```typescript
// List all templates
const templates = await client.templates.list({ limit: 20 });

// Get single template
const template = await client.templates.get(id);

// Create template
const template = await client.templates.create({
  name: 'Welcome Email',
  subject: 'Welcome!',
  html_content: '<h1>Hello {{name}}</h1>',
  category: 'transactional'
});

// Update template
await client.templates.update(id, { name: 'Updated Name' });

// Delete template
await client.templates.delete(id);

// Duplicate template
const duplicate = await client.templates.duplicate(id);

// Get templates by category
const templates = await client.templates.byCategory('newsletter');

// Search templates
const results = await client.templates.search('welcome');

// Get categories
const categories = await client.templates.categories();
```

### Forms

```typescript
// List all forms
const forms = await client.forms.list();

// Get single form
const form = await client.forms.get(id);

// Create form
const form = await client.forms.create({
  name: 'Newsletter Signup',
  list_id: 123,
  fields: [
    { name: 'email', type: 'email', required: true },
    { name: 'first_name', type: 'text', required: false }
  ]
});

// Update form
await client.forms.update(id, { name: 'Updated Name' });

// Delete form
await client.forms.delete(id);

// Duplicate form
const duplicate = await client.forms.duplicate(id);

// Get form analytics
const analytics = await client.forms.analytics(id);

// Get form submissions
const submissions = await client.forms.submissions(id);

// Get embed code
const embed = await client.forms.embed(id);
```

### Conversions

```typescript
// List all conversion goals
const conversions = await client.conversions.list();

// Get single conversion goal
const goal = await client.conversions.get(id);

// Create conversion goal
const goal = await client.conversions.create({
  name: 'Purchase',
  event_type: 'purchase',
  value: 100
});

// Update conversion goal
await client.conversions.update(id, { name: 'Updated Name' });

// Delete conversion goal
await client.conversions.delete(id);

// Track a conversion
await client.conversions.track({
  goal_id: id,
  subscriber_id: 123,
  value: 50
});

// Get conversions for a goal
const conversions = await client.conversions.conversions(id);

// Get ROI report
const roi = await client.conversions.roi();

// Get funnel analysis
const funnel = await client.conversions.funnel(id);
```

### Landing Pages

```typescript
// List all landing pages
const pages = await client.landingPages.list();

// Get single landing page
const page = await client.landingPages.get(id);

// Create landing page
const page = await client.landingPages.create({
  name: 'Thank You Page',
  slug: 'thank-you',
  html_content: '<h1>Thank you!</h1>'
});

// Update landing page
await client.landingPages.update(id, { name: 'Updated Name' });

// Delete landing page
await client.landingPages.delete(id);

// Duplicate landing page
const duplicate = await client.landingPages.duplicate(id);

// Publish landing page
await client.landingPages.publish(id);

// Unpublish landing page
await client.landingPages.unpublish(id);

// Get landing page analytics
const analytics = await client.landingPages.analytics(id);

// Get templates
const templates = await client.landingPages.templates();
```

### Workflows

```typescript
// List all workflows
const workflows = await client.workflows.list();

// Get single workflow
const workflow = await client.workflows.get(id);

// Create workflow
const workflow = await client.workflows.create({
  name: 'Welcome Series',
  nodes: [...],
  edges: [...]
});

// Update workflow
await client.workflows.update(id, { name: 'Updated Name' });

// Delete workflow
await client.workflows.delete(id);

// Duplicate workflow
const duplicate = await client.workflows.duplicate(id);

// Activate workflow
await client.workflows.activate(id);

// Pause workflow
await client.workflows.pause(id);

// Validate workflow
const validation = await client.workflows.validate(id);

// Get workflow executions
const executions = await client.workflows.executions(id);

// Get workflow templates
const templates = await client.workflows.templates();

// Create workflow from template
const workflow = await client.workflows.fromTemplate(templateId);

// Get available node types
const nodeTypes = await client.workflows.nodeTypes();

// Get workflow data
const data = await client.workflows.getData(id);
```

## Error Handling

```typescript
import {
  ApiException,
  AuthenticationException,
  RateLimitException,
  ValidationException
} from '@kirimel/node-sdk';

try {
  const campaign = await client.campaigns.create(data);
} catch (error) {
  if (error instanceof AuthenticationException) {
    // Invalid API key
    console.error(`Authentication failed: ${error.message}`);
  } else if (error instanceof RateLimitException) {
    // Too many requests
    console.error(`Rate limited. Retry after: ${error.retryAfter} seconds`);
  } else if (error instanceof ValidationException) {
    // Invalid data
    console.error(`Validation errors:`, error.errors);
  } else if (error instanceof ApiException) {
    // General API error
    console.error(`API error (${error.statusCode}): ${error.message}`);
  }
}
```

## Logging

```typescript
const client = new Client({
  apiKey: 'sk_test_xxx',
  logger: {
    debug: (message, context) => console.log(`[DEBUG] ${message}`, context),
    info: (message, context) => console.info(`[INFO] ${message}`, context),
    warn: (message, context) => console.warn(`[WARN] ${message}`, context),
    error: (message, context) => console.error(`[ERROR] ${message}`, context),
  }
});
```

## Requirements

- Node.js 16.0.0 or higher
- TypeScript 4.5+ (for TypeScript projects)

## License

MIT License

## Support

- Documentation: https://docs.kirimel.com
- GitHub: https://github.com/hualiglobal/kirimel-node-sdk
- npm: https://www.npmjs.com/package/@hualiglobal/kirimel-node-sdk
- Issues: https://github.com/hualiglobal/kirimel-node-sdk/issues
