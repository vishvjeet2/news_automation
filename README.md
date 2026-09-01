# Social Media Automation API

A PHP-based social media automation project for publishing content programmatically to social media platforms.

The project currently focuses on API-based publishing and API permission testing for:

- Facebook Pages
- Pinterest

The goal is to build a reusable backend that can publish text and media content without requiring manual posting through the platform UI.

---

## 🚀 Project Overview

This project demonstrates how a PHP application can communicate with social media APIs using HTTP requests and access tokens.

The basic workflow is:

```text
PHP Application
      │
      ├── Content
      │     ├── Text
      │     └── Image / Media
      │
      ▼
Social Media API
      │
      ├── Facebook Page
      │
      └── Pinterest
```

The application is intended to become a foundation for a larger social media automation system where content can be stored, scheduled, published, and tracked.

---

# 📌 Current Features

## Facebook

The Facebook integration can:

- Connect to a Facebook Page through the Graph API.
- Publish text posts to the Page.
- Return the created post ID.
- Retrieve post information using the Graph API.
- Check fields such as:
  - `id`
  - `message`
  - `is_published`
  - `permalink_url`

Example API endpoint:

```text
POST /{PAGE_ID}/feed
```

Example response:

```json
{
  "id": "PAGE_ID_POST_ID"
}
```

The project also tested publishing posts containing images.

### Important Facebook Permission Requirement

Facebook's old `publish_actions` permission is deprecated.

Page publishing should use the current Page permissions/access-token model instead of trying to use:

```text
publish_actions
```

For Page management, the application may require permissions such as:

```text
pages_read_engagement
pages_manage_posts
pages_manage_metadata
```

The exact permissions required depend on the operation being performed and Meta's current app review/access requirements.

---

# 📌 Pinterest

Pinterest API v5 is also being integrated into the project.

The Pinterest developer application was created for:

```text
Pin creation & scheduling
```

The current development/testing token was able to read Pinterest data, but attempting to create a Pin returned:

```text
HTTP 401
```

with:

```text
Your token does not have sufficient permissions to perform this operation.
Missing: ['boards:write', 'pins:write']
```

This means the API request itself is reaching Pinterest successfully, but the token does not contain the required write scopes.

### Required Pinterest Write Scopes

For Pin creation, the access token needs the appropriate write scopes, including:

```text
pins:write
boards:write
```

The current trial/generated token shown in the developer dashboard only provided read-oriented scopes such as:

```text
pins:read
boards:read
user_accounts:read
ads:read
catalogs:read
```

Therefore, the read token cannot be used to create Pins.

---

# 🏗️ Project Structure

A simple structure for the current PHP implementation is:

```text
social-media-automation/
│
├── facebook_API_test.php
├── pintrust_API_test.php
├── README.md
│
└── assets/
    └── images/
```

> Rename files as appropriate if your local project uses different filenames.

As the project grows, a scalable structure can be introduced:

```text
social-media-automation/
│
├── app/
│   ├── Services/
│   │   ├── FacebookService.php
│   │   └── PinterestService.php
│   │
│   ├── Models/
│   │   └── SocialPost.php
│   │
│   └── Helpers/
│       └── HttpClient.php
│
├── config/
│   └── social.php
│
├── public/
│   └── index.php
│
├── storage/
│   └── logs/
│
├── tests/
│
├── .env
├── .gitignore
└── README.md
```

---

# ⚙️ Requirements

- PHP 7.4+ or PHP 8.x
- cURL extension enabled
- XAMPP / Apache or another PHP server
- Facebook Developer account
- Pinterest Developer account
- Facebook Page
- Pinterest account/board for testing

Check whether cURL is enabled:

```php
<?php

phpinfo();
```

Search the PHP information page for:

```text
cURL support => enabled
```

---

# 🔐 Authentication

Both platforms use access tokens.

## Facebook

The Facebook integration uses a Page access token.

Conceptually:

```php
$pageId = 'YOUR_PAGE_ID';
$pageToken = 'YOUR_PAGE_ACCESS_TOKEN';
```

Do not commit the real token to GitHub.

---

## Pinterest

Pinterest uses an OAuth access token.

Conceptually:

```php
$accessToken = 'YOUR_PINTEREST_ACCESS_TOKEN';
```

The App ID and App Secret are used as part of the OAuth application setup.

The App Secret must remain private.

---

# 🧪 Facebook Text Publishing

A basic Facebook Page text post looks like:

```php
<?php

$pageId = 'YOUR_PAGE_ID';
$pageToken = 'YOUR_PAGE_ACCESS_TOKEN';

$url = "https://graph.facebook.com/v26.0/{$pageId}/feed";

$data = [
    'message' => 'Hello from my automated PHP post!',
    'access_token' => $pageToken
];

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($data),
    CURLOPT_RETURNTRANSFER => true
]);

$response = curl_exec($ch);

curl_close($ch);

echo $response;
```

A successful response should contain a post ID:

```json
{
  "id": "PAGE_ID_POST_ID"
}
```

---

# 🔎 Checking a Facebook Post

After receiving the post ID, it can be queried through the Graph API.

Example:

```text
GET /POST_ID?fields=id,message,is_published,permalink_url
```

Example response:

```json
{
  "id": "PAGE_ID_POST_ID",
  "message": "Hello from my automated PHP post!",
  "is_published": true,
  "permalink_url": "https://www.facebook.com/..."
}
```

`is_published: true` confirms that Facebook considers the post published.

If a post still cannot be viewed publicly, investigate Page visibility, Page restrictions, permissions, audience settings, and the actual permalink separately from the API publishing response.

---

# 📌 Pinterest Pin Creation

Pinterest API v5 uses an endpoint for creating Pins.

The general API workflow is:

```text
1. Authenticate user
        ↓
2. Obtain OAuth access token
        ↓
3. Obtain/select a board
        ↓
4. Prepare Pin content
        ↓
5. Create Pin
```

A Pin normally requires content such as:

- Board ID
- Title
- Description
- Media
- Link

The exact request body should follow the current Pinterest API v5 documentation.

### Important

A token that can read Pins and Boards is not automatically allowed to create them.

If the API returns:

```text
Missing: ['boards:write', 'pins:write']
```

the solution is to obtain/authorize a token with those scopes. Changing the PHP request alone will not grant the missing permissions.

---

# 🔑 OAuth Permission Model

A useful way to think about API permissions is:

```text
Application
    │
    ▼
User Authorization
    │
    ▼
Requested OAuth Scopes
    │
    ▼
Access Token
    │
    ▼
API Endpoint
```

For example:

```text
pins:read
```

allows reading Pinterest data covered by that scope.

Whereas:

```text
pins:write
```

is required for operations that create or modify Pins.

The access token cannot grant itself additional permissions.

---

# 🔒 Security

## Never commit secrets

Do NOT upload:

```text
Facebook access tokens
Pinterest access tokens
Pinterest App Secret
OAuth client secrets
Database passwords
API keys
```

Use environment variables instead.

Example:

```env
FACEBOOK_PAGE_ID=your_page_id
FACEBOOK_PAGE_TOKEN=your_page_token

PINTEREST_CLIENT_ID=your_client_id
PINTEREST_CLIENT_SECRET=your_client_secret
PINTEREST_ACCESS_TOKEN=your_access_token
```

Then add `.env` to `.gitignore`:

```gitignore
.env
/vendor/
storage/logs/
```

---

# 🧠 Recommended Architecture

For a production-grade automation platform, avoid putting all API logic into individual PHP files.

Instead, create platform-specific services.

```text
SocialMediaService
        │
        ├── FacebookService
        │
        ├── PinterestService
        │
        ├── InstagramService
        │
        └── LinkedInService
```

Each service should expose a common interface such as:

```php
publishText()
publishImage()
publishVideo()
getPost()
deletePost()
```

This makes it possible to add new social platforms without rewriting the entire application.

---

# 📅 Future Scheduling Architecture

A scalable version can support scheduled publishing:

```text
Admin Dashboard
       │
       ▼
Create Social Post
       │
       ▼
Database
       │
       ▼
Scheduled Job / Cron
       │
       ▼
Social Media Service
       │
       ├── Facebook
       ├── Pinterest
       ├── Instagram
       └── Other Platforms
       │
       ▼
Publishing Result
       │
       ▼
Database / Logs
```

Example database fields:

```text
id
platform
account_id
content
media_url
scheduled_at
status
external_post_id
error_message
created_at
updated_at
```

Possible statuses:

```text
draft
scheduled
processing
published
failed
```

---

# 📝 Error Handling

Every API request should be treated as potentially unsuccessful.

Example:

```php
$response = curl_exec($ch);

if ($response === false) {
    die(curl_error($ch));
}

$data = json_decode($response, true);

if (isset($data['error'])) {
    echo 'API Error: ' . $data['error']['message'];
}
```

For production systems, errors should be logged instead of displayed directly to users.

---

# 🧪 Development Workflow

When adding a new social platform:

### Step 1 — Create developer application

Create an application on the platform's developer portal.

### Step 2 — Configure permissions

Request only the permissions required by the application.

### Step 3 — Generate OAuth credentials

Obtain:

```text
Client ID / App ID
Client Secret
Access Token
```

where applicable.

### Step 4 — Test authentication

First test a simple read endpoint.

### Step 5 — Test write access

Test creating a small test post/pin.

### Step 6 — Store the external ID

Save the platform's returned post/pin ID.

### Step 7 — Verify

Retrieve the created object and verify its status/permalink.

---

# ⚠️ Common Problems

## Facebook: `publish_actions` deprecated

Error:

```text
(#200) This endpoint is deprecated since the required permission
publish_actions is deprecated
```

Do not use the old `publish_actions` permission.

Use the current Page access and permissions model.

---

## Facebook: `pages_read_engagement` required

Error:

```text
This endpoint requires the 'pages_read_engagement' permission
```

The token being used does not have the required permission for that API operation.

Generate/use a token containing the required Page permissions.

---

## Pinterest: insufficient permissions

Error:

```text
Your token does not have sufficient permissions to perform this operation.
Missing: ['boards:write', 'pins:write']
```

The token is valid but does not have write access.

Obtain a properly authorized OAuth token with the required write scopes.

---

## Pinterest: Authentication failed

Error:

```text
Authentication failed.
```

Check:

- Access token
- Token expiration
- Authorization flow
- Authorization header
- API version
- Client/application configuration

---

# 🚀 Roadmap

- [x] Facebook Page text publishing
- [x] Facebook post ID retrieval
- [x] Facebook post status verification
- [x] Pinterest API application creation
- [x] Pinterest read API testing
- [ ] Pinterest write authorization
- [ ] Pinterest Pin creation
- [ ] Image publishing abstraction
- [ ] OAuth login flow
- [ ] Database integration
- [ ] Social account management
- [ ] Scheduled posts
- [ ] Cron/queue worker
- [ ] Publishing history
- [ ] Retry mechanism
- [ ] API error logging
- [ ] Admin dashboard
- [ ] Multi-platform publishing
- [ ] Analytics

---

# 🎯 Project Goal

The long-term goal is to transform the current API experiments into a reusable social media automation backend.

Instead of writing separate scripts for every post, the final system should allow:

```text
Create Content
      ↓
Select Platforms
      ↓
Select Accounts
      ↓
Schedule
      ↓
Queue
      ↓
Publish
      ↓
Track Result
      ↓
Store External ID
      ↓
Report Status
```

This architecture can support multiple social platforms while keeping platform-specific API logic isolated.

---

# 📄 License

This project is intended for learning, development, and experimentation with social media APIs.

Before deploying it commercially, review the applicable API platform terms, permissions, app-review requirements, and rate limits.
